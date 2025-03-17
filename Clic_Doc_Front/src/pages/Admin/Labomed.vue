<script lang="ts" setup>
import { Ref,ref , watch , onBeforeMount } from 'vue';

import { useRouter } from 'vue-router';

import { LaboMedicament } from '../../../core/Clients/LaboMed';
import {useAuthStore} from '../../../core/Data/stores/auth'
import { ElMessage } from 'element-plus';

const client = new LaboMedicament();
const newAdmission = ref(false) 
const store = useAuthStore();
const table :Ref<any> = ref(null)
const filter = ref("")
const lab : Ref<any> = ref({
    name:"",
    adress:"",
    city:""
})

const router = useRouter()
    
watch(filter, async () => {
  //await searchThings()
})

function show(n:any)
{
    router.push("/dossiers/"+n)
}

const fields =[
    {prop:'name',label:'Nom'},
    {prop:'adress',label:'Adresse'},
    {prop:'city',label:'Ville'}
]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{show(n.uid)}
    }
]

async function addLab()
{
    await client.add(lab.value);
    lab.value = {
        name:"",
        adress:"",
        city:""
    }
    newAdmission.value=false
    table.value.getData()

}

onBeforeMount(()=>{
    if(store.user.role!="superAdmin")
    {
        ElMessage.error("La destination demandée n'est pas autorisée")
        router.go(-1)
    }
})

</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :hasButton="true" 
                :client="client" 
                :add="true"  
                :onAdd="()=>{newAdmission=true}"
                title="Laboratoires de Médicaments" 
                :fields="fields" 
                :actions="actions"
                ref="table"
            />
            <el-dialog
                title="Nouveau laboratoire"
                v-model="newAdmission"
            >
                <el-form label-position="top">
                    <el-form-item label="Nom">
                        <el-input v-model="lab.name" />
                    </el-form-item>
                    <el-form-item label="Adresse">
                        <el-input v-model="lab.adress"/>
                    </el-form-item>
                    <el-form-item label="Ville">
                        <el-input  v-model="lab.city"/>
                    </el-form-item>
                </el-form>
                <template #footer>
                    <span class="dialog-footer">
                        <button type="button" class="btn btn-m background-clickdoc" @click="async ()=>{await addLab()}">
                            Enregistrer
                        </button>
                    </span>
                </template>
            </el-dialog>
            
        </div>
    </main-layout>
</template>