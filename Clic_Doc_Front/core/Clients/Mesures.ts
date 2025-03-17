import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Mesure {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_MESURES
    public client  = new httpClient(this.baseUrl)
    public async getByID ( id : number ) :Promise<IResponse>  {
        try {
            const response = await this.client.get("/"+id+"/edit")
            return response.data
        }
        catch(error:any )
        {
            ElMessage.error(error.response.data)
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

    public async getAll(patient_id : number) : Promise<any>  {
        try {
            const response = await this.client.get("/"+patient_id)
            return response.data
        }
        catch(error:any )
        {
            console.log(error)
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

    public async add(request:any) : Promise<IResponse> {
        try {
            const response = await this.client.post("",request)
            return response.data
        }
        catch(error:any) {
            ElMessage.error("Erreur lors de l'enregistrement des sonnées")
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }
}