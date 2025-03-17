import axios from "axios"
import { useAuthStore } from "../Data/stores/auth";
import { storeToRefs } from "pinia";

export class httpClient {

    public baseUrl : string ;
    private authStore = storeToRefs(useAuthStore());
    private config = {
        headers:{
            Authorization: `Bearer ${this.authStore.token.value}`
        }
    }

    public constructor(baseUrl:string){
        this.baseUrl = baseUrl
    }

    public async post(url:string,parameters:any){
        return await axios.post(this.baseUrl+url,parameters,this.config);
    }

    public async put(url:string,parameters:any){
        return await axios.put(this.baseUrl+url,parameters,this.config);
    }

    public async get(url:string){
        return await axios.get(this.baseUrl+url,this.config);
        
    }

    public async delete(url:string){
        return await axios.delete(this.baseUrl+url,this.config)
    }
 }