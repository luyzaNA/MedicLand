import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { FirstPageComponent } from './first-page/first-page.component';
import { RegisterComponent } from './authentification/register/register.component';
import { NavigationBarComponent } from './navigation-bar/navigation-bar.component';
import { LoginComponent } from './authentification/login/login.component';
import { FooterComponent } from '../footer/footer.component';
import { ErrorModalComponent } from './error-modal/error-modal.component';
import { AddPatientComponent } from "./doctor-functionality/patient/add-patient/add-patient.component";

@Component({
    selector: 'app-root',
    standalone: true,
    templateUrl: './app.component.html',
    styleUrl: './app.component.css',
    imports: [RouterOutlet, FirstPageComponent, RegisterComponent, NavigationBarComponent, LoginComponent, FooterComponent, ErrorModalComponent, AddPatientComponent]
})
export class AppComponent {
  title = 'FE';
}
