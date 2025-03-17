<script lang="ts" setup>
import { Ref,onBeforeMount,ref , watch } from 'vue';

import { ConsultationService } from '../../../core/Data/services/consultation';

import { Consultation } from '../../../core/Clients/Consult';
import { Patients } from '../../../core/Clients/Patients';

const client = new Consultation();
const service = new ConsultationService();
const patientClient = new Patients();

const showFilters = ref(false) 
const table :Ref<any> = ref(null)
const filters :Ref<any> = ref({
    patient_id:"all",
    date:null
})

const patients :Ref<any> = ref([])
const patient :Ref<any> = ref({})
const filter :Ref<any> = ref("")

const missing : Ref<any> = ref(null)
    

async function searchThings()
{
    patients.value = await patientClient.search(filter.value)
}

watch(filter, async () => {
    await searchThings()
})

watch(filters, async () => {
    await table.value.getData();
},{deep:true})


const fields =[
    {prop:'name',label:'Nom'},
    {prop:'surname',label:'Prénom'},
    {prop:'motif',label:'Motif'}

]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{service.get_consultation(n.id,false)}
    }
]

const others = [
    {
        icon:"VideoPlay",
        text:"Continuer dernière consultation",
        type:'danger',
        action:()=>{service.get_consultation(missing.value,true)}
    }
]

onBeforeMount(async ()=>{
    const data : any = await client.getMissing();

    if(data.message==true)
    {
        missing.value = data.id
    }
})


</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :is-main="true"
                :hasButton="true" 
                :client="client" 
                title="Consultations" 
                :fields="fields" 
                :actions="actions"
                ref="table"
                :has-filters="true"
                :has-avatar="true"
                :filters="filters"
                :onFilter="()=>{showFilters=true}"
                :others="missing!=null?others:[]"
            />
            <el-dialog
                title="Filtrer les consultations"
                v-model="showFilters"
            >
                <el-form label-width="150px">
                    <el-form-item label="Type de visite">
                        <el-date-picker
                            v-model="filters.date"
                            type="date"
                            placeholder="Pick a day"
                            format="DD-MM-YYYY"
                            value-format="YYYY-MM-DD"
                        />
                    </el-form-item>
                    <el-form-item label="Rechercher"  v-if="!patient.id">
                        <el-input  placeholder="CIN / Telephone ..." v-model="filter" />
                    </el-form-item>
                </el-form>
                <el-table :data="patients" v-if="!patient.id">
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
                    <el-table-column  width="60px" >
                        <template #default="scope">
                            <el-button link @click="()=>{patient=scope.row ; filter=''}">
                                <el-icon> <View /> </el-icon> 
                            </el-button>
                        </template>
                    </el-table-column>
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
                        <el-button type="danger" @click="()=>{patient={};filters.patient_id='all'}">
                            Changer
                        </el-button>
                        <el-button type="primary" @click="async ()=>{filters.patient_id = patient.id ; await table.getData()}">
                            Filtrer
                        </el-button>
                    </el-col>
                </el-row>
            </el-dialog>
        </div>
    </main-layout>
</template>