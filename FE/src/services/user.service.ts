import { Injectable } from '@angular/core';
import {environment} from "../shared/env";
import { HttpClient } from '@angular/common/http';
import { DoctorDetails } from '../shared/DoctorDetailsI';
import { Observable, catchError, throwError } from 'rxjs';
import { User, UserI } from '../shared/UserI';
import { ErrorService } from './error.service';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  private apiUrl = `${environment.apiUrl}`;

  constructor(private http: HttpClient, private errorService: ErrorService) {}

  getDoctorBySpecialization(specializationName: string): Observable<DoctorDetails[]> {
    const url = `${this.apiUrl}/users/specialization/${specializationName}`;
    return this.http.get<DoctorDetails[]>(url);
  }


  getDoctors(): Observable<UserI[]> {
    const url = `${this.apiUrl}/api/doctors`;
    return this.http.get<UserI[]>(url);
  }

  getUsers(): Observable<UserI[]> {
    const apiUrl = `${environment.apiUrl}/api/users`;
    return this.http.get<UserI[]>(apiUrl);
  }

  updateUser(email: string, firstName: string, role: Array<string>): Observable<any> {
    const url = `${this.apiUrl}/api/users/${email}`;

    const body = { firstName, role}; 

    return this.http.put<any>(url, body); 
  }

  deleteUser(email: string): Observable<any> {
    const url = `${this.apiUrl}/api/users/${email}`;

    return this.http.delete<any>(url).pipe(
      catchError(error => {
        console.error('Error occurred:', error);
        this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred');

        return throwError(error);  
      })
    );
  }


}
