import { Injectable } from '@angular/core';
import { User, UserI } from '../shared/UserI';
import { Observable } from 'rxjs/internal/Observable';
import { HttpClient } from '@angular/common/http';
import { ErrorService } from './error.service';
import { environment } from '../shared/env';
import { BehaviorSubject, catchError, map, switchMap, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthentificationService {

  private apiUrl = `${environment.apiUrl}`;
  private currentUser: UserI = new User();
  currentUserSubject = new BehaviorSubject<User>(new User());
  currentUser$: Observable<UserI> = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient,
    private errorService: ErrorService) {

    }


  registerUser(userPayload: UserI): Observable<User> {
    return this.http.post<any>(`${this.apiUrl}/api/register`, userPayload).pipe(
      map((response) => {
        return response.user as User;
      }),
      catchError((error) => {
        this.errorService.errorSubject.next(error.error.message || 'Registration failed.');
        return throwError(error);
      })
    );
  }
    registerPatient(email: string,  password: string): Observable<string> {
      return this.http.post<any>(`${this.apiUrl}/register/patient`, {email, password}).pipe(
        map(response => {
          if (response && response.token) {
            localStorage.setItem('token', response.token);
            return response.id;
          } else {
            this.errorService.errorSubject.next(response.message || 'Authentication failed.');
          }
        }),
        switchMap(id => this.fetchCurrentUser().pipe(map(user => {
          return user.id;
        }))),
        catchError(error => {
          this.errorService.errorSubject.next(error.error.message || 'Registration failed.');
          return throwError(error);
        })
      );
    }


  loginUser(email: string, password: string): Observable<UserI> {
    return this.http.post<any>(`${this.apiUrl}/api/login`, {email, password}).pipe(
      map(response => {
        if (response && response.token) {
          localStorage.setItem('token', response.token);
          return response.token;
        } else {
          this.errorService.errorSubject.next(response.message || 'Authentication failed.');
        }
      }),
      switchMap(token => this.fetchCurrentUser()),
      catchError(error => {
        this.errorService.errorSubject.next(error.error.message || 'Authentication failed.');
        return throwError(error);
      })
    );
  }

  fetchCurrentUser(): Observable<UserI> {
    console.log("fetching user");


    return this.http.get<UserI>(`${this.apiUrl}/api/auth/user`).pipe(
      map(response => {
        this.currentUser = response;
        this.currentUserSubject.next(this.currentUser);
        return this.currentUser;
      }),
      catchError(error => {
        console.error('Error fetching user:', error);
        this.errorService.errorSubject.next(error.error.message || 'Fetching user failed.');
        return throwError(error);
      })
    );
  }

  logout(): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/api/logout`, {}).pipe(
      map(response => {
        localStorage.removeItem('token');
        this.currentUserSubject.next(new User());
        return response;
      }),
      catchError(error => {
        this.errorService.errorSubject.next(error.error.message || 'Logout failed.');
        return throwError(error);
      })
    );
  }

  getCurrentUser(): Observable<UserI> {
    if (!this.currentUser.id) {
      return this.fetchCurrentUser().pipe(
        map((response) => {
          this.currentUserSubject.next(response); 
          return response; 
        }),
        catchError((error) => {
          console.error('Error in getCurrentUser:', error);
          return throwError(error);
        })
      );
    }
    return this.currentUser$;
  }

}


