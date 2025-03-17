import { defineStore } from 'pinia';

export const useUtilStore = defineStore("utilitaire", {
    state: () => {
      return {
        RDV:false,
        dateRDV:"",
        Patient:false,
        editPatient:false,
        patient_id:null,
        trigger:false,
      };
    },
    actions: {
      setRDV(RDV:boolean,date:string)
      {
        this.RDV=RDV
        if(date!=undefined)
        {
          this.dateRDV=date
        }
        if(RDV==false)
        {
          this.dateRDV=""
        }
      },
      setPatient(Patient:boolean)
      {
        this.Patient=Patient
      },
      setEditPatient(editPatient:boolean)
      {
        this.editPatient=editPatient
      },
      setPatientID(patient_id:any)
      {
        this.patient_id=patient_id
      },
    }
  });