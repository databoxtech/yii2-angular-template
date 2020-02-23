import { Component, OnInit, Inject } from '@angular/core';
import { FormControl, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AlertService } from 'src/app/shared/services/alert.service';
import { Router } from '@angular/router';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { Observable } from 'rxjs';
import { User } from 'src/app/shared/models/user.model';
import { UserService } from '../../services/user.service';

export interface DialogData {
  modal: boolean;
  user: User;
}

@Component({
  selector: 'app-user-add',
  templateUrl: './user-add.component.html',
  styleUrls: ['./user-add.component.scss']
})
export class UserAddComponent implements OnInit {

  userForm: FormGroup;
  user: User = null;
  showStrengthInfo: boolean = false;
  roles: Observable<any>;
  passwordWithValidation;


  constructor(private api: UserService,
    private formBuilder: FormBuilder,
    private router: Router,
    private alert: AlertService,
    public dialogRef: MatDialogRef<UserAddComponent>,
    @Inject(MAT_DIALOG_DATA) public data: DialogData) {

      if(data.user){
        this.user = data.user;
      }else{
        this.user = null;
      }
      this.roles = this.api.getAvailableRoles();
      this.userForm = this.createForm();
  }

  ngOnInit(): void {

  }


  createForm(){
    let psswdValidator = [];
    if(this.user == null){
      psswdValidator = [Validators.required];
    }

    return this.formBuilder.group({
      displayName: new FormControl(this.user ? this.user.displayName : '', Validators.required),
      phone: new FormControl(this.user ? this.user.phone : '', Validators.required),
      email: new FormControl(this.user ? this.user.email : '', Validators.required),
      password: new FormControl('', psswdValidator),
      sendPassword: new FormControl(false),
      role: new FormControl(this.user ? this.user.role : '', Validators.required)
    });
  }

  onStrengthChanged(event){
    console.log(event);
    this.showStrengthInfo = true;
  }


  onSubmit(): void{
    console.log(this.userForm.value);
    const usr = this.userForm.value as User;
    let obs: Observable<Object>;
    
    let action = 'created';
    if(this.user){
      usr.id = this.user.id;
      obs = this.api.update(usr);
      action = 'updated';
    }else{
      obs = this.api.create(usr);
    }
    obs.subscribe(resp => {
      console.log('Success');
      this.alert.notify('Created', `User "${usr.displayName}" ${action} succesfully.`);
      this.dialogRef.close(true);
    }, error => {
      this.alert.notify('Error', error);
      console.log('Error', error);
    })
  }

  reset(){
    if(this.user){
      this.userForm.reset(this.user);
    }else{
      this.userForm.reset();
    }
  }
}
