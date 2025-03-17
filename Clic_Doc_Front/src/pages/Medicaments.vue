<script lang="ts" setup>
import { Ref,ref ,onBeforeMount } from 'vue';


import { Medicament } from '../../core/Clients/Medicament';
import { LaboMedicament } from '../../core/Clients/LaboMed.ts';
import { Dose } from '../../core/Clients/Dose.ts';

const client = new Medicament();
const medClient = new LaboMedicament();
const doseClient = new Dose();
const newAdmission = ref(false) 
const showMedicine = ref(false) 
const medicament : Ref<any> = ref({
    lab_id:"",
    name:"",
    prix:""
}) 
const laboratoires:Ref<Array<any>> = ref([])
const doses:Ref<Array<any>> = ref([])
const table :Ref<any> = ref(null)


async function show(n:any)
{
    doses.value = await client.getByID(n);
    dose.value.medicament_id = n
    showMedicine.value=true
}

const fields =[
    {prop:'nom',label:'Libellé'},
    {prop:'lab',label:'Laboratoire'},
    {prop:'prix',label:'Prix'}
]

const dose : Ref<any> = ref({
    medicament_id:"",
    dose:"",
    unit:""
})

const doses_fields = [
    {label:'Dose',prop:'dose'},
    {label:'Unité',prop:'unit'}]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{show(n.id)}
    }
]

onBeforeMount(async ()=>{
    laboratoires.value = await medClient.getAll();
})

async function addMedicament()
{
    await client.add(medicament.value);
    medicament.value={
        lab_id:"",
        name:"",
        prix:""
    };
    newAdmission.value=false;
    table.value.getData();
}

async function addDose()
{
    await doseClient.add(dose.value);
    await show(dose.value.medicament_id)
    dose.value.dose =  ""
    dose.value.unit =  ""
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
                title="Médicaments" 
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
                        <el-input v-model="medicament.name" />
                    </el-form-item>
                    <el-form-item label="Laboratoire">
                        <el-select class="w-full" filterable v-model="medicament.lab_id">
                            <el-option
                                v-for="lab in laboratoires"
                                :key="lab.id"
                                :label="lab.name"
                                :value="lab.id"
                            />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Prix">
                        <el-input v-model="medicament.prix"/>
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
            <el-dialog
                title="Doses"
                v-model="showMedicine"
            >
                <el-form>
                    <el-row :gutter="10">
                        <el-col :span="11">
                            <el-form-item label="Dose">
                                <el-input v-model="dose.dose" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="11">
                            <el-form-item label="Unité" >
                                <el-select v-model="dose.unit">
                                    <el-option 
                                        value="mg"
                                        label="mg"
                                    />
                                    <el-option 
                                        value="g"
                                        label="g"
                                    />
                                    <el-option 
                                        value="ml"
                                        label="ml"
                                    />
                                    <el-option 
                                        value="%"
                                        label="%"
                                    />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="2">
                            <el-button  type="primary" @click="async ()=>{ await addDose()}"> 
                                <el-icon><Select /></el-icon>
                            </el-button>
                        </el-col>
                    </el-row>
                </el-form>
                <el-table v-if="doses.length!=0" :data="doses"  :border="true">
                    <el-table-column v-for="f in doses_fields" :key="f.prop" :prop="f.prop" :label="f.label"  />
                </el-table>
            </el-dialog>
        </div>
    </main-layout>
</template>