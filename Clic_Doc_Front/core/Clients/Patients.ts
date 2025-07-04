import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IRequest } from '../Types/IRequest';
import { IResponse } from '../Types/IResponse';
import { Patient } from '../Types/Patient/Patient';
import { httpClient } from './HttpClients';

export class Patients {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_PATIENT
    public patientClient  = new httpClient(this.baseUrl)
    public async add (patient : Patient ) :Promise<IResponse>  {
        try {
            const response = await this.patientClient.post("",patient)
            ElMessage.success(response.data.message)
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

    public async getAll(request : IRequest ) : Promise<any>  {
        try {
            const response = await this.patientClient.get(request==undefined ? "" :"?page="+request.page+"&toGet="+request.toGet)
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

    public async getByUID(uid:any) : Promise<any> {
        try {
            const response = await this.patientClient.get("/"+uid)
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
    
    public async search(uid:string) : Promise<any> {
        try {
            const response = await this.patientClient.get(ENV.VITE_BACKEND_URL_SEARCH+"/"+uid)
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
            const response = await this.patientClient.put("/"+request.id,request)
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
    
    public async delete(uid:any) : Promise<any> {
        try {
            const response = await this.patientClient.delete("/"+uid)
            ElMessage.success(response.data.message)
            return {
                status:response.status,
                data:response.data
            }
        }
        catch(error:any )
        {
            console.log(error)
            ElMessage.error(error.response.data)
            return {
                status:error.status,
                data:error.response.data
            }
        }
    }
    
    

}