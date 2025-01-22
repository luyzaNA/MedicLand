import { Injectable } from '@angular/core';
import { environment } from '../shared/env';
import { Observable, catchError, throwError } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { ErrorService } from './error.service';
import { Disease } from '../shared/DiseaseI';

@Injectable({
  providedIn: 'root'
})
export class DiseaseService {
  private apiUrl = `${environment.apiUrl}/api/disease`;


  constructor(
    private http: HttpClient,
    private errorService: ErrorService
  ) {}

  getDisease(name: string): Observable<Disease> {
    return this.http.get<Disease>(`${this.apiUrl}/${name}`).pipe(
      catchError(error => {
        const errorMessage = error.error?.error || 'An unexpected error occurred';
        this.errorService.errorSubject.next(errorMessage);
        this.errorService.showError(errorMessage);
        return throwError(error);
      })
    );
}

}