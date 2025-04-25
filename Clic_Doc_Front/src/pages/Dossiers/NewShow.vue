<script setup lang="ts">
import {
  onBeforeMount,
  ref,
  Ref,
  watch
} from 'vue';
import moment from "moment"

import { ElLoading } from 'element-plus';
import { useRoute } from 'vue-router';

import {ConsultationService} from '../../../core/Data/services/consultation'


import { Patients } from '../../../core/Clients/Patients';
import { Mesure } from '../../../core/Clients/Mesures';
import { WaitingList } from '../../../core/Clients/WaitingList';
import { useUiStore } from '../../../core/Data/stores/ui';
import { usePatientStore } from '../../../core/Data/stores/patient';
import { useSocketStore } from '../../../core/Data/stores/socket';
import { useUtilStore } from '../../../core/Data/stores/utilitaire';
import { useAuthStore } from '../../../core/Data/stores/auth';
import ENV from '../../../core/env';

    const route = useRoute()
    const ws = useSocketStore().socket
    const service = new ConsultationService()

    const client = new Patients()
    const waitingClient = new WaitingList();
    const mesureClient = new Mesure();
    const ui = useUiStore()
	const util = useUtilStore()
    const store = usePatientStore()
    const authStore = useAuthStore()
    const consultation : Ref<any> = ref({
        status:false
    })
    const renseign : Ref<boolean> = ref(false)
    const activeTab  = ref("sit-financiere")
   
    const tabs = [
        {
            icon:"https://clickdoc.webredirect.org/public/icons/argent.png",
            label:"Situation",
            name:"sit-financiere"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/imagerie.png",
            label:"Imagerie",
            name:"imagerie"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/sang.png",
            label:"Analyses",
            name:"analyses"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/consultation.png",
            label:"Consultations",
            name:"consultations"
        },

        {
            icon:"https://clickdoc.webredirect.org/public/icons/consultation.png",
            label:"Traitements",
            name:"traitement"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/ordonnance.png",
            label:"Ordonnances",
            name:"ant-ordonnances"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/consultation.png",
            label:"Rendez vous",
            name:"rendez-vous"
        },
        {
            icon:"https://clickdoc.webredirect.org/public/icons/history.png",
            label:"Antécédents",
            name:"ants"
        },  
        {
            icon:"https://clickdoc.webredirect.org/public/icons/history.png",
            label:"Tableau personnalisé",
            name:"tableau-personnalise"
        },
        
    ]
   
    const patient : Ref<any>  = ref({})
    const action = {
        icon:"Edit",
        action: ()=>{
            console.log("HERE")
            util.setPatientID(patient.value.uid)
            util.setEditPatient(true)
        }
    }
    const mesure :Ref<any> = ref({
        taille:null,
        poids:null,
        tension:null
    })
	
	let result = [
        {'taille' : null},
        {'poids' : null},
        {'tension' : null}
    ];
    
    const mesure_rens :Ref<any> = ref({
        taille:null,
        poids:null,
        tension:null
    })

    async function getPatient(){
        return await client.getByUID(route.params.id)
    }
    async function renseigner(){
        
         mesure_rens.value.patient_id = patient.value.id
         await mesureClient.add(mesure_rens.value)
         renseign.value=false
         mesure.value = await getMesure()
         const data = await getMesure()
         mesure.value = data[0];
    }

    function notifier(){
        const message = {
            entite:authStore.user.entity_id,
            patient :patient.value.surname +" "+patient.value.name,
        }
        ws.send(JSON.stringify(message))
        
             
   
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
        patient.value = await getPatient()
        mesure.value = await getMesure()
		const data = await getMesure()
        mesure.value = data[0];
		
        result = data.reduce((acc, item) => {
			['poids', 'taille', 'tension'].forEach(key => {
				if (!acc[key]) {
				acc[key] = [];
				}
				acc[key].push(item[key]);
			});
			return acc;
        }, {});
        Object.keys(result).forEach(key => {
            result[key] = result[key].join(', ');
        });
        consultation.value=await waitingClient.isWaiting({patient_id:patient.value.id})
        console.log("consultation.value");
        console.log(consultation.value);
        
        loading.close()
        ui.setFold(true)
    }

    async function setConsult()
    {
        await service.add_consultation({
            patient_id : patient.value.id,
            wl_id:consultation.value.data.id,
            motif:"[]",
            patient_uid:patient.value.uid
        })
    }

    watch(store, async (newState) => {
        if(newState.trigger == true){
            patient.value = await getPatient()
            store.setTrigger(false)
        }
      }, { deep: true})

    onBeforeMount(async ()=>{
        await initPage()
    })

