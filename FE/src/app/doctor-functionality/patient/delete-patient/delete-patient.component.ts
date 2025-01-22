import { Component } from '@angular/core';
import { PatientService } from '../../../../services/patient.service';
import { CommonModule } from '@angular/common';
import { Patient } from '../../../../shared/PatientI';
import { FormsModule } from '@angular/forms';
import { ConfirmDialogComponent } from '../../../../shared/confirm-dialog/confirm-dialog.component';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MatIcon, MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-delete-patient',
  standalone: true,
  imports: [CommonModule, FormsModule, ConfirmDialogComponent, MatDialogModule, MatIconModule],
  providers:[PatientService],
  templateUrl: './delete-patient.component.html',
  styleUrl: './delete-patient.component.css'
})
export class DeletePatientComponent {
noResultsMessage: any;
successMessage: string | null = null; 

  constructor(private patientService: PatientService,  private dialog: MatDialog) {
    
  }

  cnp: string='';
patient:Patient = new Patient()
  onSearch() {
    if (this.cnp) {
      this.patientService.getPatientByCnp(this.cnp).subscribe(
        (patient) => {
          if (patient) {
            this.patient = patient;  
          } else {
            this.noResultsMessage = 'No patient found with the given CNP.';
          }         
        },
        (error) => {
          console.error('Error searching patient:', error);
          this.noResultsMessage = 'An error occurred while searching for patients';
        }
      );
    }
  }

  confirmDelete(): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '250px',
      data: { data: this.patient.firstName } 
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === 'yes') {
        this.deletePatient();
      }
    });
  }


  deletePatient() {
    this.patientService.deletePatient(this.patient.cnp).subscribe(
      () => {
        this.patient = new Patient();  
        this.noResultsMessage = '';
        this.successMessage = 'Patient successfully deleted!';
        this.cnp=''

        setTimeout(() => {
          this.successMessage = null;
        }, 3000);
      },
      (error) => {
        console.error('Error deleting patient:', error);
        this.noResultsMessage = 'An error occurred while deleting the patient';
      }
    );
  }
}