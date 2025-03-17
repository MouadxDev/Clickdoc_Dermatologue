<script lang="ts" setup>
import { Ref,ref ,onBeforeMount } from 'vue';


import { Analyses } from '../../core/Clients/Analyses';

const client = new Analyses();
const newAdmission = ref(false) 
const showMedicine = ref(false) 
const analyse : Ref<any> = ref({
    libelle:""
})
const doses:Ref<Array<any>> = ref([])
const table :Ref<any> = ref(null)


async function show(n:any)
{
    doses.value = await client.getByID(n);
    showMedicine.value=true
}

const fields =[
    {prop:'libelle',label:'Libellé'}
]



const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{show(n.id)}
    }
]

onBeforeMount(async ()=>{

})

async function addMedicament()
{
    await client.add(analyse.value);
    analyse.value={
        libelle:""
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
                title="Liste analyses" 
                :fields="fields" 
                :actions="actions"
                ref="table"
            />
            <el-dialog
                title="Nouveau médicament"
                v-model="newAdmission"
            >
                <el-form label-position="top">
                    <el-form-item label="Libellé">
                        <el-input v-model="analyse.libelle" />
                    </el-form-item>
                </el-form>
                <template #footer>
                    <span class="dialog-footer">
                        <el-button type="primary" @click="async ()=>{await addMedicament()}">
                            Enregistrer
                        </el-button>
                    </span>
                </template>
            </el-dialog>
        </div>
    </main-layout>
</template>