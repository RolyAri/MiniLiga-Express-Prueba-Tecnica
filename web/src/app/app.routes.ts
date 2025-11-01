import { Routes } from '@angular/router';
import { Teams } from './features/teams/teams';
import { Standings } from './features/standings/standings';

export const routes: Routes = [
    { path: '', redirectTo: 'teams', pathMatch: 'full' },
    { path: 'teams', component: Teams },
    { path: 'standings', component: Standings },
];
