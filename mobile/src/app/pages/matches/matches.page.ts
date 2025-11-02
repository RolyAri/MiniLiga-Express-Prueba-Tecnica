import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule, NgForOf, NgIf } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent, IonHeader, IonTitle, IonToolbar, IonRefresher, IonRefresherContent, IonList, IonItem, IonLabel, IonSpinner, RefresherEventDetail } from '@ionic/angular/standalone';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { Api } from 'src/app/services/api';

@Component({
  selector: 'app-matches',
  templateUrl: './matches.page.html',
  styleUrls: ['./matches.page.scss'],
  standalone: true,
  imports: [IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule, IonRefresher, IonRefresherContent, IonList, IonItem, IonLabel,RouterLink, IonSpinner, NgIf, NgForOf]
})
export class MatchesPage implements OnInit {

  constructor() { }

  ngOnInit() {
    this.loadPendingMatches();
  }

  private api = inject(Api);

  // Signals para el estado
  matches = signal<any[]>([]);
  isLoading = signal(true);

  loadPendingMatches(event?: CustomEvent<RefresherEventDetail>) {

    if (!event) {
      this.isLoading.set(true); 
    }

    this.api.getPendingMatches().subscribe({
      next: (data) => {
        this.matches.set(data);
        this.isLoading.set(false);
        event?.detail.complete();
      },
      error: (err) => {
        console.error('Error cargando partidos', err);
        this.isLoading.set(false);
        event?.detail.complete();
      },
    });
  }
}
