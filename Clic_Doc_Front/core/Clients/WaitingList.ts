import ENV from '../env';
import { httpClient } from './HttpClients';

export class WaitingList {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_WAITING_LIST
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
            const response = await this.client.get("?only="+request.patient_id+"&page="+request.page+"&toGet="+request.toGet)
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
   
    public async isWaiting(request : any) : Promise<any> {
        try {
            const response = await this.client.post(ENV.VITE_BACKEND_URL_REQUEST,request)
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

    public async changeStatus(id:any,status:string) : Promise<any> {
        try {
            const response = await this.client.put("/"+id,{status:status})
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

}