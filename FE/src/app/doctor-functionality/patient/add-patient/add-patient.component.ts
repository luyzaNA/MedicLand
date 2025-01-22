import { CommonModule, DatePipe } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { PatientService } from '../../../../services/patient.service';
import { Patient, PatientI, RhFactor } from '../../../../shared/PatientI';
import { Disease, DiseaseCategory } from '../../../../shared/DiseaseI';

@Component({
  selector: 'app-add-patient',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [PatientService, DatePipe],
  templateUrl: './add-patient.component.html',
  styleUrl: './add-patient.component.css'
})
export class AddPatientComponent {
  patient: Patient = new Patient();  
  number!: number;
  block: string='';
  staircase: string='';
  apartment: any
  floor: any;
  street!: string;
  minDate: string = new Date(new Date().setFullYear(new Date().getFullYear() - 120)).toISOString().split('T')[0];
  maxDate: string = new Date().toISOString().split('T')[0];
  successMessage: string | null = null; 

  diseaseDescription: any;
  diseaseCategory!: DiseaseCategory;
diseaseName: any;


  disease: Disease = new Disease();
  medicalHistory: Disease[] = [];
  age: string ='';
  weight: string = ''
  height: string =''

  constructor(private patientService: PatientService, protected datePipe: DatePipe) {
  }

  add_disease() {
    if (this.disease.name && this.disease.category) {
        this.medicalHistory.push({...this.disease});
        this.successMessage = 'Disease successfully added!';
        setTimeout(() => this.successMessage = null, 3000);
        this.disease.name = '';
        this.disease.category;
        this.disease.description = '';
    }
}


  
  onSubmit(): void {
    this.patient.address = this.formatAddress(
      this.street,
      this.number,
      this.block,
      this.staircase,
      this.apartment,
      this.floor
    );

    this.patient.diseases = this.medicalHistory;
    this.patient.age= parseInt(this.age, 10);
    this.patient.height = parseFloat(this.height); 

    
    this.patient.weight = parseFloat(this.weight);  
  
    this.patient.rh = RhFactor[this.patient.rh?.toUpperCase() as keyof typeof RhFactor];
    this.patientService.addPatient(this.patient.cnp, this.patient.firstName, this.patient.lastName,
      this.patient.locality, this.patient.address, this.patient.phone, this.patient.rh,
      this.patient.allergies, this.patient.occupation, this.patient.bloodGroup!,this.patient.weight, this.patient.height,this.patient.email, this.medicalHistory
      ).subscribe({
      next: (response) => {
        this.successMessage = 'Patient added successfully!';

        setTimeout(() => {
          this.successMessage = null;
        }, 3000);
        console.log('Patient added successfully', response);
      },
      error: (error) => {
        console.error('Error adding patient', error);
      }
    });
  }
  
  
  
  private formatAddress(
    street: string,
    number: number,
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

  cnpErrorMessage: string | null = null;

  validateAndExtractCNP(cnp: string): void {
    if (!/^\d{13}$/.test(cnp)) {
       
        this.cnpErrorMessage = 'The CNP must have 13 digits.';
        return;
    }

    const genderCode = parseInt(cnp.charAt(0), 10);
    const yearPrefix = genderCode <= 2 || genderCode === 7 || genderCode === 8 ? 1900 : 2000;
    const year = yearPrefix + parseInt(cnp.slice(1, 3), 10);
    const month = parseInt(cnp.slice(3, 5), 10);
    const day = parseInt(cnp.slice(5, 7), 10);

    if (month < 1 || month > 12 || day < 1 || day > 31) {
        this.cnpErrorMessage = 'Invalid CNP format';
        return;
    }

    const birthDate = new Date(year, month - 1, day);
    this.patient.birthDate = birthDate;
    const currentDate = new Date();
    this.age = (currentDate.getFullYear() - birthDate.getFullYear()).toString();
    this.patient.sex = genderCode % 2 === 1 ? 'M' : 'F';
    this.cnpErrorMessage = null; 
}
}