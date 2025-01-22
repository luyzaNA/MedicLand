import { Component } from '@angular/core';
import { UserService } from '../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { User, UserI } from '../../../shared/UserI';
import { MatIcon } from '@angular/material/icon';
import {AuthentificationService} from "../../../services/authentification.service";

@Component({
  selector: 'app-create-user',
  standalone: true,
  imports: [CommonModule, FormsModule, MatIcon],
  providers: [UserService, AuthentificationService],
  templateUrl: './create-user.component.html',
  styleUrl: './create-user.component.css'
})
export class CreateUserComponent {
  user: UserI = new User();
  passwordFieldType: string = 'password';
  successMessage: string = '';
  messageTimeout: any;

  constructor(private userService: UserService, private authService: AuthentificationService) {
    this.user.roles = 'admin';

  }

  generatePassword(): void {
    const length = 15;

    const upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const lowerCase = "abcdefghijklmnopqrstuvwxyz";
    const digits = "0123456789";
    const specialChars = "!@#$%^&*()_-+=<>?";
    const allChars = upperCase + lowerCase + digits + specialChars;

    let password: string[] = [];
    password.push(upperCase[Math.floor(Math.random() * upperCase.length)]);
    password.push(lowerCase[Math.floor(Math.random() * lowerCase.length)]);
    password.push(digits[Math.floor(Math.random() * digits.length)]);
    password.push(specialChars[Math.floor(Math.random() * specialChars.length)]);

    for (let i = password.length; i < length; i++) {
      password.push(allChars[Math.floor(Math.random() * allChars.length)]);
    }

    password = password.sort(() => Math.random() - 0.5);

    this.user.password = password.join('');
  }


  togglePasswordVisibility(): void {
    this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
  }
  saveUser(): void {
    let payload: any = {
      email: this.user.email,
      password: this.user.password,
      roles: [this.user.roles] 
    };

    if (this.user.roles === 'director') {
      payload.roles.push('doctor');
    }
     if (this.user.roles === 'doctor' || this.user.roles === 'director') {
      payload.cnp = this.user.cnp;
      payload.firstName = this.user.firstName;
      payload.lastName = this.user.lastName;
      payload.specialization = this.user.specialization;
    }

    this.authService.registerUser(payload).subscribe({
      next: (response) => {
        this.successMessage = 'User creat cu succes!';
        clearTimeout(this.messageTimeout);
        this.messageTimeout = setTimeout(() => this.successMessage = '', 5000);
      },
      error: (error) => {
        this.successMessage = `Eroare: ${error.error.message || 'Crearea utilizatorului a eșuat.'}`;
        clearTimeout(this.messageTimeout);
        this.messageTimeout = setTimeout(() => this.successMessage = '', 5000);
      }
    });
  }

}
