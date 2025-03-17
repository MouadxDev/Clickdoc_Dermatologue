<script lang="ts" setup>
import { Ref,ref  } from 'vue';

import { ActeMedical } from '../../core/Clients/ActeMedical';

const client = new ActeMedical();
const newAdmission = ref(false) 
const acte : Ref<any> = ref({
    libelle:"",
    prix:""
}) 
const table :Ref<any> = ref(null)

const fields =[
    {prop:'libelle',label:'Libellé'},
    {prop:'prix',label:'Prix'}
]


const actions:Array<any> = [
    {
        icon:"Delete",
        text:"Supprimer",
        action:async(n:any)=>{await client.delete(n.id);table.value.getData()}
    }
]

async function addActe()
{
    await client.add(acte.value);
    acte.value={
        libelle:"",
        prix:""
    };
    newAdmission.value=false;
    table.value.getData();
}





</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :is-main="true"
                :hasButton="true" 
                :client="client" 
                :add="true"  
                :onAdd="()=>{newAdmission=true}"
                title="Actes Médicaux" 
                :fields="fields" 
                :actions="actions"
                ref="table"
            />
            <el-dialog
                title="Nouveau médicament"
                v-model="newAdmission"
            >
                <el-form label-position="top">
                    <el-form-item label="Nom">
                        <el-input v-model="acte.libelle" />
                    </el-form-item>
                    <el-form-item label="Prix">
                        <el-input v-model="acte.prix"/>
                    </el-form-item>
                </el-form>
                <template #footer>
                    <span class="dialog-footer">
                        <el-button type="primary" @click="async ()=>{await addActe()}">
                            Enregistrer
                        </el-button>
                    </span>
                </template>
            </el-dialog>
        </div>
    </main-layout>
</template>