import { Component, OnInit } from '@angular/core';
import { SpecializationService } from '../../services/specialization.service';
import { UserService } from '../../services/user.service';
import { DoctorDetails } from '../../shared/DoctorDetailsI';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-specialization-doctor',
  standalone: true,
  imports: [CommonModule,MatIconModule],
  providers: [SpecializationService, UserService],
  templateUrl: './specialization-doctor.component.html',
  styleUrls: ['./specialization-doctor.component.css']
})
export class SpecializationDoctorComponent implements OnInit {
  specializations: { name: string; doctors: DoctorDetails[] }[] = [];

  doctorFirstName: string = '';
  doctorLastName: string = '';
  doctorEmail: string = '';
  selectedDoctorFirstName: string = '';
  selectedDoctorLastName: string = '';
  selectedDoctorEmail: string = '';

  constructor(
    private specializationService: SpecializationService,
    private userService: UserService
  ) {}

  ngOnInit(): void {
    this.loadSpecializationsWithDoctors();
  }

  loadSpecializationsWithDoctors(): void {
    this.specializationService.getAllSpecializations().subscribe({
      next: (specializations) => {
        const requests = specializations.map((spec) =>
          this.userService
            .getDoctorBySpecialization(spec.name)
            .toPromise()
            .then((doctors) => ({
              name: spec.name,
              doctors: doctors || [],
            }))
        );

        Promise.all(requests).then((results) => {
          this.specializations = results;
        });
      },
      error: (error) => {
        console.error('Error loading specializations:', error);
      },
    });
  }

  selectDoctor(doctor: DoctorDetails): void {
    this.selectedDoctorFirstName = doctor.firstName;
    this.selectedDoctorLastName = doctor.lastName;
    this.selectedDoctorEmail = doctor.email;
  }
}
