import {Component, OnInit} from '@angular/core';
import {Router, RouterLink} from "@angular/router";
import {User} from "../../../shared/UserI";
import {AuthentificationService} from "../../../services/authentification.service";

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    RouterLink
  ],
  providers: [AuthentificationService],
  templateUrl: './dashboard-doctor.component.html',
  styleUrl: './dashboard-doctor.component.css'
})
export class DashboardDoctorComponent implements OnInit{
  doctor: User = new User();
  name: string = '';

  constructor(private authentificationService: AuthentificationService, private router: Router) {}

  ngOnInit(): void {
    this.authentificationService.getCurrentUser().subscribe(user => {
      this.doctor = user;

      if (this.doctor.lastName && this.doctor.firstName) {
        this.name = this.doctor.firstName + ' ' + this.doctor.lastName;
      } else {
        this.name = '';
      }
    });
  }
}
