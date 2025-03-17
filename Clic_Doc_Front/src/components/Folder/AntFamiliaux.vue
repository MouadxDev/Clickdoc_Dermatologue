<script setup lang="ts">
    import { AntFamiliaux } from "../../../core/Clients/AntFamiliaux";
    import { reactive,ref } from "vue";

    
    const props = defineProps<{id:number}>()
    const client = new AntFamiliaux()
    const fields = [
        {prop:"type",label:"Type"},
        {prop:"members",label:"Membres"}
    ]
    const ant = reactive({
        type:"",
        patient_id:props.id,
        members:""
    })
    const table = ref(null)

    const actions = [
        {
            icon: "Delete",
            action: async (n:number)=>{client.delete(n.id);table.value.getData()}
        }
    ]

    async function addToLst() {
        await client.add(ant); 
        table.value.getData();
        ant.members='';
        ant.type=''
    }
</script>
<template>
    <ui-table   
        ref="table"
        :hasButton="true" 
        :client="client" 
        :patient_id="props.id"
        :fields="fields" 
        :noHeader="true"
        :actions="[]"
    >
        <div class="text-lg font-semibold"> Ajouter </div>
        <el-row class="my-4" :gutter="10">
            <el-col :span="6">
                <el-input v-model="ant.type" placeholder="Type" />
            </el-col>
            <el-col :span="14">
                <el-input v-model="ant.members" placeholder="Membres" />
            </el-col>
            <el-col :span="4">
                <el-button class="btn btn-sm btn-accent btn-block" @click="addToLst()"> Ajouter </el-button>
            </el-col>
        </el-row>
    </ui-table>    
</template>