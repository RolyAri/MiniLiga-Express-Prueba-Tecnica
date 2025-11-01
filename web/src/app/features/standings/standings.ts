import { Component, inject, signal } from '@angular/core';
import { NgIf, NgForOf } from "@angular/common";
import { Api } from '../../services/api';

@Component({
  selector: 'app-standings',
  imports: [NgIf, NgForOf],
  templateUrl: './standings.html',
  styleUrl: './standings.scss',
})
export class Standings {
  // Inyección de dependencias
  private api = inject(Api);

  standings = signal<any[]>([]);
  isLoading = signal(true);

  ngOnInit() {
    this.loadStandings();
  }

  loadStandings() {
    this.isLoading.set(true);
    this.api.getStandings().subscribe({
      next: (data) => {
        this.standings.set(data);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error loading standings', err);
        this.isLoading.set(false);
      },
    });
  }
}
