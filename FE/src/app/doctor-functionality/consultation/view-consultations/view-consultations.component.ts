import { Component, OnInit } from '@angular/core';
import { ConsultationService } from '../../../../services/consultation.service';
import { CommonModule } from '@angular/common';
import { ConsultationDetail } from '../../../../shared/ConsultationI';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-view-consultations',
  standalone: true,
  imports: [CommonModule, FormsModule],
  providers: [ConsultationService],
  templateUrl: './view-consultations.component.html',
  styleUrls: ['./view-consultations.component.css']
})

export class ViewConsultationsComponent implements OnInit {
  noResultsMessage: string = '';
  consultationDetail: ConsultationDetail[] = [];
  filterDoctor: string = '';
  filterSpecialization: string = '';
  filterDiagnostic: string = '';
  filterDate = new Date();
  filterMedication: string = '';
  filterConsultationId: string = '';
  filterPatientName: string = '';

  constructor(private consultationService: ConsultationService) { }

  ngOnInit(): void {
    this.loadConsultations();
  }

  loadConsultations(): void {
    this.consultationService.getConsultations().subscribe(
      (response) => {
        console.log(response)
        if (response && response.length > 0) {
          this.consultationDetail = response.map(element => ({
            id: element.id,
            specialization: element.doctor.specialization.name,
            doctorName: `${element.doctor.firstName} ${element.doctor.lastName}`,
            patientName: `${element.patient.firstName} ${element.patient.lastName}`,
            patientCnp: element.patient.cnp,
            symptoms: element.symptoms,
            diagnostic: element.diagnostic.length > 0 ? element.diagnostic[0].name : '',
            medication: element.medication,
            date: new Date(element.date)
          }));
          this.applyFilters();
        } else {
          console.error('No consultations found');
        }
      },
      (error) => {
        console.error('Error fetching consultations:', error);
      }
    );
  }

  applyFilters(): void {
    this.consultationDetail = this.consultationDetail.filter(consultation => {
      const matchesDoctor = !this.filterDoctor || consultation.doctorName.toLowerCase().includes(this.filterDoctor.toLowerCase());
      const matchesSpecialization = !this.filterSpecialization || consultation.specialization.toLowerCase().includes(this.filterSpecialization.toLowerCase());
      const matchesDiagnostic = !this.filterDiagnostic || consultation.diagnostic.toLowerCase().includes(this.filterDiagnostic.toLowerCase());
      const matchesDate = !this.filterDate || consultation.date.toDateString() === new Date(this.filterDate).toDateString();
      const matchesMedication = !this.filterMedication || consultation.medication.toLowerCase().includes(this.filterMedication.toLowerCase());
      const matchesConsultationId = this.filterConsultationId === '' || consultation.id === parseInt(this.filterConsultationId);
      const matchesPatientName = !this.filterPatientName || consultation.patientName.toLowerCase().includes(this.filterPatientName.toLowerCase());
  
      return matchesDoctor && matchesSpecialization && matchesDiagnostic && matchesDate && matchesMedication && matchesConsultationId && matchesPatientName;
    });
  }
  

  clearFilters(): void {
    this.filterDoctor = '';
    this.filterSpecialization = '';
    this.filterDiagnostic = '';
    this.filterDate = new Date();
    this.filterMedication = '';
    this.filterConsultationId ='';
    this.filterPatientName = '';
    this.loadConsultations(); 

  }
  
  clearFilter(filterName: string): void {
    switch (filterName) {
      case 'filterConsultationId':
        this.filterConsultationId = '';
        this.loadConsultations(); 
        
        break;
      case 'filterDoctor':
        this.filterDoctor = '';
        this.loadConsultations(); 

        break;
      case 'filterSpecialization':
        this.filterSpecialization = '';
        this.loadConsultations(); 

        break;
      case 'filterPatientName':
        this.filterPatientName = '';
        this.loadConsultations(); 

        break;
      case 'filterDiagnostic':
        this.filterDiagnostic = '';
        this.loadConsultations(); 

        break;
      case 'filterMedication':
        this.filterMedication = '';
        this.loadConsultations(); 

        break;
      case 'filterDate':
        this.filterDate = new Date();
        this.loadConsultations(); 

        break;
    }
    this.applyFilters(); 
  }
  
  
}
