import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuard } from '../auth/guards/auth.guard';
import { UserListPageComponent } from './pages/user-list-page/user-list-page.component';


const routes: Routes = [
  { path: '**', component: UserListPageComponent, canActivate: [AuthGuard], data: {permission: 'user:view'}}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserRoutingModule { }
