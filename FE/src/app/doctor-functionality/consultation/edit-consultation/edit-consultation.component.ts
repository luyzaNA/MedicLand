import { ChangeDetectionStrategy, Component, signal } from '@angular/core';
import { PatientService } from '../../../../services/patient.service';
import { ConsultationService } from '../../../../services/consultation.service';
import { Patient } from '../../../../shared/PatientI';
import { Disease } from '../../../../shared/DiseaseI';
import { AuthentificationService } from '../../../../services/authentification.service';
import { Consultation } from '../../../../shared/ConsultationI';
import { User } from '../../../../shared/UserI';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatExpansionModule } from '@angular/material/expansion';

@Component({
  selector: 'app-edit-consultation',
  standalone: true,
  imports: [MatExpansionModule, CommonModule, FormsModule, MatDatepickerModule],
  providers: [ConsultationService, AuthentificationService, PatientService],
  templateUrl: './edit-consultation.component.html',
  styleUrl: './edit-consultation.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush,

})
export class EditConsultationComponent {
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




  updateConsultation() {
    this.consultation.patientCnp = this.patient.cnp;
    this.consultation.diseases = this.medicalHistory;
console.log(this.consultation)
    if (this.consultation.id) {
      this.consultationService.updateConsultation(this.consultation.id, this.consultation.medication, this.consultation.diseases,
        this.consultation.symptoms).subscribe(
        (response) => {
          console.log('Consultation updated:', response);
          this.successMessage = 'Consultation updated successfully!';
          setTimeout(() => {
            this.successMessage = null;
          }, 3000);
        },
        (error) => {
          console.error('Error updating consultation:', error);
          this.noResultsMessage = 'An error occurred while updating the consultation.';
        }
      );
    } else {
      this.noResultsMessage = 'Invalid patient ID.';
    }
  }

nr: string=''
id: number=0;
oldSymptoms:string = '';
oldMedication: string = ''
oldDiseases: string = ''
onSearch() {
  this.id= parseInt(this.nr, 10);
  this.consultationService.getConsultation(this.id).subscribe(
    (response) => {
      this.patient=response['patient']
      this.oldSymptoms = response['symptoms']
      this.oldMedication = response['medication']
      this.oldDiseases = response['diagnostic'].map((disease: { name: string; }) => disease.name).join(', ');
      if (this.patient) {
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
