export interface UserI {
    email: string;
    firstName: string ;
    lastName: string;
    password: string;
    roles: string;
    id: string;
    specialization: string;
    cnp:  string
  }

  export class User implements UserI{
    email: string;
    firstName: string;
    lastName: string;
    password: string
    roles: string;
    id: string;
    specialization: string;
    cnp: string

    constructor() {
      this.email = '';
      this.firstName='';
      this.lastName='';
      this.password = '';
      this.roles = '';
      this.id = '';
      this.specialization = '';
      this.cnp=''
    }
  }
