import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import { Observable, catchError, map, throwError } from 'rxjs';
import { Specialization } from '../shared/SpecializationI'; 
import {environment} from "../shared/env";
import { ErrorService } from './error.service';

@Injectable({
  providedIn: 'root'
})
export class SpecializationService {


  constructor(private http: HttpClient,    private errorService: ErrorService 
  ) {
  }

  getAllSpecializations(): Observable<Specialization[]> {
    const apiUrl = `${environment.apiUrl}/specializations`; 
    return this.http.get<Specialization[]>(apiUrl);
  }

  addSpecialization(name: string): Observable<Specialization> {
    const apiUrl = `${environment.apiUrl}/api/specialization`; 
    const body = { name };

    return this.http.post<Specialization>(apiUrl, body).pipe(
      map(response => {
        return response;
      }),
      catchError(error => {
        console.log(error.error.error)
        this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred');
        this.errorService.showError(error.error.error ? error.error.error : 'An unexpected error occurred');
        return throwError(error);  
      })
    );
  }

 

  deleteSpecialization(name: string): Observable<void> {
    const apiUrl = `${environment.apiUrl}/api/specialization/${name}`;
    return this.http.delete<void>(apiUrl);
  }
  
}
