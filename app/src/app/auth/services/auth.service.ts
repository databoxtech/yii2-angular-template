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

  public authUrl = `${environment.apiBaseUrl}auth/login`;

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

  login(email, password) {
    console.log(`Login Request ${email} => ${password}`);
    return this.http.post<any>(this.authUrl, { email, password })
        .pipe(map(resp => {
          if(resp && resp.jwt){
            localStorage.setItem('__yat-user', JSON.stringify(resp));
            this.currentUserSubject.next(resp);
            this.permissions = resp.permissions;
            console.log(this.permissions);
            return resp;
          }
          return null;
        }));
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
