import { Component, OnInit } from '@angular/core';
import { ErrorService } from '../../services/error.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-error-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './error-modal.component.html',
  styleUrl: './error-modal.component.css'
})
  export class ErrorModalComponent implements OnInit{
    constructor(private errorService: ErrorService) {
    }
    isVisible: boolean = false;
    errorMessage: string = '';
  
    ngOnInit() {
      this.errorService.error$.subscribe(error => {
        console.log('Error received in modal:', error);  

        if(error){
          this.isVisible = true;
          this.errorMessage = error;
        } else {
          this.isVisible = false;
          this.errorMessage = '';
        }
        console.log('Error:', error)
      });
    }
  
    closeModal() {
      this.errorService.errorSubject.next(null);
    }
  }

