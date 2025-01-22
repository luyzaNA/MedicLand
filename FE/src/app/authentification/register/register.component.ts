import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterModule } from '@angular/router';
import { AuthentificationService } from '../../../services/authentification.service';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [MatIconModule,RouterModule, FormsModule, CommonModule],
  providers: [AuthentificationService],
  templateUrl: './register.component.html',
  styleUrl: './register.component.css'
})
export class RegisterComponent {
  email: string = '';
  password: string = '';
  passwordFieldType: string = 'password'; 

  constructor(private authentificationService: AuthentificationService,private router: Router) {
    
  }
  togglePasswordVisibility(): void {
    this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
  }

  register(){
      if (this.email && this.password) {
        this.authentificationService.registerPatient(this.email, this.password).subscribe({
          next: (response) => {
            console.log(response);
            this.router.navigate(['/Home']);
          },
          error: (error) => {
            console.error('Login failed', error);
          }
        });
      } else {
        alert('Please enter both email and password');
      }
  }

  
}
