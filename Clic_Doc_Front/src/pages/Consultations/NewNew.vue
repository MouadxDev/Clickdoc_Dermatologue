<script lang="ts" setup>
import { useConsultStore } from '../../../core/Data/stores/consultation';
import { Patients } from '../../../core/Clients/Patients';
import { onBeforeMount,ref,Ref,reactive } from 'vue';
import { ElLoading } from 'element-plus';
import { Consultation } from '../../../core/Clients/Consult';
import { Mesure } from '../../../core/Clients/Mesures';
import { useRouter } from 'vue-router';
import { Accordion } from '../../../core/Types/Components/Accordion';
import moment from "moment"

const router : any = useRouter()
const consult = useConsultStore();
const patient : Ref<any> = ref({})
const finalisation : any = reactive({
    id:consult.consult,
    isFinished:0,
    isPrivate:0
})

const renseign : Ref<boolean> = ref(false)
const mesure :Ref<any> = ref({
    taille:null,
    poids:null,
    tension:null
})

const mesure_rens :Ref<any> = ref({
    taille:null,
    poids:null,
    tension:null
})
const consultation : Ref<any> = ref({data:{}})

const items:Array<Accordion> = [
    {
        label:"Details de consultation",
        component:"details"
    },
    {
        label:"Examen Physique",
        component:"examen-physique"
    },
    {
        label:"Diagnostique",
        component:"diagnostique"
    },
    {
        label:"Observations",
        component:"observation"
    },
    {
        label:"Ordonnance",
        component:"ordonnance"
    },
    {
        label:"Analyses",
        component:"analyse"
    },
    {
        label:"Traitement/Soins",
        component:"soins"
    },
    {
        label:"Honoraires",
        component:"honoraires"
    },

]

const client = new Patients()
const clientConsult = new Consultation()
const mesureClient = new Mesure();




async function getPatient(){
    return await client.getByUID(consult.patient_id)
}

async function renseigner(){
    mesure_rens.value.patient_id = patient.value.id
        await mesureClient.add(mesure_rens.value)
        mesure.value = await getMesure()
        renseign.value=false
        mesure_rens.value={
        taille:null,
        poids:null,
        tension:null
    }
}

async function saveConsultation(){
    await clientConsult.update(finalisation)
    router.push("/consultations")
}


