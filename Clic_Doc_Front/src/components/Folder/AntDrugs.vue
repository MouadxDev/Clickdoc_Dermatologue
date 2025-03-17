<script setup lang="ts">
    import { AntDrugs } from "../../../core/Clients/AntDrugs";
    import { reactive,ref } from "vue";

    
    const props = defineProps<{id:number}>()
    const client = new AntDrugs()
    const fields = [
        {prop:"name",label:"Nomination"},
        {prop:"dose",label:"Dose"},
        {prop:"frequency",label:"Frequence"},
    ]
    const ant = reactive({
        name:"",
        patient_id:props.id,
        dose:"",
        frequency:"",
    })
    const table = ref({})

    const actions = [
        {
            icon: "Delete",
            action: async (n:any)=>{client.delete(n.id);table.value.getData()}
        }
    ]

    async function addToLst() {
        await client.add(ant); 
        table.value.getData();
        ant.name='';
        ant.dose='';
        ant.frequency='';

    }

    

    
</script>
<template>
    <ui-table   
        ref="table"
        :no-header="true" 
        :client="client" 
        :patient_id="props.id"
        :fields="fields" 
        :actions="actions"
    >
        <div class="text-lg font-semibold"> Ajouter </div>
        <el-row class="my-4" :gutter="10">
            <el-col :span="6">
                <el-input v-model="ant.name" placeholder="Nom" />
            </el-col>
            <el-col :span="7">
                <el-input v-model="ant.dose" placeholder="Dose" />
            </el-col>
            <el-col :span="7">
                <el-input v-model="ant.frequency" placeholder="Fréquence" />
            </el-col>
            <el-col :span="4">
                <el-button class="btn btn-sm btn-accent btn-block" @click="addToLst()"> Ajouter </el-button>
            </el-col>
        </el-row>
    </ui-table>    
</template>