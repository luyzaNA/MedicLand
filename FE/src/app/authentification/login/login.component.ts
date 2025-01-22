import { Component, OnInit } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterModule } from '@angular/router';
import { AuthentificationService } from '../../../services/authentification.service';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';  

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [MatIconModule, RouterModule, CommonModule,FormsModule],
  providers: [AuthentificationService],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent{
  passwordFieldType: string = 'password';  
  email: string = '';
  password: string = '';
  constructor(private authentificationService: AuthentificationService,  private router: Router){}

  togglePasswordVisibility(): void {
    this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
  }

   login() {
    if (this.email && this.password) {
      this.authentificationService.loginUser(this.email, this.password).subscribe({
        next: (response) => {
          console.log(response.roles[0]);
          if(response.roles[0]==='admin'){
            this.router.navigate(['/Admin']);
          }
          else if(response.roles[0]==='doctor'){
            this.router.navigate(['/Doctor']);
          }
          else {this.router.navigate(['/Home']);
        }
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
