import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, catchError, map, throwError } from 'rxjs';
import { environment } from '../shared/env';
import { ErrorService } from './error.service';
import { BloodGroup, Patient, PatientI, PatientIAdd, RhFactor } from '../shared/PatientI';
import { Disease } from '../shared/DiseaseI';

@Injectable({
  providedIn: 'root'
})
export class PatientService {

  private apiUrl = `${environment.apiUrl}/api/patients`;

  constructor(private http: HttpClient, private  errorService: ErrorService) { }



    addPatient(cnp: string, firstName: string, lastName: string, 
      locality: string, address: string, phone: string, 
      rh: RhFactor, allergies: string, occupation: string ,
      bloodGroup: BloodGroup, weight: number, height: number,
    email?: string,  diseases?: Disease[]): Observable<PatientI> {

      const patient = {cnp, firstName, lastName, locality, address,
                        phone, rh, allergies, occupation, bloodGroup, weight, height, email,diseases
     };

      return this.http.post<any>(this.apiUrl, patient).pipe( map(response => {
                    return response; 
      }),
      catchError(error => {
            this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred');
            return throwError(error);  
              })
      );
  }

  updatePatient(cnp: string, data: Patient): Observable<PatientI> {
    const url = `${this.apiUrl}/${cnp}`; 
  
    return this.http.put<PatientI>(url, data).pipe(
      map(response => {
        return response; 
      }),
      catchError(error => {
        this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred');
        return throwError(error); 
      })
    );
  }
  

  getPatientByCnp(cnp: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${cnp}`).pipe(
      map(response => {
        if (response.medicalHistory) {
          response.diseases = response.medicalHistory.map((history: any) => ({
            name: history.name,
            description: history.description,
            category: history.category
          }));
        }
        return response; 
      }),
      catchError(error => {
        this.errorService.errorSubject.next(error.error.error ? error.error.error : 'No patient found with the given CNP.');

        return throwError(error); 
      })
    );
  
  }

  deletePatient(cnp: string): Observable<any> {
    const url = `${this.apiUrl}/${cnp}`;
    return this.http.delete(url).pipe(
        map(response => response),
        catchError(error => {
            this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred');
            return throwError(error);
        })
    );
}


getPatientsByDoctorEmail(): Observable<PatientI[]> {
  const url = `${environment.apiUrl}/api/Newpatients`;
  return this.http.get<PatientI[]>(url).pipe(
    map(response => response),
    catchError(error => {
      this.errorService.errorSubject.next(error.error.error ? error.error.error : 'No patients found for this doctor.');
      return throwError(error);
    })
  );
}

findPatientsByDoctorAndDisease(diseaseName: string): Observable<PatientI[]> {
  const url = `${environment.apiUrl}/api/patients/search/${diseaseName}`;
  
  return this.http.get<PatientI[]>(url).pipe(
    map(response => {
      return response; 
    }),
    catchError(error => {
      this.errorService.errorSubject.next(
        error.error.error
          ? error.error.error
          : 'An unexpected error occurred while fetching patients by disease.'
      );
      return throwError(error); 
    })
  );
}
countPatientsBySpecialization(): Observable<any> {
  const url = `${environment.apiUrl}/api/patients/specialization/count`;

  return this.http.get<any>(url).pipe(
    map(response => {
      return response; 
    }),
    catchError(error => {
      this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred while fetching patient counts by specialization.');
      return throwError(error); 
    })
  );
}
getChronicDiseaseCount(): Observable<any> {
  const url = `${environment.apiUrl}/api/patients/chronic/count`;

  return this.http.get<any>(url).pipe(
    map(response => {
      return response; 
    }),
    catchError(error => {
      this.errorService.errorSubject.next(error.error.error ? error.error.error : 'An unexpected error occurred while fetching patient counts by chronic disease.');
      return throwError(error); 
    })
  );
}

} 