import { Injectable } from '@angular/core';
import { HttpParams, HttpResponse, HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { User } from 'src/app/shared/models/user.model';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  url = `${environment.apiBaseUrl}users`;

  constructor(private http: HttpClient) { }


  getUsers(filter: string, sortFiled: string, sortDirection, pageIndex, perPage): Observable<HttpResponse<object>> {
    console.log([sortFiled, sortDirection, pageIndex]);

    let params =  new HttpParams()
                      .set('per-page', `${perPage}`)
                      .set('page', `${pageIndex}`)
                      .set('search', filter);

    if(sortDirection == 'asc'){
      params = params.append('sort', `-${sortFiled}`);
    }else if(sortDirection == 'desc'){
      params = params.append('sort', `${sortFiled}`);
    }

    return this.http.get(this.url, { observe: 'response' as 'response', params: params});
  }

  getAvailableRoles(){
    return this.http.get(`${this.url}/available-roles`);
  }

  delete(id){
    return this.http.delete(`${this.url}/${id}`);
  }

  create(user: User){
    return this.http.post(this.url, user);
  }

  update(user: User){
    return this.http.put(`${this.url}/${user.id}`, user);
  }
}
