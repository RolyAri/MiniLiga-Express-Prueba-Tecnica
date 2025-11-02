import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ReportResultPage } from './report-result.page';

describe('ReportResultPage', () => {
  let component: ReportResultPage;
  let fixture: ComponentFixture<ReportResultPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(ReportResultPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
