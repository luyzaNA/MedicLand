import { Disease } from "./DiseaseI";

export  enum BloodGroup {
    A = 'A',
    B = 'B',
    O = 'O',
    AB = 'AB',
}

export enum RhFactor {
    NEGATIV = 'NEGATIV',
    POZITIV = 'POZITIV',
}
export interface PatientDetail {
    cnp: string;
    fullName: string;
    age: number;
    bloodGroup?: BloodGroup; 
    rh: RhFactor; 
    weight?: number;
    height?: number;
    allergies: string;
    occupation : string;
    sex: string;
}

export interface PatientI {
    email?: string;
    cnp: string;
    nr?: number;
    firstName: string;
    lastName: string;
    birthDate: Date; 
    age: number;
    locality: string;
    address: string;
    phone: string;
    bloodGroup?: BloodGroup; 
    rh: RhFactor; 
    weight?: number;
    height?: number;
    allergies: string;
    occupation : string;
    sex: string;
    diseases?: Disease[]; 
    recordDate: Date;
}
export interface PatientIAdd {
    email?: string;
    cnp: string;
    nr?: number;
    firstName: string;
    lastName: string;
    locality: string;
    address: string;
    phone: string;
    bloodGroup?: BloodGroup; 
    rh: RhFactor; 
    weight?: number;
    height?: number;
    allergies: string;
    occupation : string;
    diseases?: Disease[]; 
    recordDate: Date;
}

export class Patient implements PatientI {
    email?: string;
    cnp: string;
    nr?: number;
    firstName: string;
    lastName: string;
    birthDate!: Date;
    age!: number;
    locality: string;
    address: string;
    phone: string;
    bloodGroup?: BloodGroup;
    rh!: RhFactor;
    weight!: number;
    height!: number;
    allergies: string;
    occupation: string;
    sex: string;
    diseases?: Disease[];
    recordDate!: Date;

    constructor() {
        this.cnp = '';
        this.firstName = '';
        this.lastName = '';
        this.locality = '';
        this.address='';
        this.phone='';
        this.occupation='';
        this.sex='';
        this.allergies ='';
        this.diseases = [];
    }
}
