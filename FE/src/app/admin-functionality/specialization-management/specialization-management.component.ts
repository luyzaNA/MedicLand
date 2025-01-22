import { Component, OnInit } from '@angular/core';
import { SpecializationService } from '../../../services/specialization.service';
import { Specialization } from '../../../shared/SpecializationI';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-specialization-management',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [SpecializationService],
  templateUrl: './specialization-management.component.html',
  styleUrls: ['./specialization-management.component.css'],
})
  export class SpecializationManagementComponent implements OnInit {
    specializations: Specialization[] = [];
    filteredSpecializations: Specialization[] = [];
    newSpecializationName: string = '';
    isAddFormVisible: boolean = false;
    isTableVisible: boolean = false;
    searchQuery: string = '';
    successMessage: string = '';
    messageTimeout: any;
  
    constructor(private specializationService: SpecializationService) {}
  
    ngOnInit(): void {
      this.loadSpecializations();
      
    }
  
    loadSpecializations(): void {
      this.specializationService.getAllSpecializations().subscribe(
        (data: Specialization[]) => {
          this.specializations = data;
          this.filteredSpecializations = data;
        },
        (error) => {
          console.error('Error loading specializations:', error);
        }
      );
    }
  
    toggleAddForm(): void {
      this.isAddFormVisible = !this.isAddFormVisible;
      this.isTableVisible = false;
    }
  
    showTable(): void {
      this.isTableVisible = true;
      this.isAddFormVisible = false;
    }
  
    saveSpecialization(): void {
      if (this.newSpecializationName.trim()) {
        this.specializationService.addSpecialization(this.newSpecializationName).subscribe(
          (response) => {
            this.specializations.push(response);
            this.filteredSpecializations = [...this.specializations];
            this.newSpecializationName = '';
            this.isAddFormVisible = false;
            this.showSuccessMessage(`Specialization '${response.name}' was added successfully!`);
          },
          (error) => {
            console.error('Error saving specialization:', error);
          }
        );
      }
    }
  
    showSuccessMessage(message: string): void {
      this.successMessage = message;
      clearTimeout(this.messageTimeout);
      this.messageTimeout = setTimeout(() => {
        this.successMessage = '';
      }, 5000); 
    }
  
    closePopup(): void {
      this.successMessage = ''; 
    }
  
    searchSpecializations(): void {
      this.filteredSpecializations = this.specializations.filter(specialization =>
        specialization.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    }
    get isNameInvalid(): boolean {
      return this.newSpecializationName.trim().length < 3;
    }
  }