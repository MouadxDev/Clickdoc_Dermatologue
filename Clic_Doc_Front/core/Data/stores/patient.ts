import { defineStore } from 'pinia';

export const usePatientStore = defineStore("patients", {
    state: () => {
      return {
        rdv_id:0,
        trigger:false,
      };
    },
    actions: {
      setPatient(rdv_id:number)
      {
        this.rdv_id=rdv_id
      },
      setTrigger(trigger : boolean) {
        this.trigger = trigger
      }
    }
  });