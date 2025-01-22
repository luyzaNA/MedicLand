export enum DiseaseCategory {
    Infectious = 'Infectious',
    Chronic = 'Chronic',
    Genetic = 'Genetic',
    Autoimmune = 'Autoimmune',
    OTOtherHER = 'Other',
}



export interface DiseaseI {
    name: string;
    description?: string;
    category: DiseaseCategory;
}

export class Disease implements DiseaseI {
    name: string;
    description?: string;
    category!: DiseaseCategory;

    constructor() {
        this.name = '';
        this.description = '';
    }

}