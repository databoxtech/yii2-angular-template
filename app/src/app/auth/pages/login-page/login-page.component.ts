import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router, ActivatedRoute } from '@angular/router';
import { first } from 'rxjs/operators';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({
  selector: 'app-login-page',
  templateUrl: './login-page.component.html',
  styleUrls: ['./login-page.component.scss']
})
export class LoginPageComponent implements OnInit {

  loginForm: FormGroup;
  returnUrl = '/';

  constructor(private atuhService: AuthService, private router: Router, private route: ActivatedRoute, private formBuilder: FormBuilder){
    if (this.atuhService.currentUserValue) {
      this.router.navigate(['/']);
    }
  }

  ngOnInit(): void {
    this.loginForm = this.formBuilder.group({
      email: ['', Validators.required],
      password: ['', Validators.required]
    });

    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
  }

  get f() { return this.loginForm.controls; }

  onSubmit(){
    if (this.loginForm.invalid) {
      return;
    }

    this.atuhService.login(this.f.email.value, this.f.password.value)
    .pipe(first())
    .subscribe(
        data => {
            this.router.navigate([this.returnUrl]);
        },
        error => {
          console.log('Error');
            // this.alertService.error(error);
            // this.loading = false;
        });
  }

  isEmpty(str){
    return !(str && str!='');
  }
}
