<script setup lang="ts">

import { onBeforeMount,ref,Ref } from 'vue';
import moment from "moment";

import { ActeMedical } from '../../../core/Clients/ActeMedical';
import { Soin } from '../../../core/Clients/Soin';
import { useConsultStore } from '../../../core/Data/stores/consultation';

const consult =  useConsultStore();

const acteClient = new ActeMedical()
const soinClient = new Soin()

const soins :Ref<any> = ref([])
const actes :Ref<any> = ref([])

const soin : Ref<any> = ref({
    consultation_id:consult.consult,
    nbr_sceances:"",
    acte_id:""
})


async function getSoins(){
    return  await soinClient.getAll(consult.consult)
}

async function setSoins(){
    await soinClient.add(soin.value)
    soins.value = await getSoins()
    soin.value.acte_id=""
    soin.value.nbr_sceances=""
}


async function removeSoin(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await soinClient.delete(x) 
    }
    soins.value = await getSoins()
        
}

onBeforeMount(async ()=>{
    actes.value = await acteClient.getAll()
    soins.value = await getSoins()
})

</script>

<template>
    <div class="container">
        <el-form label-position="top" >
            <el-row :gutter="10" >
                <el-col :span="14">
                    <el-form-item label="Soin/Acte médical">
                        <el-select class="w-full" v-model="soin.acte_id" >
                            <el-option 
                                v-for="a in actes"
                                :key="a.id"
                                :value="a.id"
                                :label="a.libelle"
                            />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="7">
                    <el-form-item label="Nombre de scéances">
                        <el-input v-model="soin.nbr_sceances" />
                    </el-form-item>
                </el-col>
                <el-col :span="3">
                    <el-form-item label=" &nbsp ">
                        <el-button @click="async ()=>{await setSoins()}" class="w-full" type="primary"> <el-icon><Select /></el-icon> </el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <hr class="my-3">
        <el-table :data="soins" :border="true">
            <el-table-column label="Libellé" prop="soin"  />
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
                    <el-button link type="danger" v-if="scope.row.nbr_performed==0" @click="async ()=>{ await removeSoin(scope.row.id) }"><el-icon><Delete/></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>

</template>