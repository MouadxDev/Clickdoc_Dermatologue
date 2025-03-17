<script lang="ts" setup>
import { Ref,ref  } from 'vue';


import { Ordonnance } from '../../core/Clients/Ordonnance';

const client = new Ordonnance();

const table :Ref<any> = ref(null)


const fields =[
    {prop:'name',label:'Nom',width:"140px"},
    {prop:'medocs',label:"# medoc",width:"100px"},
    {prop:'uid',label:"# consultation",width:"150px"},
    {prop:'motif',label:'Motif'}

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
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :is-main="true" 
                :client="client" 
                title="Ordonnances" 
                :fields="fields" 
                :actions="actions"
                ref="table"
                :has-avatar="true"
            />
        </div>
    </main-layout>
</template>