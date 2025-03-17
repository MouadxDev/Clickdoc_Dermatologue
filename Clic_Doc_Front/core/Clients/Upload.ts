import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IRequest } from '../Types/IRequest';
import { IResponse } from '../Types/IResponse';
import { User } from '../Types/User/User';
import { httpClient } from './HttpClients';

export class Upload {

    public baseUrl = ENV.VITE_BACKEND_API+ENV.VITE_BACKEND_URL_V1+ENV.VITE_BACKEND_URL_UPLOAD
    public uploadClient  = new httpClient(this.baseUrl)
    public async add (file : any ) :Promise<IResponse>  {
        try {
            const response = await this.uploadClient.post("",file)
            //ElMessage.success(response.data.message)
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