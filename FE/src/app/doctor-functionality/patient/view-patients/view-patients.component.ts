import { Component, OnInit } from '@angular/core';
import { PatientService } from '../../../../services/patient.service';
import { PatientDetail } from '../../../../shared/PatientI';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-view-patients',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [PatientService],
  templateUrl: './view-patients.component.html',
  styleUrls: ['./view-patients.component.css']
})
export class ViewPatientsComponent implements OnInit {
  patients: PatientDetail[] = [];
  errorMessage: string = '';
  
  constructor(private patientService: PatientService) {}

  ngOnInit(): void {
    this.getPatientsByDoctorEmail();
  }

  getPatientsByDoctorEmail(): void {
    this.patientService.getPatientsByDoctorEmail().subscribe({
      next: (response) => {
        this.patients = response.map(patient => ({
          fullName: `${patient.firstName} ${patient.lastName}`,
          age: patient.age,
          sex: patient.sex,
          bloodGroup: patient.bloodGroup,
          rh: patient.rh,
          allergies: patient.allergies || 'N/A',
          occupation: patient.occupation,
          cnp: patient.cnp
        }));
      },
      error: (error) => {
        console.error(error);
        this.errorMessage = error.error.error ? error.error.error : 'No patients found for this doctor.';
      }
    });
  }

  filterName: string = '';
  filterSex: string = '';
  filterBloodGroup: string = '';
  filterRh: string = '';
  filterOccupation: string = '';
  filterCnp: string = '';

  sexOptions: string[] = ["M", "F"];
  rhOptions: string[] = ['POZITIV', 'NEGATIV'];
  bloodGroupOptions: string[] = ['A', 'B', 'AB', 'O'];
  
  applyFilters(): void {
    let filteredPatients = [...this.patients];

    if (this.filterName) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.fullName.toLowerCase().includes(this.filterName.toLowerCase())
      );
    }

    if (this.filterSex) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.sex === this.filterSex
      );
    }

    if (this.filterBloodGroup) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.bloodGroup === this.filterBloodGroup
      );
    }

    if (this.filterRh) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.rh === this.filterRh
      );
    }

    if (this.filterOccupation) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.occupation.toLowerCase().includes(this.filterOccupation.toLowerCase())
      );
    }

    if (this.filterCnp) {
      filteredPatients = filteredPatients.filter(patient =>
        patient.cnp.includes(this.filterCnp)
      );
    }

    this.patients = filteredPatients;
  }

  clearFilter(filterKey: string): void {
    (this as any)[filterKey] = '';

    if (filterKey === 'Select Rh') {
      this.filterBloodGroup = '';
    } else if (filterKey === 'filterBloodGroup') {
      this.filterRh = '';
    }

    this.applyFilters();
    this.getPatientsByDoctorEmail();
  }

  clearFilters(): void {
    this.filterName = '';
    this.filterSex = '';
    this.filterBloodGroup = '';
    this.filterRh = '';
    this.filterOccupation = '';
    this.filterCnp = '';
    this.applyFilters();
    this.getPatientsByDoctorEmail();
  }
}
