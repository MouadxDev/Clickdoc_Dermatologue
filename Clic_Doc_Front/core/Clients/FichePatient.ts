import ENV from '../env';
import { httpClient } from './HttpClients';
import { ElMessage } from "element-plus";

export class FichePatient {

  public baseUrl = ENV.VITE_BACKEND_API + ENV.VITE_BACKEND_URL_V1 + ENV.VITE_BACKEND_URL_FICHE_PATIENT;
  public client = new httpClient(this.baseUrl);

  // Get patient basic info + paginated consultations with analyses & meds
  public async getPatientDetails(id: string, page = 1): Promise<any> {
    try {
      // The show method requires the patient id in the URL, and page as query param
      const response = await this.client.get(`/${id}?page=${page}`);
      return response.data;
    } catch (error: any) {
      console.error(error);
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Create a new patient
  public async create(payload: any): Promise<any> {
    try {
      const response = await this.client.post("", payload);
      return {
        status: response.status,
        data: response.data,
      };
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Update existing patient by id
  public async update(id: string, payload: any): Promise<any> {
    try {
      const response = await this.client.put(`/${id}`, payload);
      return {
        status: response.status,
        data: response.data,
      };
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Delete patient by id
  public async delete(id: string): Promise<any> {
    try {
      const response = await this.client.delete(`/${id}`);
      ElMessage.success(response.data.message || 'Deleted successfully');
      return {
        status: response.status,
        data: response.data,
      };
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Optionally, methods for consultations, analyses, medications if needed:

  // Get all consultations for a patient
  public async getConsultations(id: string): Promise<any> {
    try {
      const response = await this.client.get(`/${id}/consultations`);
      return response.data;
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Get all analyses for a patient
  public async getAnalyses(id: string): Promise<any> {
    try {
      const response = await this.client.get(`/${id}/analyses`);
      return response.data;
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

  // Get all medicaments for a patient
  public async getMedicaments(id: string): Promise<any> {
    try {
      const response = await this.client.get(`/${id}/medicaments`);
      return response.data;
    } catch (error: any) {
      return {
        status: error.status || 500,
        data: error.response?.data || null,
      };
    }
  }

}
