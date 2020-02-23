import { TestBed } from '@angular/core/testing';

import { JwtInterceptor } from './jwt.interceptor';
import { HttpClientModule } from '@angular/common/http';

describe('JwtInterceptor', () => {
  beforeEach(() => TestBed.configureTestingModule({
    providers: [
      JwtInterceptor
    ],
    imports: [
      HttpClientModule
    ]
  }));

  it('should be created', () => {
    const interceptor: JwtInterceptor = TestBed.inject(JwtInterceptor);
    expect(interceptor).toBeTruthy();
  });
});