async function getMesure(){
    return await mesureClient.getByID(patient.value.id)
}
async function initPage(){
    const loading = ElLoading.service({
        lock: true,
        text: 'Loading',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    consultation.value = await clientConsult.getOne(consult.consult)
    patient.value = await getPatient()
    mesure.value = await getMesure()
    loading.close()
}

onBeforeMount(async ()=>{
    await initPage()
})

</script>

<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-sheet :title="'Consultation N° '+consult.consult_uid" :hasBack="true" :hasAction="true" :action="()=>{}" :isTop="true" />
            <el-row :gutter="10">
                <el-col :span="18">
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl h-consult" >
                        <ui-accordion 
                            name="consultation"
                            type="plus"
                            :items="items"
                        />
                    </div>
                </el-col>
                <el-col :span="6">
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" >
                        <el-row>
                            <el-col :lg="8" :sm="24" class="text-center">
                                <div class="demo-image__preview">
                                    <el-image
                                        style="width: 80px; height: 80px"
                                        :src="patient.avatar"
                                        fit="cover"
                                    />
                                </div>
                            </el-col>
                            <el-col :lg="16" :sm="24">
                                <span class=" font-bold"> {{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{ patient.surname }} </span><br>
                                <span> né{{patient.sex=="M"?"":"e"}} le {{ patient.date_of_birth }}  </span><br>
                                <span> CIN : {{patient.CIN}} </span> <br>
                                <span> Téléphone : {{patient.phone}} </span> <br>
                                <el-row>
                                    <el-col :span="4" class="">
                                        <el-icon class="text-error" v-if="patient.coverage==false"><CircleCloseFilled /></el-icon>
                                        <el-icon class="text-success" v-else><CircleCheckFilled /></el-icon>
                                    </el-col>
                                    <el-col :span="18">
                                        <span class="font-bold"> {{ patient.coverage==true?"":" Non"}} Couvert </span>
                                        <span v-if="patient.coverage==true">  {{ patient.coverage_type }} </span>
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" >
                        <div class="flex text-lg text-clickdoc">
                            <img src="https://clickdoc.webredirect.org/public/icons/mesure.png" class="h-6 w-6"> &nbsp;&nbsp;
                            <span class="font-bold">
                                Données vitales
                            </span>
                        </div>
                        <el-row :gutter="10" class="mt-4">
                            <el-col :lg="15" :sm="24" >
                                <ul class="ml-1">
                                    <li> <b class="text-clickdoc">Constantes : </b>
                                        <ul class="ml-4">
                                            <li> Diabetes :  {{ patient.diabetes==1?"Type 1":patient.diabetes==2?"Type 2":patient.diabetes==3?"Prédiabètes":"Non" }} </li>
                                            <li> Groupe sanguin : {{ patient.blood_type }} </li>
                                        </ul>    
                                    </li>
                                    <li> <b class="text-clickdoc"> Mesures : </b> <br>
                                        <ul class="ml-4" v-if="mesure.id!=undefined">
                                            <li> Taille :  <b>{{ mesure.taille }}</b></li>
                                            <li> Poids : <b>{{ mesure.poids }} </b></li>
                                            <li> Tension :  <b>{{ mesure.tension }}</b></li>
                                            <li> Dernière saisie le <b>{{  moment(mesure.created_at).format("DD/MM/YYYY") }}</b>  </li>
                                            <li class="mt-2"> <button class="btn btn-sm btn-link btn-block text-clickdoc " @click="renseign=true" > Renseigner  </button> </li>
                                        </ul> 
                                        <div class="p-4 text-center" v-else>
                                            <button class="btn btn-sm background-clickdoc " @click="renseign=true" > Renseigner </button>
                                        </div>
                                        <el-dialog title="Renseigner mesures" v-model="renseign">
                                            <el-form label-position="top">
                                                <el-form-item label="taille">
                                                    <el-input v-model="mesure_rens.taille">
                                                        <template #append>
                                                            cm
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                <el-form-item label="poids" >
                                                    <el-input v-model="mesure_rens.poids">
                                                        <template #append>
                                                            KG
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                <el-form-item label="tension" >
                                                    <el-input v-model="mesure_rens.tension">
                                                        <template #append>
                                                            mmHG
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                <el-form-item>
                                                    <button class="btn btn-sm btn-block background-clickdoc btn-block" type="button" @click="async()=>await renseigner()" > Enregistrer </button>
                                                </el-form-item>

                                            </el-form>
                                        </el-dialog>
                                    </li>
                                </ul>
                            </el-col>
                            <el-col :lg="9">
                                <img src="https://clickdoc.webredirect.org/public/icons/silhouette.png" class="w-full">
                            </el-col>
                        </el-row>
                    </div>
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" v-if="consultation.data.deets && consultation.data.deets.isFinished==0">
                        <button  class=" btn btn-error btn-block mb-2" @click="async () => { finalisation.isPrivate=true ; await saveConsultation()  }"> 
                            <el-icon>
                                <Lock/>  
                            </el-icon>&nbsp;&nbsp;&nbsp;&nbsp; Finaliser - privé 
                        </button>
                        <button  class=" btn btn-success btn-block" @click="async () => { finalisation.isPrivate=false ; await saveConsultation()  }"> 
                            <el-icon>R
                                <Unlock/>  
                            </el-icon>&nbsp;&nbsp;&nbsp;&nbsp; Finaliser - publique 
                        </button>
                    </div>
                </el-col>
            </el-row>
        </div>
    </main-layout>
</template>