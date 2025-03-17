import { defineStore } from 'pinia';

export const useRdvStore = defineStore("rendez-vous", {
    state: () => {
      return {
        rdv_id:0,
        rendezVous:[],
        salleAttente:[],
        trigger:false,
      };
    },
    actions: {
      setRDV(rdv_id:number)
      {
        this.rdv_id=rdv_id
      },
      setRDVList(rendezVous:any)
      {
        this.rendezVous=rendezVous
      },
      setSalleAttente(salleAttente:any)
      {
        this.salleAttente=salleAttente
      },
      setTrigger(trigger : boolean) {
        this.trigger = trigger
      }
    }
  });