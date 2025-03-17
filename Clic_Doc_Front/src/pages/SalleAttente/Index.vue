<script lang="ts" setup>
import { Ref,ref  } from 'vue';

import { useRouter } from 'vue-router';

import { WaitingList } from '../../../core/Clients/WaitingList';
import { useUtilStore } from '../../../core/Data/stores/utilitaire';
import { useRdvStore } from '../../../core/Data/stores/rendez-vous';

const client = new WaitingList();
const util =  useUtilStore();
const table :Ref<any> = ref(null)

const router = useRouter()


function show(n:any)
{
    router.push("/dossiers/"+n)
}

const fields =[
    {prop:'uid',label:'Reference'},
    {prop:'surname',label:'Prénom'},
    {prop:'name',label:'Nom'},
    {prop:'type',label:'Type de visite'}
]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: (n:any)=>{show(n.uid)}
    }
]

</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :is-main="true"
                :hasAvatar="true"  
                :hasButton="true" 
                :client="client" 
                :add="true"  
                :onAdd="()=>{util.setRDV(true)}"
                title="Salle d'attente" 
                :fields="fields" 
                :actions="actions"
                ref="table"
                :triggerStore="useRdvStore"
                toRoute="/dossiers"
            />
        </div>
    </main-layout>
</template>