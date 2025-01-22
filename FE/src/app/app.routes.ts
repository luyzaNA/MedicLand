import { Routes } from '@angular/router';
import { FirstPageComponent } from './first-page/first-page.component';
import { LoginComponent } from './authentification/login/login.component';
import { RegisterComponent } from './authentification/register/register.component';
import { RoleGuard } from '../services/auth-guard.service';
import { AdminDashboardComponent } from './admin-functionality/dashboard/dashboard-admin.component';
import { SpecializationManagementComponent } from './admin-functionality/specialization-management/specialization-management.component';
import { AccountsManagementComponent } from './admin-functionality/accounts-management/accounts-management.component';
import { EditUserComponent } from './admin-functionality/edit-user/edit-user.component';
import { DeleteUserComponent } from './admin-functionality/delete-user/delete-user.component';
import { CreateUserComponent } from './admin-functionality/create-user/create-user.component';
import {DashboardDoctorComponent} from "./doctor-functionality/dashboard/dashboard-doctor.component";
import {AddPatientComponent} from "./doctor-functionality/patient/add-patient/add-patient.component";
import {PatientManagementComponent} from "./doctor-functionality/patient/patient-management/patient-management.component";
import { EditPatientComponent } from './doctor-functionality/patient/edit-patient/edit-patient.component';
import { DeletePatientComponent } from './doctor-functionality/patient/delete-patient/delete-patient.component';
import { AddConsultationComponent } from './doctor-functionality/consultation/add-consultation/add-consultation.component';
import { ConsultationManagementComponent } from './doctor-functionality/consultation/consultation-management/consultation-management.component';
import { DeleteConsultationComponent } from './doctor-functionality/consultation/delete-consultation/delete-consultation.component';
import { ViewConsultationsComponent } from './doctor-functionality/consultation/view-consultations/view-consultations.component';
import { ViewPatientsComponent } from './doctor-functionality/patient/view-patients/view-patients.component';
import { SearchPatientComponent } from './doctor-functionality/patient/search-patient/search-patient.component';
import { ReportsManagementComponent } from './doctor-functionality/reports/reports-management/reports-management.component';
import { PatientRecordComponent } from './doctor-functionality/reports/patient-record/patient-record.component';
import { DiseaseRecordComponent } from './doctor-functionality/reports/disease-record/disease-record.component';
import { ConsultationRecordComponent } from './doctor-functionality/reports/consultation-record/consultation-record.component';
import { StaticticManagementComponent } from './doctor-functionality/statistics/statictic-management/statictic-management.component';
import { SpecializationStatisticComponent } from './doctor-functionality/statistics/specialization-statistic/specialization-statistic.component';
import { DiseaseStatisticComponent } from './doctor-functionality/statistics/disease-statistic/disease-statistic.component';

export const routes: Routes = [
    { path: '', component: FirstPageComponent },
    { path: 'Home', component: FirstPageComponent },
    { path: 'Login', component: LoginComponent },
    { path: 'Register', component: RegisterComponent },
    {
        path: 'Admin', children: [
            {
                path: '', component: AdminDashboardComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'admin' }
            },
            {
                path: 'Specialization', component: SpecializationManagementComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'admin' }
            },
            {
                path: 'Accounts', component: AccountsManagementComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'admin' },
                children: [
                    {
                        path: 'Edit', component: EditUserComponent,
                        canActivate: [RoleGuard],
                        data: { expectedRole: 'admin' },
                    },
                    {
                        path: 'Delete', component: DeleteUserComponent,
                        canActivate: [RoleGuard],
                        data: { expectedRole: 'admin' },
                    },
                    {
                      path: 'Create', component: CreateUserComponent,
                      canActivate: [RoleGuard],
                      data: { expectedRole: 'admin' },
                  }
                ]
            }
        ]
    },
  {
    path: 'Doctor', children: [
      {
        path: '', component: DashboardDoctorComponent,
        canActivate: [RoleGuard],
        data: {expectedRole: 'doctor'}
      },
      {
        path: 'Patients', component: PatientManagementComponent,
        canActivate: [RoleGuard],
        data: {expectedRole: 'doctor'},
        children:[
            {
                path: 'Create', component: AddPatientComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' },
            },
            {
              path: 'Edit', component: EditPatientComponent,
              canActivate: [RoleGuard],
              data: { expectedRole: 'doctor' },
          },
          {
            path: 'Delete', component: DeletePatientComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
          },
          
          {
            path: 'View', component: ViewPatientsComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
          },
          {
            path: 'Search', component: SearchPatientComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
          }
          ]
      },

      {
        path: 'Consultations', component: ConsultationManagementComponent,
        canActivate: [RoleGuard],
        data: {expectedRole: 'doctor'},
        children:[
            {
                path: 'Add', component: AddConsultationComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' },
            },
            {
              path: 'Edit', component: EditPatientComponent,
              canActivate: [RoleGuard],
              data: { expectedRole: 'doctor' },
          },
          {
            path: 'Delete', component: DeleteConsultationComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
        },
        
          {
            path: 'View', component: ViewConsultationsComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
        }
          
          ]
      },
      {
        path: 'Reports',
        children:[
            {
                path: '', component: ReportsManagementComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' }
            },
            {
                path: 'Patient', component: PatientRecordComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' },
            },
            {
              path: 'Disease', component: DiseaseRecordComponent,
              canActivate: [RoleGuard],
              data: { expectedRole: 'doctor' },
          },
          {
            path: 'Consultation', component: ConsultationRecordComponent,
            canActivate: [RoleGuard],
            data: { expectedRole: 'doctor' },
        }
          ]
      }, {
        path: 'Statistic',
        children:[
            {
                path: '', component: StaticticManagementComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' }
            },
            {
                path: 'Specialization', component: SpecializationStatisticComponent,
                canActivate: [RoleGuard],
                data: { expectedRole: 'doctor' },
            },
            {
              path: 'Disease', component: DiseaseStatisticComponent,
              canActivate: [RoleGuard],
              data: { expectedRole: 'doctor' },
          }
          ]
      },
    ]
  }
];
