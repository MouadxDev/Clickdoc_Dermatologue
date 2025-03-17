import { defineStore } from 'pinia';


export const useConsultStore = defineStore("consult", {
    state: () : any => {
      return {
        consult:0,
        consult_uid:0,
        patient_id:0,
        examen_id:0,
        diagnostic_id:0,
        facture_id:0,
        observation_id:0,
        edit:true,
        trigger:false,
      };
    },
    actions: {
      setConsult(consult:number)
      {
        this.consult=consult
      },
      setUID(consult:string)
      {
        this.consult_uid=consult
      },
      setPatientID(id:number)
      {
        this.patient_id=id
      },
      setDiagnosticID(id:number)
      {
        this.diagnostic_id=id
      },
      setObservationID(id:number)
      {
        this.observation_id=id
      },
      setExamenID(id:number)
      {
        this.examen_id=id
      },
      setFactureID(id:number)
      {
        this.facture_id=id
      },
      setEdit(mode:boolean)
      {
        this.edit=mode
      }
    },
    persist: {
      storage: sessionStorage, // data in sessionStorage is cleared when the page session ends.
    },
  });