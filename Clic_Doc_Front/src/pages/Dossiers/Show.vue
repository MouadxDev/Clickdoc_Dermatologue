<script setup lang="ts">
import {
  onBeforeMount,
  ref,
  Ref
} from 'vue';

import { ElLoading } from 'element-plus';
import { useRoute } from 'vue-router';

import {ConsultationService} from '../../../core/Data/services/consultation'


import { Patients } from '../../../core/Clients/Patients';
import { Upload } from '../../../core/Clients/Upload';
import { WaitingList } from '../../../core/Clients/WaitingList';
import { useUiStore } from '../../../core/Data/stores/ui';

    const route = useRoute()
    const service = new ConsultationService()

    const client = new Patients()
    const uploadClient = new Upload();
    const waitingClient = new WaitingList();
    const ui = useUiStore()
    const changeID = ref(false)
    const isUploaded = ref(false)
    const consultation : any = ref({})
    const activeTab  = ref("ant-medicaux")
    const motif = ref("")
    const ajouterAdmission :Ref<boolean> = ref(false)
        
    const liste_motifs = [ 
      
        "Teigne (dermatophytose du cuir chevelu)",
        "Trichotillomanie",
        
    ]
   
    const patient : Ref<any>  = ref({})
    const patientToChange : Ref<any> = ref({})
    const action = {
        icon:"Edit",
        action:async ()=>{
            changeID.value=true
            patientToChange.value = await getPatient()
        }
    }

    async function upload() {
        var formData = new FormData();
        var file : any = document.querySelector('input[type=file]').files[0];
        formData.append("file",file);
        const resp : any = await uploadClient.add(formData)
        patientToChange.value.avatar=resp.data.full_path
    }

    function getAvatar() {
        if(isUploaded.value==false)
        {
            patient.sex=="M"?patient.avatar="/avatar-m.png":patient.avatar="/avatar-f.jpg"
        }
    }

    async function getPatient(){
        return await client.getByUID(route.params.id)
    }
    async function initPage(){
        const loading = ElLoading.service({
            lock: true,
            text: 'Loading',
            background: 'rgba(0, 0, 0, 0.7)',
        })
        patient.value = await getPatient()
        consultation.value=await waitingClient.isWaiting({patient_id:patient.value.id})
        loading.close()
        ui.setFold(true)
    }

    async function setConsult()
    {
        await service.add_consultation({
            patient_id : patient.value.id,
            wl_id:consultation.value.data.id,
            motif:motif.value,
            patient_uid:patient.value.uid
        })
    }


    onBeforeMount(async ()=>{
        await initPage()
    })

