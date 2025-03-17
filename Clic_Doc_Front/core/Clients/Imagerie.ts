import ENV from '../env';
import { httpClient } from './HttpClients';
import { ElMessage } from "element-plus";

export class Imagerie {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_IMAGERIE
    public client  = new httpClient(this.baseUrl)

    public async add (payload : any ) :Promise<any>  {
        try {
            const response = await this.client.post("",payload)
            return {
                status:response.status,
                data:response.data
            }
        }
        catch(error:any )
        {
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }

    public async getAll(request : any) : Promise<any>  {
        try {
            const response = await this.client.get("?patient_id="+request.patient_id+"&page="+request.page+"&toGet="+request.toGet)
            return response.data
        }
        catch(error:any )
        {
            console.log(error)
            return {
                status:error.status,
                //data:error.response.data
            }
        }
    }
    public async update(request:any) : Promise<any> {
        try {
            const response = await this.client.put("/"+request.id,request)
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

    public async delete(id:number) : Promise<any> {
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