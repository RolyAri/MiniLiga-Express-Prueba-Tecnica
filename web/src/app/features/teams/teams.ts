import { Component, inject, OnInit, signal } from '@angular/core';
import { Api } from '../../services/api';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgIf, NgForOf } from "@angular/common";

@Component({
  selector: 'app-teams',
  imports: [NgIf, NgForOf, ReactiveFormsModule],
  templateUrl: './teams.html',
  styleUrl: './teams.scss',
})
export class Teams implements OnInit{
  // Inyección de dependencias
  private api = inject(Api);
  private fb = inject(FormBuilder);

  teams = signal<any[]>([]);
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);

  // Formulario Reactivo
  teamForm = this.fb.group({
    name: ['', Validators.required],
  });

  ngOnInit() {
    this.loadTeams();
  }

  loadTeams() {
    this.api.getTeams().subscribe({
      next: (data) => this.teams.set(data),
      error: (err) => this.errorMessage.set('Error al cargar los equipos.'),
    });
  }

  onSubmit() {
    if (this.teamForm.invalid) {
      this.teamForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);
    const formValue = this.teamForm.value as { name: string };

    this.api.createTeam(formValue).subscribe({
      next: (newTeam) => {
        this.teams.update((currentTeams) => [...currentTeams, newTeam]);
        this.teamForm.reset();
        this.isLoading.set(false);
      },
      error: (err) => {
        this.isLoading.set(false);
        if (err.status === 422 && err.error.errors.name) {
          this.errorMessage.set(err.error.errors.name[0]);
        } else {
          this.errorMessage.set('Error al crear el equipo.');
        }
      },
    });
  }
}
