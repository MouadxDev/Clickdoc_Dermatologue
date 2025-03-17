import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Demande {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_DEMANDE_ANALYSES
    public client  = new httpClient(this.baseUrl)

    public async getAll(request : any = undefined) : Promise<any>  {
        try {
            const response = await this.client.get(request==undefined ? "" :"?page="+request.page+"&toGet="+request.toGet)
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
    public async getByID(id : number) : Promise<any>  {
        try {
            const response = await this.client.get("/"+id+"/edit")
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
    public async getByConsult(id : number) : Promise<any>  {
        try {
            const response = await this.client.get("/"+id)
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