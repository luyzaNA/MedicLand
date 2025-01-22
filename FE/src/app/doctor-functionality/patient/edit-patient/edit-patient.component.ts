import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Patient, PatientI } from '../../../../shared/PatientI';
import { Disease, DiseaseCategory } from '../../../../shared/DiseaseI';
import { PatientService } from '../../../../services/patient.service';

@Component({
  selector: 'app-edit-patient',
  standalone: true,
  imports: [FormsModule, CommonModule],
  providers: [PatientService],
  templateUrl: './edit-patient.component.html',
  styleUrl: './edit-patient.component.css'
})
export class EditPatientComponent {

  constructor(private patientService: PatientService) {
    
  }
  disease: Disease = new Disease();
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

    onSubmit() {
      this.patient.address = this.formatAddress(
        this.street,
        this.number,
        this.block,
        this.staircase,
        this.apartment,
        this.floor
      );
      this.patient.allergies += ', ' + this.allergies
      if (this.patient.diseases) {
        this.patient.diseases.push(...this.medicalHistory);
      }

      if (this.patient.cnp) {
        this.patientService.updatePatient(this.patient.cnp, this.patient).subscribe(
          (updatedPatient) => {
            this.successMessage = 'Patient successfully updated!';
            this.cnp=''
    
            setTimeout(() => {
              this.successMessage = null;
            }, 3000);
            console.log('Patient updated successfully:', updatedPatient);
          },
          (error) => {
            console.error('Error updating patient:', error);
          }
        );
      }
    }
      

patient: Patient = new Patient();
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
        } else {
          this.noResultsMessage = 'No patient found with the given CNP.';
        }
       
      },(error) => {
        console.error('Error searching patient:', error);
        this.noResultsMessage = 'An error occurred while searching for patients';
      }
    );
  }
}

  
private formatAddress(
  street: string,
  number: number | null,
  block: string,
  staircase: string,
  apartment: any,
  floor: any
): string {
  let addressParts = [];

  if (street) addressParts.push(`str. ${street}`);
  if (number) addressParts.push(`nr. ${number}`);
  if (block) addressParts.push(`bl. ${block}`);
  if (staircase) addressParts.push(`sc. ${staircase}`);
  if (apartment) addressParts.push(`ap. ${apartment}`);
  if (floor) addressParts.push(`et. ${floor}`);

  return addressParts.join(', ');
}


}
