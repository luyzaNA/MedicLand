import { Component } from '@angular/core';
import { PatientService } from '../../../../services/patient.service';
import { ConsultationService } from '../../../../services/consultation.service';
import { CommonModule, DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ConsultationPatientI } from '../../../../shared/ConsultationI';
import { Patient } from '../../../../shared/PatientI';
import html2canvas from 'html2canvas';
import * as jsPDF from 'jspdf';

@Component({
  selector: 'app-patient-record',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [PatientService,DatePipe],
  templateUrl: './patient-record.component.html',
  styleUrl: './patient-record.component.css'
})
export class PatientRecordComponent {
street: string = '';
number: number | null = null;
block: string = '';
staircase: string = '';
apartment: string = '';
floor: string = '';
weight: string = '';
age: string = '';
height: string = '';
diseases: string = ''
    constructor(
      private patientService: PatientService,
      private consultationService: ConsultationService,
      protected datePipe: DatePipe
    ) {}
  
    cnp: string = '';
    patient: Patient = new Patient();

    
    noResultsMessage: string | null = null;
    successMessage: string | null = null;
    currentDate= new Date();
  
    onSearch() {
      if (this.cnp) {
        this.currentDate = new Date();
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

              if (this.patient.diseases) {

              this.diseases = this.patient.diseases.map(disease => disease.name).join(', ');
              }
              console.log(this.patient)
            }},
              
          (error) => {
            this.noResultsMessage = 'A apărut o eroare la căutarea pacientului.';
          }
        );
      }
    }
    
    generatePDF() {
      const data = document.getElementById('content');
      html2canvas(data!).then(canvas => {
          const imgWidth = 208;
          const imgHeight = canvas.height * imgWidth / canvas.width;
          const contentDataURL = canvas.toDataURL('image/png');
          const pdf = new jsPDF.jsPDF('p', 'mm', 'a4'); 
          const position = 0;
          pdf.addImage(contentDataURL, 'PNG', 0, position, imgWidth, imgHeight);
          pdf.save('exported-file.pdf');
      });
  }

 
  }
  

