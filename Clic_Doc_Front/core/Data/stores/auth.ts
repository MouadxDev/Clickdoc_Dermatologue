import { defineStore } from 'pinia';
import {User} from "../../Types/User/IUser"

interface State {
  user: User | any
  entite: any | null
  privileges: Array<any> 
  token: string | null
}

export const useAuthStore = defineStore("auth", {
    state: () : State => {
      return {
        user : {},
        entite:{},
        privileges:[],
        token:null
      };
    },
    actions: {
      login(user:any,token:any,entite:any,privileges:any) {
        
        this.token = token
        this.user = user
        this.entite = entite
        this.privileges = privileges
      },
      logout()
      {
        this.user = {}
        this.entite = {}
        this.token = null
      }
    },
    getters:{
      getToken(state)
      {
        return state.token
      }
    },
    persist: {
      storage: sessionStorage, // data in sessionStorage is cleared when the page session ends.
    },
  });