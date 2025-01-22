import { Injectable } from "@angular/core";
import { environment } from "../shared/env";
import { ErrorService } from "./error.service";
import { HttpClient } from "@angular/common/http";
import { ConsultationI } from "../shared/ConsultationI";
import { Observable, catchError, throwError } from "rxjs";
import { Disease } from "../shared/DiseaseI";

@Injectable({
  providedIn: 'root'
})
export class ConsultationService {
  private apiUrl = `${environment.apiUrl}/api/consultation`;

  constructor(
    private http: HttpClient,
    private errorService: ErrorService
  ) {}

  
  addConsultation(consultation: ConsultationI): Observable<ConsultationI> {
    return this.http.post<ConsultationI>(this.apiUrl, consultation).pipe(
      catchError(error => {
        const errorMessage = error.error?.error || 'An unexpected error occurred';
        this.errorService.errorSubject.next(errorMessage);
        this.errorService.showError(errorMessage);
        return throwError(error);
      })
    );
  }

  updateConsultation(id: number, medication: string |undefined, diseases: Disease[], symptoms: string | undefined): Observable<ConsultationI> {
    const consultationData = { medication, diseases, symptoms };

    return this.http.put<ConsultationI>(`${this.apiUrl}/${id}`, consultationData).pipe(
      catchError(error => {
        const errorMessage = error.error?.error || 'An unexpected error occurred';
        this.errorService.errorSubject.next(errorMessage);
        this.errorService.showError(errorMessage);
        return throwError(error);
      })
    );
}


  getConsultation(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`).pipe(
      catchError(error => {
        const errorMessage = error.error?.error || 'An unexpected error occurred';
        this.errorService.errorSubject.next(errorMessage);
        this.errorService.showError(errorMessage);
        return throwError(error);
      })
    );
  }

  deleteConsultation(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`).pipe(
      catchError(error => {
        console.error('Error deleting consultation:', error);
        return throwError(error);
      })
    ); 
}

  getConsultations(): Observable<any[]> {
    const url = `${this.apiUrl}`; 
    return this.http.get<any[]>(url);
  }

  getConsultationByPatient(patientCnp: string): Observable<any[]> {
    const url = `${this.apiUrl}s/patient/${patientCnp}`;
    return this.http.get<any[]>(url).pipe(
      catchError(error => {
        const errorMessage = error.error?.error || 'An unexpected error occurred';
        this.errorService.errorSubject.next(errorMessage);
        this.errorService.showError(errorMessage);
        return throwError(error);
      })
    );
  }
}