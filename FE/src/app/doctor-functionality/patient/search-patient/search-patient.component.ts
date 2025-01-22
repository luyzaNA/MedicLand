import { Component } from '@angular/core';
import { PatientService } from '../../../../services/patient.service';
import { ConsultationService } from '../../../../services/consultation.service';
import { Patient } from '../../../../shared/PatientI';
import { ConsultationPatientI } from '../../../../shared/ConsultationI';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-search-patient',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [PatientService],
  templateUrl: './search-patient.component.html',
  styleUrls: ['./search-patient.component.css']
})
export class SearchPatientComponent {
  constructor(
    private patientService: PatientService,
    private consultationService: ConsultationService
  ) {}

  cnp: string = '';
  patient: Patient = new Patient();
  consultations: ConsultationPatientI[] = []; 
  
  noResultsMessage: string | null = null;
  successMessage: string | null = null;

  onSearch() {
    if (this.cnp) {
      this.patientService.getPatientByCnp(this.cnp).subscribe(
        (patient) => {
          if (patient) {
            this.patient = patient;
            this.consultationService.getConsultationByPatient(this.cnp).subscribe(
              (response) => {
                this.consultations = response.map(consultation => ({
                  id: consultation.id,
                  date: consultation.date,
                  medication: consultation.medication,
                  symptoms: consultation.symptoms,
                  diagnostic: consultation.diagnostic,
                  doctorEmail: consultation.doctor.email,
                  doctorFirstName: consultation.doctor.firstName,
                  doctorLastName: consultation.doctor.lastName,
                  doctorSpecializationName: consultation.doctor.specialization.name,
                  patientCnp: consultation.patient.cnp,
                  patientFirstName: consultation.patient.firstName,
                  patientLastName: consultation.patient.lastName,
                  patientBirthDate: consultation.patient.birthDate,
                  patientAge: consultation.patient.age,
                  patientAddress: consultation.patient.address,
                  patientEmail: consultation.patient.email,
                  patientPhone: consultation.patient.phone,
                  patientLocality: consultation.patient.locality,
                  patientBloodGroup: consultation.patient.bloodGroup,
                  patientRh: consultation.patient.rh,
                  patientWeight: consultation.patient.weight,
                  patientHeight: consultation.patient.height,
                  patientAllergies: consultation.patient.allergies,
                  patientOccupation: consultation.patient.occupation,
                  patientRecordDate: consultation.patient.recordDate,
                  patientSex: consultation.patient.sex,
                  patientDiseases: consultation.patient.diseases
                }));
              },
              (error) => {
              }
            );
          } else {
          }
        },
        (error) => {
          this.noResultsMessage = 'A apărut o eroare la căutarea pacientului.';
        }
      );
    }
  }
}
