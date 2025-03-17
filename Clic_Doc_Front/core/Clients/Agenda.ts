import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Agenda {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_ACGENDA
    public client  = new httpClient(this.baseUrl)

    public async getByDate(date:string) :Promise<any>{
        try {
            const response = await this.client.get("?date="+date)
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
   
    public async getByMonth(month:string) :Promise<any>{
        try {
            const response = await this.client.get("?month="+month)
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

    public async getByID(id:number) : Promise<IResponse> {
        try {
            const response = await this.client.get("/"+id)
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

    public async add(request:any) : Promise<IResponse> {
        try {
            const response = await this.client.post("",request)
            return response.data
        }
        catch(error:any) {
            ElMessage.error("Erreur lors de l'enregistrement des données")
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

    public async delete(id:number) : Promise<IResponse> {
        try {
            const response = await this.client.delete("/"+id)
            return response.data
        }
        catch(error:any) {
            ElMessage.error("Erreur lors de l'enregistrement des données")
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

}