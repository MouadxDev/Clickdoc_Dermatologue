import { ElMessage } from 'element-plus';
import ENV from '../env';
import { httpClient } from './HttpClients';

export class TableauPersonnalise {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_TABLEAU
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
   
    public async update (payload : any ) :Promise<any>  {
        try {
            const response = await this.client.put("/"+payload.id,payload)
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

    public async getByDate(date:string) :Promise<any>{
        try {
            const response = await this.client.get("/create?date="+date)
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
            const response = await this.client.get("/create?month="+month)
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

    public async getAll(request : any) : Promise<any>  {
        try {
            const response = await this.client.get("?patient_id="+request.patient_id+"&page="+request.page+"&toGet="+request.toGet)
            return {
                data:response.data
            }
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
   
    public async isToday(request ?: any ) : Promise<any> {
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