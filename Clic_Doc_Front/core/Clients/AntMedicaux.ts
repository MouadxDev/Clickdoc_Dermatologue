import { ElMessage } from 'element-plus';

import ENV from '../env';
import { httpClient } from './HttpClients';

export class AntMedicaux {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_PATIENT_ANT_MED
    public client  = new httpClient(this.baseUrl)


    public async getByID(id:number) : Promise<any> {
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

}