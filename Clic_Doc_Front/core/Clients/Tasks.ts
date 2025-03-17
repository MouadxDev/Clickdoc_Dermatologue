import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Tasks {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_TASK
    public client  = new httpClient(this.baseUrl)
    public async getByID ( id : number ) :Promise<any>  {
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

    public async update(request: any): Promise<IResponse> {
        try {          
            // Use request instead of toSend
            const response = await this.client.put("/" + request.id, request);
            ElMessage.success("Enregistrement effectué");
            return response.data;
        } catch (error: any) {
            // Ensure error handling checks for the response
            let errorMessage = "Erreur lors de l'enregistrement des sonnées";
            if (error.response) {
                errorMessage += ": " + (error.response.data || "Unknown error occurred");
            }
            ElMessage.error(errorMessage);
            return {
                status: error.response ? error.response.status : 500, // Fallback status
                data: error.response ? error.response.data : { message: "An unexpected error occurred" }
            };
        }
    }
    
    public async delete(id: string): Promise<IResponse> {
        try {     

            const response = await this.client.delete(`/${id}`);
            ElMessage.success("Task deleted successfully");
            
            return response.data;
        } catch (error: any) {
            ElMessage.error("Error deleting the task");
            
            return {
                status: error.response ? error.response.status : 500, // Fallback to 500 if no response
                data: error.response ? error.response.data : { message: "An unexpected error occurred" }
            };
        }
    }
    
}