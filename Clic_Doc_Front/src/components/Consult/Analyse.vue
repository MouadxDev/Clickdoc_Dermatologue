<script setup lang="ts">

import { Ref , computed, onBeforeMount, ref } from "vue";
import {useConsultStore} from "../../../core/Data/stores/consultation"

import { Demande } from '../../../core/Clients/DemandeAnalyse';
import { Analyses } from '../../../core/Clients/Analyses';

import ENV from '../../../core/env'

const consult = useConsultStore();

const demande : Ref<any> = ref({
    consultation_id:consult.consult,
    analyse_id:"",
})

const demandes : Ref<any> = ref([])
const analyses : Ref<any> = ref([])
const analyseClient = new Analyses()
const demandeClient = new Demande()

const searchTerm: Ref<string> = ref('');

const filteredAnalyses = computed(() => {
    return analyses.value.filter((analyse: any) =>
        analyse.libelle.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});



async function getDemande(){
    return  await demandeClient.getByConsult(consult.consult)
}
async function setDemande(){
    await demandeClient.add(demande.value)
    demandes.value = await getDemande()
    demande.value.analyse_id=""
}
async function removeDemande(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await demandeClient.delete(x) 
    }
    demandes.value = await getDemande()
}

onBeforeMount(async ()=>{

    analyses.value = await analyseClient.getAll()
    demandes.value = await getDemande()
})



</script>
<template>
    <div class="container">
        <el-form label-position="top">
            <el-row :gutter="10" >
                <el-col :span="21">
                    <el-form-item label="Analyse">
                    <el-select
                        class="w-full"
                        v-model="demande.analyse_id"
                        placeholder="Rechercher une analyse"
                        filterable
                    >
                        <el-option
                            v-for="m in filteredAnalyses"
                            :key="m.id"
                            :value="m.id"
                            :label="m.libelle"
                            @click="async ()=>{await setDemande()}"
                        />
                    </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="3">
                    <el-form-item label=" &nbsp ">
                        <button @click="async ()=>{await setDemande()}" class="btn btn-sm btn-block background-clickdoc" type="button" > <el-icon><Select /></el-icon> </button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <hr class="my-3">
        <el-table :data="demandes" :border="true">
            <el-table-column label="Analyse" prop="libelle" />
            <el-table-column label="Etat" prop="state" />
            <el-table-column label="Document" prop="document">
                <template #default="scope">
                    <a v-if="scope.row.document" 
                        class="btn btn-link btn-accent btn-sm" 
                        :href="ENV.VITE_BACKEND_Download+'/'+scope.row.document+'/download'" >
                        <el-icon> 
                            <Folder />
                        </el-icon> 
                        voir 
                    </a>
                </template>
            </el-table-column>
            <el-table-column width="70px">
                <template #default="scope">
                    <button class="btn btn-sm btn-error" type="button"  v-if="scope.row.state=='soumise'" @click="async ()=>{ await removeDemande(scope.row.id) }" ><el-icon><Delete/></el-icon></button>
                </template>
            </el-table-column>
        </el-table>
        <div class="text-right">
            <a class="btn btn-sm btn-link" target="_blank" :href="ENV.VITE_BACKEND+'/analyse/'+consult.consult"> <el-icon> <Printer/></el-icon> Imprimer </a>
        </div>
    </div>
</template>