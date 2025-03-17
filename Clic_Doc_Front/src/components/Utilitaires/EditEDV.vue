<script setup lang="ts">
import { useUtilStore } from '../../../core/Data/stores/utilitaire';
import { Ref,reactive,ref , watch } from 'vue';
import { Patients } from '../../../core/Clients/Patients';
import { RendezVous } from '../../../core/Clients/RendezVous';
import { RdvService } from '../../../core/Data/services/rendez-vous'

const util = useUtilStore();


const client = new RendezVous()
const patientClient = new Patients();
const service = new RdvService();

const patients:Ref<Array<any>> = ref([])
const patient:Ref<any> = ref({})
const filter = ref("")
const data = reactive([])

const rdv : Ref<any> = ref({
    doctor_id:1,
    patient_id:"",
    date:"",
    heure:"",
    type:"",
    statut:""
})

const heures = ["06:00","06:30","07:00","07:30","08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30","20:00"]

async function searchThings()
{
    patients.value = await patientClient.search(filter.value)
}

async function add_to_database() {
    rdv.value.patient_id = patient.value.id
    await service.update_rendez_vous(rdv.value);
    patient.value={};
    rdv.value={
        doctor_id:1,
        patient_id:"",
        date:"",
        heure:"",
        type:"",
        statut:""
    };
}

 function isEmpty (heure:string)  {
    
    for(var i=0;i<data.length;i++)
    {
        if(data[i].heure==heure)
        {
            return false
        }
    }
    return true;
}

watch(filter, async () => {
  await searchThings()
})
watch(util, async (newState) => {
    if(newState.RDV == false){
        util.setRDV(false,"")
    }
    if(newState.RDV == true){
        rdv.value.date=util.dateRDV.length>0?util.dateRDV:""
    }
}, { deep: true})
</script>

<template>
    <el-dialog width="950px" title="Nouveau Rendez-Vous" v-model="util.RDV">
        <el-form label-position="top" >
            <el-row :gutter="10">
                <el-col :span="24">
                    <el-form-item label="Type de visite">
                        <el-select class="w-full" v-model="rdv.type"> 
                            <el-option value="Consultation" label="Consultation" />
                            <el-option value="Controle" label="Controle" />
                            <el-option value="Traitement" label="Traitement" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="Date">
                        <el-date-picker
                            v-model="rdv.date"
                            :disabled="util.dateRDV!=''"
                            type="date"
                            placeholder="Cliquez pour selectionner"
                            format="DD/MM/YYYY"
                            value-format="DD/MM/YYYY"
                            class="w-full"
                            style="width:100vw"
                            @change="async()=>{data = await client.isToday({date:rdv.date,doctor_id:rdv.doctor_id})}"
                        />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="Heure">
                        <el-select class="w-full" v-model="rdv.heure">
                            <el-option v-for="h in heures" :key="h" :value="h" :label="h" v-if="isEmpty(h)" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="Statut">
                        <el-select class="w-full" v-model="rdv.statut"> 
                            <el-option value="salle attente" label="Salle d'attente" />
                            <el-option value="canceled" label="Rendez vous annulé" />
                            <el-option value="honored" label="Rendez vous honoré" />
                            <el-option value="planified" label="Rendez vous planifié" />
                        </el-select>
                    </el-form-item>

                    <hr class="my-3">
                    <button class="background-clickdoc btn btn-block" type="button" @click="async () => {await add_to_database()}">
                        Enregistrer le rendez-vous
                    </button>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="Rechercher"  v-if="!patient.id">
                        <el-input  placeholder="CIN / Telephone ..." v-model="filter" />
                    </el-form-item>
                    <el-table :data="patients" size="small" v-if="!patient.id" @row-click="(row : any)=>{patient=row;filter=''}">
                        <template #empty>
                            <button @click="util.setPatient(true)" class="background-clickdoc btn btn-sm btn-block" type="button" > nouveau patient </button>
                        </template>
                        <el-table-column prop="avatar" width="60px"  >
                            <template #default="scope">
                                <div class="flex items-center space-x-3" >
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-10 h-10">
                                            <img :src="scope.row.avatar" :alt="scope.row.name" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="Nom et Prenom">
                            <template #default="scope">
                                <span> {{scope.row.name}} {{ scope.row.surname }} </span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="date_of_birth" label="Date naissance" />
                        <el-table-column prop="CIN" label="CIN" />
                    </el-table>
                    <el-row v-else>
                        <el-col :span="8" class="text-center">
                            <div class="demo-image__preview">
                                <el-image
                                style="width: 150px; height: 150px"
                                :src="patient.avatar"
                                fit="cover"
                                />
                            </div>
                        </el-col>
                        <el-col :span="10">
                            <span class="text-lg font-bold"> {{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{ patient.surname }} </span><br>
                            <span> né{{patient.sex=="M"?"":"e"}} le {{ patient.date_of_birth }}  </span><br>
                            <span> CIN : {{patient.CIN}} </span> <br>
                            <span> Téléphone : {{patient.phone}} </span> <br>
                            <span> Assurance : {{patient.coverage_type}} </span> <br>
                            <el-button type="danger" @click="()=>{patient={}}">
                                Changer
                            </el-button>
                            <el-button type="primary" @click="async ()=>{}">
                                Ajouter
                            </el-button>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
        </el-form>
    </el-dialog>
</template>