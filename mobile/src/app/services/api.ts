import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class Api {
  base = environment.API_URL;
  constructor(private http: HttpClient) {}
  getPendingMatches() { return this.http.get<any[]>(`${this.base}/api/matches?played=false`); } // o mock
  reportResult(id: number, payload: { home_score: number; away_score: number }) {
    return this.http.post(`${this.base}/api/matches/${id}/result`, payload);
  }
}
