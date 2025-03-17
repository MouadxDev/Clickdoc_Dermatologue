<script lang="ts" setup>
import { Ref,ref  } from 'vue';


import { Ordonnance } from '../../../core/Clients/Ordonnance';

const client = new Ordonnance();

const table :Ref<any> = ref(null)

    const props = defineProps<
    {
        id:string
    }
>()

const fields =[
    {prop:'name',label:'Nom'},
    {prop:'surname',label:'Prénom'},
    {prop:'medocs',label:"# medoc",width:"100px"},
    {prop:'uid',label:"# consultation",width:"150px"}

]

const actions = [
    {
        icon: "Printer",
        text:"imprimer",
        action: (n:any)=>{console.log(n)}
    },
]




</script>
<template>
    <div class="container mx-auto">
        <ui-table 
        class="w-full"
            :client="client" 
            :no-header="true"
            :has-filters="true"
            :filters="{patient_id:props.id}"
            title="Ordonnances" 
            :fields="fields" 
            :actions="actions"
            ref="table"
            :has-avatar="true"
        />
    </div>
</template>