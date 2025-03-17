import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Reporting {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_REPORTING
    public client  = new httpClient(this.baseUrl)

    public async getAll() : Promise<IResponse> {
        try {
            const response = await this.client.get("/demographics,dates,plage-horaire,rdv-annule,billing,patients,consultations")
            return response.data
        }
        catch(error:any) {
            ElMessage.error("Erreur lors de l'acquisition des données")
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

}