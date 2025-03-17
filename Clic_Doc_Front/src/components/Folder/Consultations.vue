<script lang="ts" setup>
import { Ref,onBeforeMount,ref , watch } from 'vue';

import { ConsultationService } from '../../../core/Data/services/consultation';

import { Consultation } from '../../../core/Clients/Consult';
import { Patients } from '../../../core/Clients/Patients';

const props = defineProps<
    {
        id:string
    }
>()

const client = new Consultation();
const service = new ConsultationService();
const patientClient = new Patients();

const showFilters = ref(false) 
const table :Ref<any> = ref(null)
const filters :Ref<any> = ref({
    patient_id:props.id,
    date:null
})

const patients :Ref<any> = ref([])
const filter :Ref<any> = ref("")

const missing : Ref<any> = ref(null)
    

async function searchThings()
{
    patients.value = await patientClient.search(filter.value)
}

watch(filter, async () => {
    await searchThings()
})

watch(filters, async () => {
    await table.value.getData();
},{deep:true})


const fields =[
    {prop:'name',label:'Nom'},
    {prop:'surname',label:'Prénom'},
    {prop:'docteur',label:'Docteur traitant'},
    {prop:'created_at',label:'Date',isDate:true}

]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{service.get_consultation(n.id,false)}
    }
]

const others = [
    {
        icon:"VideoPlay",
        text:"Continuer dernière consultation",
        type:'danger',
        action:()=>{service.get_consultation(missing.value,true)}
    }
]

onBeforeMount(async ()=>{
    const data : any = await client.getMissing();

    if(data.message==true)
    {
        missing.value = data.id
    }
})


</script>
<template>
        <div class="container mx-auto">
            <ui-table 
                :hasButton="true" 
                :client="client" 
                title="Consultations" 
                :fields="fields" 
                :actions="actions"
                ref="table"
                :no-header="true"
                :has-filters="true"
                :has-avatar="true"
                :filters="filters"
                :onFilter="()=>{showFilters=true}"
                :others="missing!=null?others:[]"
            />
        </div>
</template>