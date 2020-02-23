import { TestBed } from '@angular/core/testing';

import { AlertService } from './alert.service';
import { RouterTestingModule } from '@angular/router/testing';

describe('AlertService', () => {
  let service: AlertService;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [ RouterTestingModule]
    });
    service = TestBed.inject(AlertService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
