import { Component, ViewChild } from '@angular/core';
import { AuthService } from './auth/services/auth.service';
import { Router } from '@angular/router';
import { SwalComponent } from '@sweetalert2/ngx-sweetalert2';
import { AlertService, Alert } from './shared/services/alert.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {

  drawer;
  
  @ViewChild('alertSwal') private alertSwal: SwalComponent;
  alert = {
    title: '',
    text: ''
  };

  constructor(public auth: AuthService, private router: Router, private alertSrv: AlertService){
    this.alertSrv.subject.subscribe(alert => {
      if(alert === null){
        this.alertSwal.dismiss();
      }else{
        this.showAlert(alert);
      }
    });
  }

  showAlert(alert: Alert){
    this.alertSwal.title = alert.title;
    this.alertSwal.text = alert.text;
    this.alertSwal.update();
    this.alertSwal.fire();
  }

  logout(){
    this.auth.logout();
    this.router.navigate(['/login']);
  }
}
