<script setup lang="ts">

import { Ref , onBeforeMount, ref } from "vue";

import { Demande } from '../../../core/Clients/DemandeAnalyse';
import { Analyses } from '../../../core/Clients/Analyses';

const props = defineProps<{
    id:any
}>()



const demandes : Ref<any> = ref([])
const analyses : Ref<any> = ref([])
const analyseClient = new Analyses()
const demandeClient = new Demande()

async function getDemande(){
    return  await demandeClient.getByID(props.id)
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
        <el-table :data="demandes" :border="true">
            <el-table-column label="Analyse" prop="libelle" />
            <el-table-column label="Etat" prop="state" />
            <el-table-column label="Document" prop="document">
                <template #default="scope">
                    <a v-if="scope.row.document!=null" class="btn btn-link btn-accent btn-sm"> <el-icon> <Folder /></el-icon> voir </a>
                </template>
            </el-table-column>
            <el-table-column width="50px">
                <template #default="scope">
                    <button class="btn btn-sm btn-error" type="button"  v-if="scope.row.state=='soumise'" @click="async ()=>{ await removeDemande(scope.row.id) }" ><el-icon><Delete/></el-icon></button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>