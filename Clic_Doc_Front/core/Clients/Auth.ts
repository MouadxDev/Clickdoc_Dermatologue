import { ElMessage } from 'element-plus';

import { useAuthStore } from '../Data/stores/auth';
import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { IPostUser } from '../Types/User/IPostUser';
import { httpClient } from './HttpClients';

export class Auth {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_AUTH
    public initiationClient  = new httpClient(this.baseUrl)
    private authStore = useAuthStore();
    public async login (credentials : IPostUser ) :Promise<IResponse>  {
        try {
            const response = await this.initiationClient.post(ENV.VITE_BACKEND_URL_LOGIN,credentials)
            ElMessage.success(response.data.message)
            this.authStore.login(response.data.user,response.data.token,response.data.entite,response.data.privileges)
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

    public async logout () : Promise<IResponse> {
        try {
            const response = await this.initiationClient.post(ENV.VITE_BACKEND_URL_LOGOUT,{})
            ElMessage.success(response.data.message)
            this.authStore.logout()
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
    
}