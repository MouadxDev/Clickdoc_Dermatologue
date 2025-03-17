import { ElMessage } from 'element-plus';

import ENV from '../env';


import { httpClient } from './HttpClients';
import { useRouter } from 'vue-router';

export class Initiation {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_INITIATE
    public client  = new httpClient(this.baseUrl)
    private router = useRouter()
    
    public async request ( ) :Promise<any>  {
        try {
            const response = await this.client.post(ENV.VITE_BACKEND_URL_REQUEST,{})
            if(response.data.message == true)
            {
                return true
            }
            else
            {
                return this.router.push("/login");
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
    
    public async go ( request:any ) :Promise<any>  {
        try {
            const response = await this.client.post(ENV.VITE_BACKEND_URL_GO,request)
            ElMessage.success(response.data.message)
            return this.router.push("/login");
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
    
}