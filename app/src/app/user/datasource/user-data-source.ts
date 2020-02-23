import {CollectionViewer, DataSource} from "@angular/cdk/collections";
import { BehaviorSubject, Observable, of } from 'rxjs';
import { User } from 'src/app/shared/models/user.model';
import { catchError, finalize, map } from 'rxjs/operators';
import { NgxSpinnerService } from 'ngx-spinner';
import { UserService } from '../services/user.service';

export class UserDataSource implements DataSource<User> {

    private usersSubject = new BehaviorSubject<User[]>([]);
    private loadingSubject = new BehaviorSubject<boolean>(false);

    public totalUsers = 0;

    constructor(private userService: UserService, private spinner: NgxSpinnerService) {
        this.loadingSubject.subscribe(status => {
            if(status){
                this.spinner.show();
            }else{
                this.spinner.hide();
            }
        })
    }

    connect(collectionViewer: CollectionViewer): Observable<User[]> {
        return this.usersSubject.asObservable();
    }

    disconnect(collectionViewer: CollectionViewer): void {
        this.usersSubject.complete();
        this.loadingSubject.complete();
    }

    loadUsers(filter: string, sortField: string, sortDirection: string, pageIndex: number, pageSize: number) {

        console.log('Load Users', [
            filter, sortField, sortDirection, pageIndex, pageSize
        ]);
        this.loadingSubject.next(true);

        this.userService.getUsers(filter, sortField, sortDirection, pageIndex, pageSize).pipe(
            map(data => {
                console.log(data.headers.get('X-Pagination-Total-Count'));
                console.log(data.headers);
                this.totalUsers = Number(data.headers.get('X-Pagination-Total-Count'));
                //console.log('resultsLength', this.resultsLength);
                return data.body as User[];
            }),
            catchError(() => of([])),
            finalize(() => this.loadingSubject.next(false)),
        )
        .subscribe(Users => this.usersSubject.next(Users));
    }
}