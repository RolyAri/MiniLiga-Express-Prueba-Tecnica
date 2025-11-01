import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { Teams } from './features/teams/teams';
import { Standings } from './features/standings/standings';

const routes: Routes = [
  { path: '', redirectTo: 'teams', pathMatch: 'full' },
  { path: 'teams', component: Teams },
  { path: 'standings', component: Standings },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