</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-sheet :title="'Dossier N° '+patient.uid" :hasBack="true" :hasAction="true" :action="action" :isTop="true" />
            <el-row :gutter="10">
                <el-col :span="18">
					<div class="flex" style="padding-top:10px ;gap: 10px;"> 
                             <a 
                            class="btn background-clickdoc" 
                            target="_blank" 
                            :href="`${ENV.VITE_BACKEND}/certificat/aptitude/${patient.uid}/${authStore.user.id}`"
                            >
                            certificat d'aptitude
                            </a>
                            <a 
                            class="btn background-clickdoc" 
                            target="_blank" 
                            :href="`${ENV.VITE_BACKEND}/certificat/repos/${patient.uid}/${authStore.user.id}`"
                            >
                            certificat de repos
                           </a>
                           <a 
                            class="btn background-clickdoc" 
                            target="_blank" 
                            style="display: none;"
                            :href="`${ENV.VITE_BACKEND}/certificat/maladpro/${patient.uid}/${authStore.user.id}`"
                            >
                            certificat maladie pro
                           </a>
                           <a 
                            class="btn background-clickdoc" 
                            target="_blank" 
                            :href="`${ENV.VITE_BACKEND}/facturation/${patient.uid}/${authStore.user.id}`"
                            >
                            Facturation
                           </a>
						
					</div>
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" >
                        <el-tabs
                            v-model="activeTab"
                            type="card"
                            class="demo-tabs"
                        >
                            <el-tab-pane v-for="tab in tabs" v-if="patient.id" :key="tab.name" :name="tab.name">
                                <template #label>
                                    <el-icon size="20"> <img :src="tab.icon" > </el-icon> &nbsp; {{ tab.label }}
                                </template>
                                <div class="h-thing">
                                    <component v-if="patient.id" :is="tab.name"  :id="patient.id" />
                                </div>
                            </el-tab-pane>
                        </el-tabs>
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
                                <span class=" font-bold">{{ patient.sex === 'M' ? 'M.' : (patient.sex === 'F' ? 'Mme' : 'Mlle') }} {{patient.name }} {{ patient.surname }} </span><br>
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
                    <div class="rounded-2xl h-32 p-4 bg-white mt-3 shadow-xl" >
                        <div class="flex text-lg text-green-500">
                            <img src="https://clickdoc.webredirect.org/public/icons/argent.png" class="h-6 w-6"> &nbsp;&nbsp;
                            <span class="font-bold" >
                                Observations
                            </span>
                        </div>
                        <el-input type="textarea" v-model="patient.observation" @change="async()=>{await client.update(patient)}" />
                    </div>
                    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" >
                        <div class="flex text-lg text-clickdoc">
                            <img src="https://clickdoc.webredirect.org/public/icons/mesure.png" class="h-6 w-6"> &nbsp;&nbsp;
                            <span class="font-bold">
                                Données vitales
                            </span>
                        </div>

                        <el-row :gutter="10" class="mt-4" >
                            <el-col :lg="15" :sm="24" >
                                <ul class="ml-1">
                                    <li> <b class="text-clickdoc">Constantes : </b>
                                        <ul class="ml-4">
                                            <li> Diabetes :  {{ patient.diabetes==1?"Type 1":patient.diabetes==2?"Type 2":patient.diabetes==3?"Prédiabètes":"Non" }} </li>
                                            <li> Groupe sanguin : {{ patient.blood_type }} </li>
                                            <li v-if="patient.sex === 'F' || patient.sex === 'Mlle'">
                                                Statut marital :  {{ patient.sex === 'F' ? 'Mariée' : 'Célibataire' }}
                                            </li>


                                        </ul>    
                                    </li>
                                    <li> <b class="text-clickdoc"> Mesures : </b> <br>
                                        <ul class="ml-4" v-if="typeof mesure == 'object'">
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
													{{ result.taille }}
                                                    <el-input v-model="mesure_rens.taille">
                                                        <template #append>
                                                            cm
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                <el-form-item label="poids" >
													{{ result.poids }}
                                                    <el-input v-model="mesure_rens.poids">
                                                        <template #append>
                                                            KG
                                                        </template>
                                                    </el-input>
                                                </el-form-item>
                                                <el-form-item label="tension" >
													{{ result.tension }}
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
                    <div class="rounded-2xl  p-4 bg-white mt-3 shadow-xl" v-if="consultation.status==true" >
                        <button class="btn btn-block background-clickdoc mb-2" @click="notifier() ">
                            Notifier l'acceuil
                        </button>
                        <button class="btn btn-block background-clickdoc" @click="async ()=>{await setConsult()} ">
                            Commencer la consultation
                        </button>
                    </div>
                </el-col>
            </el-row>
        </div>
    </main-layout>
</template>
<style>
    .text-success 
    {
        color:#23c552
    }
    .text-error 
    {
        color:#f84f31
    }
</style>