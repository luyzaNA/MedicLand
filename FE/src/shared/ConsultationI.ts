import { Disease } from "./DiseaseI";

export interface ConsultationDetail {
    id: number;
    specialization: string;
    doctorName: string;
    patientName: string;
    patientCnp: string;
    symptoms: string;
    diagnostic: string;
    medication: string;
    date: Date;
}
export interface ConsultationPatientI {
    id: number;
    date: string; 
    medication: string;
    symptoms: string;
    diagnostic: { name: string;
    description: string;
    category: string;
  }[];
  
    doctorEmail: string;
    doctorFirstName: string | null;
    doctorLastName: string | null;
    doctorSpecializationName: string;
  
    patientCnp: string;
    patientFirstName: string;
    patientLastName: string;
    patientBirthDate: string; 
    patientAge: number;
    patientAddress: string;
    patientEmail: string;
    patientPhone: string;
    patientLocality: string;
    patientBloodGroup: string;
    patientRh: string;
    patientWeight: number;
    patientHeight: number;
    patientAllergies: string;
    patientOccupation: string;
    patientRecordDate: string; 
    patientSex: string;
  
    patientDiseases: {
      name: string;
      description: string;
      category: string;
    }[];
  }
  
export interface ConsultationI {
    patientCnp: string;
    diseases: Disease[];
    medication?: string;
    symptoms?: string;
    date: Date; 
    id: number;
}

export class Consultation implements ConsultationI {
    patientCnp: string;
    diseases: Disease[];
    medication?: string;
    symptoms?: string;
    date!: Date; 
    id!: number;


    constructor() {
       this.patientCnp = '';
       this.diseases = [];
       this.symptoms = '';
       this.medication = '';
        }

}