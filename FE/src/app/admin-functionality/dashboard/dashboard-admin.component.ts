import { HttpClientModule } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { AuthentificationService } from '../../../services/authentification.service';
import { User } from '../../../shared/UserI';

@Component({
  selector: 'app-dashboard-admin',
  standalone: true,
  imports: [ RouterModule],
  providers: [AuthentificationService],
  templateUrl: './dashboard-admin.component.html',
  styleUrl: './dashboard-admin.component.css'
})
export class AdminDashboardComponent implements OnInit {
  admin: User = new User();
  name: string = '';

  constructor(private authentificationService: AuthentificationService, private router: Router) {}

  ngOnInit(): void {
    this.authentificationService.getCurrentUser().subscribe(user => {
      this.admin = user; 

      if (this.admin.email) {
        this.name = this.admin.email.split('@')[0]; 
      } else {
        this.name = ''; 
      }
    });
  }
}
