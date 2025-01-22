import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ErrorService {

   errorSubject = new BehaviorSubject<string | null>(null); 
  error$ = this.errorSubject.asObservable();  

  constructor() {}

  showError(message: string) {
    console.log('Showing error:', message);  
    this.errorSubject.next(message);
  }

  clearError() {
    this.errorSubject.next(null);  
  }
}

