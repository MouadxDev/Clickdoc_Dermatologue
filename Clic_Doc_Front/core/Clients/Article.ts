import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Article {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_ARTICLE
    public client  = new httpClient(this.baseUrl)

    public async getAll(request : any) : Promise<IResponse> {
        try {
            const response = await this.client.get("?patient_id="+request.patient_id)
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


    public async add ( request : any ) :Promise<IResponse>  {
        try {
            const response = await this.client.post("",request)
            
            return {
                status:response.status,
                data:response.data
            }
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

    public async update(request:any) : Promise<any> {
        try {
            const response = await this.client.put("/"+request.id,request)
            ElMessage.success(response.data.message)
            return {
                status:response.status,
                data:response.data
            }
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
    
    public async delete(id:any) : Promise<any> {
        try {
            const response = await this.client.delete("/"+id)
            ElMessage.success(response.data.message)
            return {
                status:response.status,
                data:response.data
            }
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
}