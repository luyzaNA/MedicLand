import { Component } from '@angular/core';
import { DiseaseService } from '../../../../services/disease.service';
import { CommonModule, DatePipe } from '@angular/common';
import { PatientService } from '../../../../services/patient.service';
import { PatientI } from '../../../../shared/PatientI';
import { Disease } from '../../../../shared/DiseaseI';
import { map } from 'rxjs/operators';
import { FormsModule } from '@angular/forms';
import html2canvas from 'html2canvas';
import * as jsPDF from 'jspdf';

@Component({
  selector: 'app-disease-record',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [DiseaseService, PatientService, DatePipe],
  templateUrl: './disease-record.component.html',
  styleUrl: './disease-record.component.css'
})
export class DiseaseRecordComponent {
  disease?: Disease; 
  patients: { 
    name: string; 
    age: number; 
    sex: string; 
    cnp: string; 
    locality: string; 
    rhBloodGroup: string; 
    phone: string;
    doctorName: string 
  }[] = [];
  errorMessage: string = '';
  currentDate: Date = new Date();
  doctorName: string =''

  constructor(
    private diseaseService: DiseaseService, 
    private patientService: PatientService,
    protected datePipe: DatePipe
  ) {}

  searchDiseaseAndPatients(name: string): void {
    this.errorMessage = '';
    this.disease = undefined;
    this.patients = [];

    if (!name.trim()) {
      this.errorMessage = 'Please enter a valid disease name.';
      return;
    }

    // Get disease
    this.diseaseService.getDisease(name).subscribe({
      next: (response) => {
        this.disease = response;
      },
      error: (error) => {
        this.errorMessage = error.error || 'An error occurred while fetching the disease.';
      },
    });

    this.patientService.findPatientsByDoctorAndDisease(name).pipe(
      map((response: any[]) =>
        response.map(patientRecord => ({
          name: `${patientRecord.patient.firstName} ${patientRecord.patient.lastName}`,
          age: patientRecord.patient.age,
          sex: patientRecord.patient.sex,
          cnp: patientRecord.patient.cnp,
          locality: patientRecord.patient.locality,
          rhBloodGroup: `${patientRecord.patient.rh}/${patientRecord.patient.bloodGroup}`,
          phone: patientRecord.patient.phone,
          doctorName: this.doctorName || `${patientRecord.doctor.firstName} ${patientRecord.doctor.lastName}` 
        }))
      )
    ).subscribe({
      next: (transformedPatients) => {
        this.patients = transformedPatients;
        this.currentDate = new Date();
        this.doctorName = this.patients[0]?.doctorName || '';
      },
      error: (error) => {
        this.errorMessage = error.error || 'An error occurred while fetching patients.';
      },
    });
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
