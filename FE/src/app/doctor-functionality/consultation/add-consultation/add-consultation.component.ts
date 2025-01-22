import { CommonModule } from '@angular/common';
import { Component,ChangeDetectionStrategy,signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MatExpansionModule } from '@angular/material/expansion';
import { Patient } from '../../../../shared/PatientI';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { PatientService } from '../../../../services/patient.service';
import { Disease } from '../../../../shared/DiseaseI';
import { User } from '../../../../shared/UserI';
import { Consultation } from '../../../../shared/ConsultationI';
import { AuthentificationService } from '../../../../services/authentification.service';
import { ConsultationService } from '../../../../services/consultation.service';


@Component({
  selector: 'app-add-consultation',
  standalone: true,
  imports: [MatExpansionModule, CommonModule, FormsModule, MatDatepickerModule],
  providers: [PatientService,AuthentificationService,ConsultationService],
  templateUrl: './add-consultation.component.html',
  styleUrl: './add-consultation.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush,


})
export class AddConsultationComponent {
  readonly panelOpenState = signal(false);
  consultation: Consultation = new Consultation()
  disease: Disease=new Disease();
  doctor: User = new User();
  age: string = '';
  weight: string = '';
  height: string = '';
  number: number | null = null;
  block: string = '';
  staircase: string = '';
  apartment: string = '';
  floor: string = '';
  street: string = '';
  cnp: string = '';
  noResultsMessage: string = '';
  allergies: string ='';
  minDate: string = new Date(new Date().setFullYear(new Date().getFullYear() - 120)).toISOString().split('T')[0];
  maxDate: string = new Date().toISOString().split('T')[0];
  successMessage: string | null = null; 
  
  patient: Patient = new Patient();

  constructor(private patientService: PatientService, private authService: AuthentificationService,
    private consultationService: ConsultationService){}
  medicalHistory: Disease[] = [];

  add_disease(){
    this.medicalHistory.push({ 
      name: this.disease.name, 
      category: this.disease.category, 
      description: this.disease.description 
    });

    
    this.successMessage = 'Disease successfully added!';

        setTimeout(() => {
          this.successMessage = null;
        }, 3000);
    this.disease.name = '';
    this.disease.category ;
    this.disease.description = '';
    console.log(this.medicalHistory)
  }

 
  private resetConsultation() {
    this.consultation = new Consultation();
    this.patient = new Patient();
    this.doctor = new User();
    this.noResultsMessage = '';
  }



  addConsultation() {
  if (!this.patient || !this.doctor) {
    this.noResultsMessage = 'Please ensure patient and doctor are correctly loaded.';
    return;
  }

  this.consultation.patientCnp = this.patient.cnp;
  this.consultation.diseases = this.medicalHistory;
  this.consultationService.addConsultation(this.consultation).subscribe(
    (response) => {
      this.successMessage = 'Consultation added successfully!';

      setTimeout(() => {
        this.successMessage = null;
      }, 3000);
      this.resetConsultation();
    },
    (error) => {
      console.error('Error adding consultation:', error);
      this.noResultsMessage = 'An error occurred while adding the consultation.';
    }
  );
}
 


onSearch() {
  if (this.cnp) {
    this.patientService.getPatientByCnp(this.cnp).subscribe(
      (patient) => {
        if (patient) {
          this.patient = patient; 
          this.age = this.patient.age.toString();
          this.weight = this.patient.weight.toString();
          this.height = this.patient.height.toString();

          if (this.patient.address) {
            const addressParts = this.patient.address.split(',').map(part => part.trim()); 
            if (addressParts.length >= 2) {
              this.street = addressParts[0]?.replace(/^(str\.)/i, '').trim() || '';
              this.number = parseInt(addressParts[1]?.replace(/^(nr\.|număr)/i, '').trim() || '', 10) || null;
              this.block = addressParts[2]?.replace(/^(bl\.|Bloc)/i, '').trim() || '';
              this.staircase = addressParts[3]?.replace(/^(sc\.|Scară)/i, '').trim() || '';
              this.apartment = addressParts[4]?.replace(/^(ap\.|Apartament)/i, '').trim() || '';
              this.floor = addressParts[5]?.replace(/^(et\.|Etaj)/i, '').trim() || '';
            }
          }

          this.authService.getCurrentUser().subscribe(
            (user) => {
              this.doctor = user; 
            },
            (error) => {
              console.error('Error fetching current user:', error);
            }
          );
        } else {
          this.noResultsMessage = 'No patient found with the given CNP.';
        }
      },
      (error) => {
        console.error('Error searching patient:', error);
        this.noResultsMessage = 'An error occurred while searching for patients.';
      }
    );
  }
}

    
  

}
