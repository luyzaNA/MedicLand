import { ApplicationConfig } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import { HTTP_INTERCEPTORS, provideHttpClient, withInterceptors } from '@angular/common/http';
import { CustomInterceptor } from '../services/custom.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [provideRouter(routes), 
              provideAnimationsAsync(),
              provideAnimationsAsync(),
              provideHttpClient(withInterceptors([CustomInterceptor])), provideAnimationsAsync()
    ]
};