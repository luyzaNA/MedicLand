import { Component, OnInit, OnDestroy } from '@angular/core';
import { NavigationBarComponent } from '../navigation-bar/navigation-bar.component';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { SpecializationDoctorComponent } from '../specialization-doctor/specialization-doctor.component';
import { FooterComponent } from '../../footer/footer.component';

@Component({
  selector: 'app-first-page',
  standalone: true,
  imports: [NavigationBarComponent, MatIconModule, MatCardModule, CommonModule,
           SpecializationDoctorComponent, FooterComponent],
  templateUrl: './first-page.component.html',
  styleUrls: ['./first-page.component.css']
})
export class FirstPageComponent implements OnInit, OnDestroy {

  sliderImages = [
    { url: '../assets/slider/img1.jpg', alt: 'Imagine 1' },
    { url: '../assets/slider/img2.jpg', alt: 'Imagine 2' },
    { url: '../assets/slider/img3.jpg', alt: 'Imagine 3' }
  ];

  constructor() { }


  currentSlide = 0;
  slideInterval: any;

  nextSlide() {
    this.currentSlide = (this.currentSlide + 1) % this.sliderImages.length;
  }

  prevSlide() {
    this.currentSlide =
      (this.currentSlide - 1 + this.sliderImages.length) %
      this.sliderImages.length;
  }

  startAutoSlide() {
    this.slideInterval = setInterval(() => {
      this.nextSlide(); 
    }, 5000); 
  }

  stopAutoSlide() {
    if (this.slideInterval) {
      clearInterval(this.slideInterval);
    }
  }

  ngOnInit() {
    this.startAutoSlide();
  }

  ngOnDestroy() {
    this.stopAutoSlide();
  }

  onSliderInteraction() {
    this.stopAutoSlide(); 
    this.startAutoSlide(); 
  }


}
