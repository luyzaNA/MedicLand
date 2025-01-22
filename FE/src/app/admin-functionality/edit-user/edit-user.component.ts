import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../../services/user.service';
import { UserI } from '../../../shared/UserI';
import { SpecializationService } from '../../../services/specialization.service';
import { Specialization } from '../../../shared/SpecializationI';

@Component({
  selector: 'app-edit-user',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [UserService],
  templateUrl: './edit-user.component.html',
  styleUrl: './edit-user.component.css'
})
export class EditUserComponent {
  users: UserI[] = [];
  filteredUsers: UserI[] = [];
  searchQuery: string = '';
  selectedUser: UserI | null = null;
  specializations: string[] = []; 
  selectedSpecialization: string = ''; 
  isDirector: boolean = false;
  showNoUsersMessage: boolean = false;
  successMessage: string = '';
  messageTimeout: any; 

  constructor(private userService: UserService) {}

  ngOnInit(): void {
    this.getDoctors(); 
  }

  getDoctors(): void {
    this.userService.getDoctors().subscribe(
      (data) => {
        this.users = data;
        this.filteredUsers = []; 
      },
      (error) => {
        console.error('Error fetching medics:', error);
      }
    );
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

  filterUsers(): void {
    if (this.searchQuery.length >= 5) {
      this.filteredUsers = this.users.filter(user =>
        user.email.toLowerCase().startsWith(this.searchQuery.toLowerCase())
      );
      this.showNoUsersMessage = this.filteredUsers.length === 0;
    } else {
      this.filteredUsers = [];
      this.showNoUsersMessage = false; 
    }
  }

  selectUser(user: UserI): void {
    this.selectedUser = user;
    this.filteredUsers = [];  
    this.searchQuery = '';    
    this.isDirector = user.roles.includes('Director'); 
  }
  saveUser(): void {
    if (this.selectedUser) {
      const { email, firstName, roles } = this.selectedUser;

      let updatedRoles = [...roles];

      if (this.isDirector) {
        if (!updatedRoles.includes('director')) {
          updatedRoles.push('director');
        }
      } else {
        updatedRoles = updatedRoles.filter(role => role !== 'director');
      }
      console.log(updatedRoles); 

      this.userService.updateUser(email, firstName, updatedRoles).subscribe(
        response => {
          this.showSuccessMessage(`Doctot ${firstName} updated successfully!`);
        },
        error => {
          console.error('Error updating user:', error);
        }
      );
    }
  }
}