import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserListPageComponent } from './pages/user-list-page/user-list-page.component';
import { UserAddComponent } from './components/user-add/user-add.component';
import { UserRoutingModule } from './user-routing.module';
import { MaterialModule } from '../material.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SweetAlert2Module } from '@sweetalert2/ngx-sweetalert2';
import { MatPasswordStrengthModule } from '@angular-material-extensions/password-strength';



@NgModule({
  declarations: [UserListPageComponent, UserAddComponent],
  imports: [
    CommonModule,
    UserRoutingModule,
    MaterialModule,
    FormsModule,
    ReactiveFormsModule,
    SweetAlert2Module,
    MatPasswordStrengthModule
  ],
  entryComponents: [UserAddComponent]
})
export class UserModule { }
