import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class LaboMedicament {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_LAB_MEDICAMENT
    public client  = new httpClient(this.baseUrl)
    public async getByID ( id : number ) :Promise<IResponse>  {
        try {
            const response = await this.client.get("/"+id)
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