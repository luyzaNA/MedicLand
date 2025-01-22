import { Component } from '@angular/core';
import { UserService } from '../../../services/user.service';
import { User, UserI } from '../../../shared/UserI';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ConfirmDialogComponent } from '../../../shared/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-delete-user',
  standalone: true,
  imports: [MatDialogModule, FormsModule, CommonModule],
  providers: [UserService],
  templateUrl: './delete-user.component.html',
  styleUrls: ['./delete-user.component.css']
})
export class DeleteUserComponent {
  users: UserI[] = [];
  searchEmail: string = '';
  showUsers: boolean = false;
  successMessage: string | null = null;  

  constructor(private userService: UserService, private dialog: MatDialog) {}

  loadUsers() {
    this.userService.getUsers().subscribe(users => {
      this.users = users;
      this.showUsers = true; 
    });
  }

  searchByEmail() {
    if (this.searchEmail.length >= 3) {  
      this.userService.getUsers().subscribe(users => {
        this.users = users.filter(user => user.email.includes(this.searchEmail));
        this.showUsers = true; 
      });
    } else {
      this.showUsers = false;
    }
  }

  confirmDelete(email: string): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '250px',
      data: { data: email }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === 'yes') {
        this.deleteUser(email);
      }
    });
  }

  deleteUser(email: string): void {
    this.userService.deleteUser(email).subscribe(
      () => {
        this.searchEmail = ''
        this.users = this.users.filter(user => user.email !== email);

        this.successMessage = 'User successfully deleted!';
        setTimeout(() => {
          this.successMessage = null;
        }, 3000);
      },
      error => {
        console.error('Eroare la ștergerea utilizatorului:', error);
      }
    );
  }
}
