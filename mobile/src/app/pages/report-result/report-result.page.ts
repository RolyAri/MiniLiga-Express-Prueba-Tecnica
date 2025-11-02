import { Component, OnInit, signal } from '@angular/core';
import { CommonModule, NgIf } from '@angular/common';
import { FormBuilder, FormsModule, Validators, ReactiveFormsModule, FormGroup } from '@angular/forms';
import { IonContent, IonHeader, IonTitle, IonToolbar, IonItem, IonLabel, IonButton, IonNote, IonInput, IonSpinner, IonButtons, IonBackButton } from '@ionic/angular/standalone';
import { ActivatedRoute, Router } from '@angular/router';
import { Api } from 'src/app/services/api';
import { AlertController } from '@ionic/angular';
import { finalize } from 'rxjs';
import { Camera, CameraResultType } from '@capacitor/camera';

@Component({
  selector: 'app-report-result',
  templateUrl: './report-result.page.html',
  styleUrls: ['./report-result.page.scss'],
  standalone: true,
  imports: [IonContent, IonHeader, IonTitle, IonToolbar, CommonModule, FormsModule, ReactiveFormsModule, IonItem, IonLabel, IonButton, IonNote, NgIf, IonInput, IonSpinner, IonButtons, IonBackButton]
})
export class ReportResultPage implements OnInit {
    form!: FormGroup;
    matchId!: number;
    isLoading = signal(false);
    photo?: string | null;

  constructor(
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private api: Api,
    private alertCtrl: AlertController,
    private router: Router
  ) {}

  ngOnInit() {
    this.matchId = Number(this.route.snapshot.paramMap.get('id'));

    // Formulario reactivo
    this.form = this.fb.group({
      home_score: [null, [Validators.required, Validators.min(0)]],
      away_score: [null, [Validators.required, Validators.min(0)]],
    });
  }

  async takePhoto() {
    const image = await Camera.getPhoto({
      quality: 70,
      resultType: CameraResultType.DataUrl,
    });
    this.photo = image.dataUrl ?? null;
  }

  async submitResult() {

    if (this.form.invalid) {
      const alert = await this.alertCtrl.create({
        header: 'Formulario inválido',
        message: 'Por favor, completa correctamente los campos.',
        buttons: ['OK'],
      });
      await alert.present();
      return;
    }

    this.isLoading.set(true);

    const payload = this.form.value;

    this.api.reportResult(this.matchId, payload)
    .pipe(
      finalize(() => this.isLoading.set(false))
    )
    .subscribe({
      next: async () => {
        const alert = await this.alertCtrl.create({
          header: 'Éxito',
          message: 'Resultado reportado correctamente',
          buttons: ['OK'],
        });
        await alert.present();
        this.router.navigate(['/matches']);
      },
      error: async (err) => {
        console.error(err);
        const alert = await this.alertCtrl.create({
          header: 'Error',
          message: 'No se pudo enviar el resultado. Inténtalo nuevamente.',
          buttons: ['OK'],
        });
        await alert.present();
      },
    });
  }
}
