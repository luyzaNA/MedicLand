import { Component } from '@angular/core';
import html2canvas from 'html2canvas';
import * as jsPDF from 'jspdf';
import { ConsultationService } from '../../../../services/consultation.service';
import { CommonModule, DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ConsultationPatientI } from '../../../../shared/ConsultationI';
import { Disease } from '../../../../shared/DiseaseI';
@Component({
  selector: 'app-consultation-record',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [ConsultationService,DatePipe],
  templateUrl: './consultation-record.component.html',
  styleUrl: './consultation-record.component.css'
})
export class ConsultationRecordComponent {
  consultation!: ConsultationPatientI; 

  constructor(private consultationService: ConsultationService,
              protected datePipe: DatePipe){ }
getDiseasesList(): string {
  if (this.consultation.patientDiseases && this.consultation.patientDiseases.length > 0) {
    return this.consultation.patientDiseases
      .map(d => `${d.name} (${d.category})`)
      .join(', ');
  } else {
    return '-';
  }
}

getDiagnosticList(): string {
  if (this.consultation.diagnostic && this.consultation.diagnostic.length > 0) {
    return this.consultation.diagnostic
      .map(d => `${d.name} (${d.category})`)
      .join(', ');
  } else {
    return '-';
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

nr: string = '';
id! : number;
currentDate= new Date();
onSearch() {
  
  this.id = parseInt(this.nr, 10);
  this.currentDate = new Date();

  this.consultationService.getConsultation(this.id).subscribe(
    (response) => {
      
      this.consultation = {
        id: response.id,
        date: response.date,
        medication: response.medication,
        symptoms: response.symptoms,
        diagnostic: response.diagnostic.map((d:Disease) => ({
          name: d.name,
          description: d.description,
          category: d.category
        })),
        doctorEmail: response.doctor.email,
        doctorFirstName: response.doctor.firstName,
        doctorLastName: response.doctor.lastName,
        doctorSpecializationName: response.doctor.specialization.name.toUpperCase(),
        patientCnp: response.patient.cnp,
        patientFirstName: response.patient.firstName,
        patientLastName: response.patient.lastName,
        patientBirthDate: response.patient.birthDate,
        patientAge: response.patient.age,
        patientAddress: response.patient.address,
        patientEmail: response.patient.email,
        patientPhone: response.patient.phone,
        patientLocality: response.patient.locality,
        patientBloodGroup: response.patient.bloodGroup,
        patientRh: response.patient.rh,
        patientWeight: response.patient.weight,
        patientHeight: response.patient.height,
        patientAllergies: response.patient.allergies,
        patientOccupation: response.patient.occupation,
        patientRecordDate: response.patient.recordDate,
        patientSex: response.patient.sex,
        patientDiseases: response.patient.diseases.map((d: Disease) => ({
          name: d.name,
          description: d.description,
          category: d.category
        }))
      };

      console.log(this.consultation);

    },
    (error) => {
      console.error('Error searching patient:', error);
    }
  );
}
}