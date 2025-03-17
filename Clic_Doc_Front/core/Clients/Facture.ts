import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Facture {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_FACTURE
    public client  = new httpClient(this.baseUrl)

    public async getAll() : Promise<IResponse> {
        try {
            const response = await this.client.get("/create")
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
    
    public async getThings(id:number): Promise<IResponse> {
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
    public async update(request:any) : Promise<IResponse> {
        try {
            const response = await this.client.put("/"+request.id,request)
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
            const response = await this.client.get("/"+id)
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
    public async deets(id:any) : Promise<IResponse> {
        try {
            const response = await this.client.get("/"+id+"/edit")
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