</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <ui-sheet :title="patient.uid" :hasAction="true" :action="action" >
                        <el-row>
                            <el-col :span="8" class="text-center">
                                <div class="demo-image__preview">
                                    <el-image
                                    style="width: 100px; height: 100px"
                                    :src="patient.avatar"
                                    fit="cover"
                                    />
                                </div>
                            </el-col>
                            <el-col :span="16">
                                <span class="text-lg font-bold"> {{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{ patient.surname }} </span><br>
                                <span> né{{patient.sex=="M"?"":"e"}} le {{ patient.date_of_birth }}  </span><br>
                                <span> CIN : {{patient.CIN}} </span> <br>
                                <span> Téléphone : {{patient.phone}} </span> <br>
                            </el-col>
                        </el-row>
                    </ui-sheet>
                    <ui-sheet :noHeader="true" title="Mutuelle" class="mt-2" :hasAction="true" :action="action">
                        <el-row>
                            <el-col :span="6" class="text-center">
                                <el-icon size="50" class="text-error" v-if="patient.coverage==false"><CircleCloseFilled /></el-icon>
                                <el-icon size="50" class="text-success" v-else><CircleCheckFilled /></el-icon>
                            </el-col>
                            <el-col :span="18">
                                <span class="text-lg font-bold"> {{ patient.coverage==true?"":" Non"}} Couvert </span><br>
                                <span v-if="patient.coverage==true">  {{ patient.coverage_type }} </span>
                            </el-col>
                        </el-row>
                    </ui-sheet>
                    <ui-sheet title="Voir plus" class="mt-2" :hasAction="false" >
                        <el-row :gutter="10">
                            <el-col :span="12" class="p-2">
                                <button class="btn btn-block " @click="activeTab = 'ant-medicaux'">Antécédents médicaux</button>
                            </el-col>
                            <el-col :span="12" class="p-2">
                                <button class="btn btn-block btn-doc"  @click="activeTab = 'ant-chirurgicaux'">Antécédents chirurgicaux</button>
                            </el-col>
                            <el-col :span="12" class="p-2">
                                <button class="btn btn-block btn-doc"  @click="activeTab = 'ant-familiaux'">Antécédents familiaux</button>
                            </el-col>
                            <el-col :span="12" class="p-2">
                                <button class="btn btn-block btn-doc"  @click="activeTab = 'ant-allergies'">Allérgies</button>
                            </el-col>
                            <el-col :span="12" class="p-2">
                                <button class="btn btn-block btn-doc"  @click="activeTab = 'ant-drugs'">Médicaments</button>
                            </el-col>
                        </el-row>
                    </ui-sheet>
                </el-col>
                <el-col :span="16">
                    <ui-sheet :noHeader="true" title="Actions" class="mb-2" :hasAction="false" v-if="consultation.status==true" >
                        <el-row :gutter="10"  >
                            <el-col :span="12">
                                <el-button type="primary"  class="btn-block " @click=""> 
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg> &nbsp; &nbsp;
                                    Notifier 
                                </el-button>
                            </el-col>
                            <el-col :span="12">
                                <el-button type="primary"  class="btn-block " @click="() =>{ajouterAdmission=true}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg> &nbsp; &nbsp;
                                    Commencer {{ consultation.data.type }}  
                                </el-button>
                            </el-col>
                        </el-row>
                    </ui-sheet>
                    <component v-if="patient.id" :is="activeTab" :id="patient.id" />
                </el-col>
            </el-row>
            <el-dialog 
                v-model="ajouterAdmission" 
                title="Commencer consultation"
            >
                <el-form>
                    <el-form-item 
                        label="Motif"
                    >
                        <el-select v-model="motif" class="w-full" >
                            <el-option v-for="m in liste_motifs" :key="m" :value="m" :label="m"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item 
                        
                    >
                        <el-button class="btn-block" type="primary" @click="async()=>{await setConsult()}">
                            Enregistrer
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
            <el-dialog 
                v-model="changeID"
                title="Identification"    
            >
                <div>
                    <el-row>
                        <el-col :span="6" class="text-center">
                            <div class="demo-image__preview">
                                <el-image
                                style="width: 100px; height: 100px"
                                :src="patientToChange.avatar"
                                fit="cover"
                                />
                            </div>
                            <input type="file" id="file" hidden class="file-input file-input-bordered file-input-xs w-full max-w-xs" @change="async ()=>{await upload()}"/>
                            <label for="file" class="el-button el-button--primary" > Selectionner Image</label>
                        </el-col>
                        <el-col :span="18">
                            <el-form label-position="top" >
                                <el-row :gutter="10">
                                    <el-col :span="8">
                                        <el-form-item label="Civilité">
                                            <el-select :on-change="()=>(getAvatar())" v-model="patient.sex" class="w-full" >
                                                <el-option label="Monsieur" :value="'M'" />
                                                <el-option label="Madame" :value="'F'" />
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="8">
                                        <el-form-item label="Nom" >
                                            <el-input v-model="patientToChange.name" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="8">
                                        <el-form-item label="Prénom" >
                                            <el-input v-model="patientToChange.surname" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="8">
                                        <el-form-item label="Date de naissance">
                                            <el-date-picker
                                                v-model="patientToChange.date_of_birth"
                                                type="date"
                                                placeholder="Cliquez pour selectionner"
                                                format="DD/MM/YYYY"
                                                value-format="DD/MM/YYYY"
                                                class="w-full"
                                            />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="8">
                                        <el-form-item label="CIN" >
                                            <el-input v-model="patientToChange.CIN" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="8">
                                        <el-form-item label="Téléphone" >
                                            <el-input v-model="patientToChange.phone" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="12">
                                        <el-form-item label="Mutuelle ?" >
                                            <el-switch
                                                v-model="patientToChange.coverage"
                                                size="large"
                                                active-text="Oui"
                                                inactive-text="Non"
                                                :active-value="1"
                                                :inactive-value="0"

                                            />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="12">
                                        <el-form-item label="Type mutuelle" >
                                            <el-input :disabled="patientToChange.coverage==false" v-model="patientToChange.coverage_type" />
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                                <div class="text-right">
                                    <el-button type="primary" @click="async ()=>{await client.update(patientToChange) ; await initPage(); changeID=false }" >
                                        Enregistrer
                                    </el-button>
                                </div>
                            </el-form>
                        </el-col>
                    </el-row>
                </div>
            </el-dialog>
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