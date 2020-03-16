import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError, BehaviorSubject } from 'rxjs';
import { AuthService } from '../services/auth.service';
import { catchError, switchMap, filter, take } from 'rxjs/operators';
import { User } from 'src/app/shared/models/user.model';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {

  private isRefreshing = false;
  private refreshTokenSubject: BehaviorSubject<any> = new BehaviorSubject<any>(null);

  constructor(private auth: AuthService) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {

    const jwt = this.auth.getJwtToken();

    if (jwt) {
      request = this.addToken(request, jwt);
    }

    return next.handle(request).pipe(catchError(error => {
      console.log('handle', error);
      if (error instanceof HttpErrorResponse && error.status === 401) {
          return this.handle401Error(request, next);
      } else {
        return throwError(error);
      }
    }));

  }

  private handle401Error(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (!this.isRefreshing) {
      this.isRefreshing = true;
      this.refreshTokenSubject.next(null);
  
      return this.auth.refreshJwt().pipe(
        switchMap((user: User) => {
          this.isRefreshing = false;
          this.refreshTokenSubject.next(user.jwt);
          return next.handle(this.addToken(request, user.jwt));
        }),
        catchError((error) => {
          //logout upon error
          this.auth.logout();
          location.reload(true);          
          return throwError(error);
        }));
  
    } else {
      return this.refreshTokenSubject.pipe(
        filter(user => user != null),
        take(1),
        switchMap(user => {
          return next.handle(this.addToken(request, user.jwt));
        }));
    }
  }

  private addToken(request: HttpRequest<any>, token: string) {
    return request.clone({
      setHeaders: {
        'Authorization': `Bearer ${token}`
      }
    });
  }


}
