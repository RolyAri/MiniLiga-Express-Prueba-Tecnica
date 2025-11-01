import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class Api {
  private base = environment.API_URL;
  private http = inject(HttpClient);
  getTeams() { return this.http.get<any[]>(`${this.base}/api/teams`); }
  createTeam(payload: { name: string }) { return this.http.post(`${this.base}/api/teams`, payload); }
  getStandings() { return this.http.get<any[]>(`${this.base}/api/standings`); }
}
