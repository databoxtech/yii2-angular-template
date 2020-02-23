import { TestBed, inject } from '@angular/core/testing';

import { AuthService } from './auth.service';

import {
  HttpClientTestingModule,
  HttpTestingController
} from '@angular/common/http/testing';
import { PassThrough } from 'stream';
import { HttpEvent, HttpEventType } from '@angular/common/http';


const mockUser = { email: 'admin@crm.lk', password: '1234', displayName: 'Prabath P', phone: '0775831176', jwt: 'sdzcasidinawdaasdasdas' };


describe('AuthService', () => {
  let service: AuthService;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [
        HttpClientTestingModule,
      ],
      providers: [ AuthService ]
    });
    service = TestBed.inject(AuthService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should login',
    inject(
      [HttpTestingController, AuthService],
      (
        httpMock: HttpTestingController,
        authService: AuthService
      ) => {
        authService.login(mockUser.email, mockUser.password).subscribe((event: HttpEvent<any>) => {
          switch (event.type) {
            case HttpEventType.Response:
              expect(event.body).toEqual(mockUser);
          }
        });
        const mockReq = httpMock.expectOne(authService.authUrl);
        expect(mockReq.cancelled).toBeFalsy();
        expect(mockReq.request.responseType).toEqual('json');
        mockReq.flush(mockUser);
        httpMock.verify();
      }
    )
  );
});
