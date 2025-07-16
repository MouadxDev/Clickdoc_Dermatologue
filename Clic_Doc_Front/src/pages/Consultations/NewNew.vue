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
import { useUtilStore } from '../../../core/Data/stores/utilitaire';
import { Edit as EditIcon } from '@element-plus/icons-vue';

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
        tension:null,
        fr_cardiaque:null,
        saturation:null,
        glyc:null,
        temp:null,
    })

    const mesure_rens :Ref<any> = ref({
        taille:null,
        poids:null,
        tension:null,
        fr_cardiaque:null,
        saturation:null,
        glyc:null,
        temp:null,
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
const util = useUtilStore();


async function getPatient(){
    return await client.getByUID(consult.patient_id)
}

async function renseigner(){
    mesure_rens.value.patient_id = patient.value.id
    await mesureClient.add(mesure_rens.value)
    
    // ✅ FIX: Properly handle the returned data
    const mesureData = await getMesure()
    mesure.value = Array.isArray(mesureData) ? mesureData[0] : mesureData
    
    renseign.value = false
    mesure_rens.value = {
        taille:null,
        poids:null,
        tension:null,
        fr_cardiaque:null,
        saturation:null,
        glyc:null,
        temp:null,
    }
}

async function saveConsultation(){
    await clientConsult.update(finalisation)
    router.push("/dossiers/"+patient.value.uid)
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
    
    // ✅ FIX: Properly handle array data from API
    const mesureData = await getMesure()
    mesure.value = Array.isArray(mesureData) ? mesureData[0] : mesureData
    
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
                    <!-- Patient Info Block -->
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl patient-info-block">
                        <el-row align="middle">
                            <el-col :lg="8" :sm="24" class="text-center">
                                <div class="demo-image__preview">
                                    <el-image
                                        style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #e0e7ef; box-shadow: 0 2px 8px #e0e7ef;"
                                        :src="patient.avatar"
                                        fit="cover"
                                    />
                                </div>
                            </el-col>
                            <el-col :lg="16" :sm="24">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-lg">{{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{ patient.surname }}</span>
                                    <el-button size="small" type="primary" :icon="EditIcon" circle @click="() => { util.setPatientID(patient.uid); util.setEditPatient(true); }" style="margin-left: 8px;" title="Modifier patient" />
                                </div>
                                <div class="patient-info-fields">
                                    <div class="info-row">
                                        <span class="info-label">{{ patient.sex == "M" ? "Né" : "Née" }} le</span>
                                        <span class="info-value">{{ patient.date_of_birth }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Age :</span>
                                        <span class="info-value">{{ patient.age !== null ? patient.age + " ans" : "Date de naissance invalide" }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">CIN :</span>
                                        <span class="info-value">{{patient.CIN}}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">État civil :</span>
                                        <span class="info-value">
                                            {{ patient.civil_status === 1 
                                                ? (patient.sex === 'F' ? 'Mariée' : 'Marié') 
                                                : (patient.civil_status === 0 ? 'Célibataire' : 'Indéfini') }}
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Téléphone :</span>
                                        <span class="info-value">{{patient.phone}}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Couverture :</span>
                                        <span class="info-value">
                                            <el-icon class="text-error" v-if="patient.coverage==false"><CircleCloseFilled /></el-icon>
                                            <el-icon class="text-success" v-else><CircleCheckFilled /></el-icon>
                                            <span class="font-bold ml-1">{{ patient.coverage==true?"":" Non"}} Couvert</span>
                                            <span v-if="patient.coverage==true">  {{ patient.coverage_type }} </span>
                                        </span>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <edit-patient />

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
                                        <!-- ✅ FIX: Better condition checking -->
                                        <ul class="ml-4" v-if="mesure && typeof mesure === 'object' && mesure.id">
                                            <li> Taille :  <b>{{ mesure.taille }}</b></li>
                                            <li> Poids : <b>{{ mesure.poids }} </b></li>
                                            <li> Tension :  <b>{{ mesure.tension }}</b></li>
                                            <li> Fréquence cardiaque :  <b>{{ mesure.fr_cardiaque }}</b></li>
                                            <li> Saturation :  <b>{{ mesure.saturation }}</b></li>
                                            <!-- ✅ FIX: Correct property name -->
                                            <li> Glycémie :  <b>{{ mesure.glyc }}</b></li>
                                            <li> Température :  <b>{{ mesure.temp }}</b></li>
                                            
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

                                                <el-form-item label="Fréquence cardiaque" >
                                                    <el-input v-model="mesure_rens.fr_cardiaque">
                                                        <template #append>
                                                            bpm
                                                        </template>
                                                    </el-input>
                                                </el-form-item>

                                                <el-form-item label="Saturation" >
                                                    <el-input v-model="mesure_rens.saturation">
                                                        <template #append>
                                                            %
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                
                                                <el-form-item label="Glycémie" >
                                                    <!-- ✅ FIX: Correct property name -->
                                                    <el-input v-model="mesure_rens.glyc">
                                                        <template #append>
                                                            mmol/L
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                
                                                <el-form-item label="Température corporelle" >
                                                    <el-input v-model="mesure_rens.temp">
                                                        <template #append>
                                                            °C
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
                            </el-icon>Finaliser - privé 
                        </button>
                        <button  class=" btn btn-success btn-block" @click="async () => { finalisation.isPrivate=false ; await saveConsultation()  }"> 
                            <el-icon>
                                <Unlock/>  
                            </el-icon>Finaliser - publique 
                        </button>
                    </div>
                </el-col>
            </el-row>
        </div>
    </main-layout>
</template>

<style scoped>
.patient-info-block {
  border: 1px solid #e0e7ef;
  box-shadow: 0 2px 8px #e0e7ef;
  background: #f9fbfd;
  margin-bottom: 1rem;
}
.patient-info-fields {
  margin-top: 0.5rem;
}
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 4px 0;
  border-bottom: 1px solid #f0f2f5;
  font-size: 14px;
}
.info-label {
  color: #7b8794;
  font-weight: 500;
}
.info-value {
  color: #222f3e;
  font-weight: 600;
}

</style>