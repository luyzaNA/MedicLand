import { Component, OnInit } from '@angular/core';
import { MatIconModule } from '@angular/material/icon'; 
import {Router, RouterLink} from "@angular/router";
import { AuthentificationService } from '../../services/authentification.service';
import {  HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-navigation-bar',
  standalone: true,
  imports: [MatIconModule, RouterLink, CommonModule],
  providers: [AuthentificationService],
  templateUrl: './navigation-bar.component.html',
  styleUrl: './navigation-bar.component.css'
})
export class NavigationBarComponent implements OnInit {
  isAuthenticated: boolean = false;

  constructor(private router: Router,    private authentificationService: AuthentificationService) {}
  navigateToHome() {
    this.router.navigate(['/Home']);
  }
  navigateToAuth(){
    this.router.navigate(['/Login']);
  }

  ngOnInit(): void {
    this.isAuthenticated = !!localStorage.getItem('token');
  }

  logout(): void {
    localStorage.removeItem('token');
    this.isAuthenticated = false;

    this.router.navigate(['/Home']);
  }

}
