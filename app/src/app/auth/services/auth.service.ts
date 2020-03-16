import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { User } from 'src/app/shared/models/user.model';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { environment } from 'src/environments/environment';
import { isArray } from 'util';


@Injectable({
  providedIn: 'root'
})
export class AuthService {

  public isLoggedIn: boolean = false;
  private currentUserSubject: BehaviorSubject<User>;
  public currentUser: Observable<User>;

  private permissions;

  public authUrl = `${environment.apiBaseUrl}auth/token`;

  constructor(private http: HttpClient) {
      this.currentUserSubject = new BehaviorSubject<User>(JSON.parse(localStorage.getItem('__yat-user')));
      this.currentUser = this.currentUserSubject.asObservable();
      this.currentUser.subscribe(user => {
        if(user && user.jwt){
          this.isLoggedIn = true;
        }else{
          this.isLoggedIn = false;
        }
      })
  }

  public get currentUserValue(): User {
      return this.currentUserSubject.value;
  }

  public getJwtToken(): string{
    return this.currentUserValue ? this.currentUserValue.jwt : null;
  }

  login(email, password) {
    return this.http.post<any>(this.authUrl, {
      "email": email,
      "password" : password,
      "grant_type": "password"
    }).pipe(map(resp => {
      return this.processAuthResponse(resp);
    }));
  }

  refreshJwt(){
    return this.http.post<any>(this.authUrl, {
      "grant_type": "refresh_token",
      "refresh_token": this.currentUserValue.refresh_token
    }).pipe(map(resp => {
      return this.processAuthResponse(resp);
    }));
  }

  private processAuthResponse(response){
    if(response && response.jwt){
      const user = (new User()).deserialize(response.user);
      user.jwt = response.jwt;
      user.refresh_token = response.refresh_token;
      user.permissions = response.permissions;
      localStorage.setItem('__yat-user', JSON.stringify(user));

      this.permissions = user.permissions;
      this.currentUserSubject.next(user);
      return user;
    }
    return null;
  }

  logout() {
    localStorage.removeItem('__yat-user');
    this.currentUserSubject.next(null);
  }

  can(permission){
    const can = this.currentUserValue && isArray(this.currentUserValue.permissions) && (this.currentUserValue.permissions.includes(permission));
    console.log(`Can user, ${permission} => ${can}`);
    return can;
  }
}
