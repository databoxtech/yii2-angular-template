import { Component, OnInit, ViewChild, AfterViewInit, ElementRef } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';

import {merge, Observable, of as observableOf, fromEvent} from 'rxjs';
import {catchError, map, startWith, switchMap, debounceTime, distinctUntilChanged, tap} from 'rxjs/operators';
import { NgxSpinnerService } from 'ngx-spinner';
import { SwalProvider } from '@sweetalert2/ngx-sweetalert2/lib/sweetalert2-loader.service';
import { AuthService } from 'src/app/auth/services/auth.service';
import { MatDialog } from '@angular/material/dialog';
import { UserDataSource } from '../../datasource/user-data-source';
import { UserService } from '../../services/user.service';
import { UserAddComponent } from '../../components/user-add/user-add.component';

@Component({
  selector: 'app-user-list-page',
  templateUrl: './user-list-page.component.html',
  styleUrls: ['./user-list-page.component.scss']
})
export class UserListPageComponent implements OnInit {

  PER_PAGE = Math.floor((window.innerHeight - 105)/ 48);

  isLoadingResults = true;
  displayedColumns  :  string[] = ['id', 'name', 'email', 'phone1', 'actions'];
  dataSource: UserDataSource;
  resultsLength = 0;

  filterValue = '';

  canCreate = false;
  canDelete = false;


  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort, {static: true}) sort: MatSort;
  @ViewChild('filterInput') input: ElementRef;


  constructor(private api: UserService,
    private auth: AuthService,
    private spinner: NgxSpinnerService,
    public dialog: MatDialog) { }

  ngOnInit() {
    this.canCreate = this.auth.can('user:create');
    this.canDelete = this.auth.can('user:delete');
    this.dataSource = new UserDataSource(this.api, this.spinner);
    this.dataSource.loadUsers('', 'id', 'asc', 0, this.PER_PAGE);
  }

  ngAfterViewInit() {

    fromEvent(this.input.nativeElement,'keyup')
        .pipe(
            debounceTime(150),
            distinctUntilChanged(),
            tap(() => {
                this.paginator.pageIndex = 0;
                this.loadUsers();
            })
        )
        .subscribe();

        // reset the paginator after sorting
      this.sort.sortChange.subscribe(() => this.paginator.pageIndex = 0);

      // on sort or paginate events, load a new page
      merge(this.sort.sortChange, this.paginator.page)
      .pipe(
          tap(() => this.loadUsers())
      )
      .subscribe();
  }

  clearFilter(){
    this.filterValue = '';
    this.loadUsers();
  }

  loadUsers(){
    this.dataSource.loadUsers(
      this.filterValue,
      this.sort.active,
      this.sort.direction,
      this.paginator.pageIndex,
      this.PER_PAGE
    );
  }
  
  delete(user){
    console.log(user);
    this.api.delete(user.id).subscribe(
      resp =>{
        console.log('Sucess', resp);
        this.loadUsers();
      },
      error => {
        console.log('Error', error);
      })
  }

  addOrEditDialog(user=null): void{
    const dialogRef = this.dialog.open(UserAddComponent, {
      width: '500px',
      data: {modal: true, user: user},
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      this.loadUsers();
    });
  }
}
