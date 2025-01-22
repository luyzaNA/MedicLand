import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ConsultationService } from '../../../../services/consultation.service';
import { PatientService } from '../../../../services/patient.service';
import { AuthentificationService } from '../../../../services/authentification.service';
import { Patient } from '../../../../shared/PatientI';
import { User } from '../../../../shared/UserI';
import { MatIcon } from '@angular/material/icon';
import { ConfirmDialogComponent } from '../../../../shared/confirm-dialog/confirm-dialog.component';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { Consultation } from '../../../../shared/ConsultationI';

@Component({
  selector: 'app-delete-consultation',
  standalone: true,
  imports: [CommonModule, FormsModule,MatIcon, ConfirmDialogComponent, MatDialogModule],
  providers: [ConsultationService],
  templateUrl: './delete-consultation.component.html',
  styleUrl: './delete-consultation.component.css'
})
export class DeleteConsultationComponent {

  constructor(private dialog: MatDialog, private authService: AuthentificationService,
    private consultationService: ConsultationService){}
    
  nr: string=''
  id: number=0;
  patient:Patient = new Patient()
  name: string =''
  noResultsMessage: string = '';
  doctor: User = new User();
  doctor_name: string=''
  successMessage: string='';
  consultation: Consultation = new Consultation();


  onSearch() {
  this.consultation.id = +this.nr; 
  console.log(this.id)

  this.consultationService.getConsultation(this.consultation.id).subscribe(
    (response) => {
      this.consultation.date = response['date'];
      this.patient = response['patient'];
      if (this.patient) {
        this.name = this.patient.firstName + ' ' + this.patient.lastName;
        console.log(this.name);
      }
      this.authService.getCurrentUser().subscribe(
        (user) => {
          this.doctor = user;
          this.doctor_name = this.doctor.firstName + ' ' + this.doctor.lastName;
        },
        (error) => {
          console.error('Error fetching current user:', error);
        }
      );
      
    },
    (error) => {
      console.error('Error searching consultation:', error);
      this.noResultsMessage = 'An error occurred while searching for consultations.';
    }
  );
}


confirmDelete(): void {
  const dialogRef = this.dialog.open(ConfirmDialogComponent, {
    width: '250px',
    data: 'CONSULTATION no ' + this.consultation.id
  });

  dialogRef.afterClosed().subscribe(result => {
    if (result === 'yes') {
      this.deleteConsultation();
    }
  });
}


  deleteConsultation() {
    if(this.consultation.id){
  this.consultationService.deleteConsultation(this.consultation.id).subscribe(
    () => {
     console.log("LUYZA E AICI")
      this.nr = '';

      setTimeout(() => {
        this.successMessage = '';
      }, 3000);

      this.noResultsMessage = '';
      this.successMessage = 'Consultation successfully deleted!';
    },
    (error) => {
      console.error('Error deleting patient:', error);
      this.noResultsMessage = 'An error occurred while deleting the patient';
    }
  );  }
  }
}
