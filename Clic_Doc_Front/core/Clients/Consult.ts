import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Consultation {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_CONSULTATION
    public client  = new httpClient(this.baseUrl)
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

    public async getOne ( id : any ) :Promise<IResponse>  {
        try {
            const response = await this.client.get("/"+id)
            
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
    
    public async getMissing ( ) :Promise<IResponse>  {
        try {
            const response = await this.client.get("/create")
            return response.data;
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

    public async getAll(request:any) : Promise<IResponse> {
        let made_request = "?page="+request.page+"&toGet="+request.toGet
        if(request.patient_id!="all")
        {
            made_request = made_request+"&patient_id="+request.patient_id
        }
        if(request.date!=null)
        {
            made_request = made_request+"&date="+request.date
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
}