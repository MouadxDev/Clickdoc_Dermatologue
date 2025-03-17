import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Ordonnance {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_ORDONNANCE
    public client  = new httpClient(this.baseUrl)

    public async getAll(request:any = undefined) : Promise<IResponse> {
        let made_request = request==undefined ? "" :"?page="+request.page+"&toGet="+request.toGet
        if(request.patient_id)
        {
            made_request = made_request+"&patient_id="+request.patient_id
        }
        try {
            const response = await this.client.get(made_request)
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


    public async getByID(id:number) : Promise<IResponse> {
        try {
            const response = await this.client.get("/"+id)
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

    public async delete(id:number) : Promise<IResponse> {
        try {
            const response = await this.client.delete("/"+id)
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