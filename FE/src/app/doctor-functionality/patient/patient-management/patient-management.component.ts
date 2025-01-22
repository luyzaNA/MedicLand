import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import {RouterLink} from "@angular/router";
import { AddPatientComponent } from "../add-patient/add-patient.component";
import { EditPatientComponent } from "../edit-patient/edit-patient.component";
import { DeletePatientComponent } from "../delete-patient/delete-patient.component";
import { ViewPatientsComponent } from '../view-patients/view-patients.component';
import { SearchPatientComponent } from '../search-patient/search-patient.component';
import { MatIconModule } from '@angular/material/icon';

@Component({
    selector: 'app-patient-management',
    standalone: true,
    templateUrl: './patient-management.component.html',
    styleUrl: './patient-management.component.css',
    imports: [
        RouterLink, CommonModule,
        AddPatientComponent,
        EditPatientComponent,
        DeletePatientComponent,
        ViewPatientsComponent,
        SearchPatientComponent,
        MatIconModule
    ]
})
export class PatientManagementComponent {

  isAddPatientFormVisible: boolean = false;
  isEditPatientFormVisible: boolean = false;
  isDeletePatientFormVisible: boolean = false;
  isPatientsTableVisible: boolean = false;
  isPatientisible: boolean = false;


  toggleAddPatientForm() {
    this.isAddPatientFormVisible = true;
    this.isDeletePatientFormVisible = false;
    this.isEditPatientFormVisible = false;
    this.isPatientsTableVisible = false;
    this.isPatientisible = false;
  }

  toggleEditPatientForm() {
    this.isEditPatientFormVisible = true;
    this.isAddPatientFormVisible = false;
    this.isDeletePatientFormVisible = false;
    this.isPatientsTableVisible = false;
    this.isPatientisible = false;
  }

  toggleDeletePatientForm() {
    this.isDeletePatientFormVisible = true;
    this.isEditPatientFormVisible = false;
    this.isAddPatientFormVisible = false;
    this.isPatientsTableVisible = false;
    this.isPatientisible = false;
  }

  showAllPatients() {
    this.isPatientsTableVisible = true;
    this.isDeletePatientFormVisible = false;
    this.isEditPatientFormVisible = false;
    this.isAddPatientFormVisible = false;
    this.isPatientisible = false;
  }

  toggleViewPatient() {
    this.isPatientisible = true;
    this.isPatientsTableVisible = false;
    this.isDeletePatientFormVisible = false;
    this.isEditPatientFormVisible = false;
    this.isAddPatientFormVisible = false;
  }
}
