import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Soin {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_SOIN
    public client  = new httpClient(this.baseUrl)

    public async getAll(id:number) : Promise<IResponse> {
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
    public async getAllT(id:number) : Promise<IResponse> {
        try {
            const response = await this.client.get("?patient_id="+id)
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
    public async update(id:any,request:any) : Promise<IResponse> {
        try {
            const response = await this.client.put("/"+id,request)
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

    public async facture(id:any) : Promise<IResponse> {
        try {
            const response = await this.client.get("/"+id+ENV.VITE_BACKEND_URL_EDIT)
            return response.data
        }
        catch(error:any) {
            ElMessage.error("Erreur lors de l'aquisition des données")
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

}