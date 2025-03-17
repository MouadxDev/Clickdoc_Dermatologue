<script setup lang="ts">

import { onBeforeMount,ref,Ref } from 'vue';
import moment from "moment";

import { ActeMedical } from '../../../core/Clients/ActeMedical';
import { Soin } from '../../../core/Clients/Soin';

const acteClient = new ActeMedical()
const soinClient = new Soin()

const soins :Ref<any> = ref([])
const prestation :Ref<any> = ref({
    id:null,
    prix:0
})
const actes :Ref<any> = ref([])
const addPrice :Ref<any> = ref(false)

const props = defineProps<{
    id:number
}>()


async function getSoins(){
    return  await soinClient.getAllT(props.id)
}

async function addSoins(){
    await soinClient.update(prestation.value.id,prestation.value)
    soins.value = await getSoins()
    addPrice.value=false
}

async function getPrice(x:any,id:null){
    let data = await acteClient.getByID(x)
    prestation.value.prix = data.prix
    prestation.value.id = id
    addPrice.value=true
}

onBeforeMount(async ()=>{
    actes.value = await acteClient.getAll()
    soins.value = await getSoins()
})

</script>

<template>
    <div class="container">
       
        <hr class="my-3">
        <el-table :data="soins" :border="true">
            <el-table-column label="Libellé" prop="libelle"  />
            <el-table-column label="Nombre de scéances" prop="nbr_sceances"  />
            <el-table-column label="Scéances faites" >
                <template #default="scope">
                    {{scope.row.nbr_performed.length}}
                </template>
            </el-table-column>
            <el-table-column label="Historique" >
                <template #default="scope">
                    <ul>
                        <li v-for="h in scope.row.nbr_performed" :key="h.id">
                            {{ moment(h.created_at).format("DD/MM/yyyy") }}
                        </li>
                    </ul>
                </template>
            </el-table-column>
            <el-table-column width="100px">
                <template #default="scope">
                    <el-button link type="primary" v-if="scope.row.nbr_sceances>scope.row.nbr_performed.length" @click=" async()=>{ await getPrice(scope.row.acte_id,scope.row.id) }"><el-icon><Plus/></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog title="Facture" v-model="addPrice">
            <el-form label-position="top">
                <el-form-item label="Prix de prestation">
                    <el-input v-model="prestation.prix" />
                </el-form-item>
            </el-form>
            <button class="btn background-clickdoc" type="button" @click="async()=>{ await addSoins() }"> Ajouter </button>
        </el-dialog>
    </div>

</template>