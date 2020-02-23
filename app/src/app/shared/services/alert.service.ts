import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Router, NavigationStart } from '@angular/router';

export class Alert{
  title: string = '';
  text: string = ''
}

@Injectable({
  providedIn: 'root'
})
export class AlertService {

  public subject = new Subject<Alert>();

  constructor(private router: Router) {
    this.router.events.subscribe(event => {
      if (event instanceof NavigationStart) {
        this.clear();
      }
    });
  }

  notify(title, message){
    this.subject.next({title: title, text: message});
  }

  alert(alert: Alert) {
    this.subject.next(alert);
  }

  clear(){
    this.subject.next(null);
  }
}
