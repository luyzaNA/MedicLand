import { Component } from '@angular/core';
import { AddConsultationComponent } from '../add-consultation/add-consultation.component';
import { CommonModule } from '@angular/common';
import { EditConsultationComponent } from '../edit-consultation/edit-consultation.component';
import { DeleteConsultationComponent } from '../delete-consultation/delete-consultation.component';
import { ViewConsultationsComponent } from '../view-consultations/view-consultations.component';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-consultation-management',
  standalone: true,
  imports: [AddConsultationComponent, CommonModule, EditConsultationComponent, DeleteConsultationComponent, ViewConsultationsComponent,RouterLink],
  templateUrl: './consultation-management.component.html',
  styleUrl: './consultation-management.component.css'
})
export class ConsultationManagementComponent {

  isAddConsultationFormVisible: boolean = false;
  isEditConsultationFormVisible: boolean = false;
  isDeleteConsultationFormVisible: boolean = false;
  isConsultationsTableVisible: boolean = false;

  toggleAddConsultationForm(){
    this.isAddConsultationFormVisible = true;
    this.isEditConsultationFormVisible = false;
    this.isDeleteConsultationFormVisible = false;
    this.isConsultationsTableVisible = false;
  }

  toggleEditConsultationForm(){
    this.isEditConsultationFormVisible = true;
    this.isAddConsultationFormVisible = false;
    this.isDeleteConsultationFormVisible = false;
    this.isConsultationsTableVisible = false;
  }

  toggleDeleteConsultationForm(){
    this.isDeleteConsultationFormVisible = true;
    this.isEditConsultationFormVisible = false;
    this.isAddConsultationFormVisible = false;
    this.isConsultationsTableVisible = false;
  }

  toggleViewConsultation(){
    this.isConsultationsTableVisible = true;
    this.isDeleteConsultationFormVisible = false;
    this.isEditConsultationFormVisible = false;
    this.isAddConsultationFormVisible = false;
  }
}
