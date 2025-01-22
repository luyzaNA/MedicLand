import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../../services/user.service';
import { UserI } from '../../../shared/UserI';
import { Router, RouterModule } from '@angular/router';
import { EditUserComponent } from '../edit-user/edit-user.component';
import { DeleteUserComponent } from "../delete-user/delete-user.component";
import { CreateUserComponent } from "../create-user/create-user.component";

@Component({
    selector: 'app-accounts-management',
    standalone: true,
    providers: [UserService],
    templateUrl: './accounts-management.component.html',
    styleUrl: './accounts-management.component.css',
    imports: [CommonModule, FormsModule, RouterModule, EditUserComponent, DeleteUserComponent, CreateUserComponent]
})
export class AccountsManagementComponent {
  isAddUserFormVisible: boolean = false;
  isEditUserFormVisible: boolean = false;
  isDeleteUserFormVisible: boolean = false;
  isUsersTableVisible: boolean = false;
  users: UserI[] = [];
  filteredUsers: UserI[] = [];
  searchQuery: string = '';
  selectedRole: string = ''; 
  selectedSpecialization: string = ''; 

  constructor(private userService: UserService, private router: Router) {}

  ngOnInit(): void {
  }

  getUsers(): void {
    this.userService.getUsers().subscribe(
      (data) => {
        this.users = data; 
        this.filteredUsers = this.users; 
        this.isUsersTableVisible = true; 
      },
      (error) => {
        console.error('Error fetching users:', error);
      }
    );
  }

  searchUsers(): void {
    this.filteredUsers = this.users.filter(user =>
      user.email.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
      (this.selectedRole ? (Array.isArray(user.roles)
        ? user.roles.join(' ').toLowerCase().includes(this.selectedRole.toLowerCase()) 
        : user.roles.toLowerCase().includes(this.selectedRole.toLowerCase())) : true) &&
      (this.selectedSpecialization ? user.specialization?.toLowerCase().includes(this.selectedSpecialization.toLowerCase()) : true)
    );
  }

  toggleAddUserForm() {
    this.isAddUserFormVisible = !this.isAddUserFormVisible;
    this.isEditUserFormVisible = false;
    this.isDeleteUserFormVisible = false;
    this.isUsersTableVisible = false;
  }

  toggleEditUserForm() {
    this.isEditUserFormVisible = !this.isEditUserFormVisible;
    this.isAddUserFormVisible = false;
    this.isDeleteUserFormVisible = false;
    this.isUsersTableVisible = false;

  }

  toggleDeleteUserForm() {
    this.isDeleteUserFormVisible = !this.isDeleteUserFormVisible;
    this.isAddUserFormVisible = false;
    this.isEditUserFormVisible = false;
    this.isUsersTableVisible = false;
  }

  showAllUsers() {
    this.isAddUserFormVisible = false;
    this.isEditUserFormVisible = false;
    this.isDeleteUserFormVisible = false;
    this.isUsersTableVisible = true;
    this.getUsers(); 

  }

  getRoles(): string[] {
    const roles = this.users.map(user => user.roles).flat();
    return Array.from(new Set(roles)).sort(); 
  }

  getSpecializations(): string[] {
    const specializations = this.users.map(user => user.specialization).filter(Boolean); 
    return Array.from(new Set(specializations)).sort(); 
  }

  clearFilters(): void {
    this.selectedRole = '';
    this.selectedSpecialization = '';
    this.searchQuery = '';
    this.filteredUsers = this.users;
  }

  shouldShowSpecializationFilter(): boolean {
    return this.selectedRole !== 'admin' && this.selectedRole !== 'patient';
  }
}
