<script setup lang="ts">
    import { AntAllergies } from "../../../core/Clients/AntAllergies";
    import { ref } from "vue";

    
    const props = defineProps<{id:number}>()
    const client = new AntAllergies()
    const fields = [
        {prop:"allergy",label:"Allérgie"},
        {prop:"reaction",label:"Reaction"}
    ]
    const ant = ref({
        allergy:"",
        patient_id:props.id,
        reaction:""
    })
    const table = ref(null)

    const actions = [
        {
            icon: "Delete",
            action: async (n:number)=>{client.delete(n.id);table.value.getData()}
        }
    ]

    async function addToLst() {
        ant.value.patient_id=props.id
        await client.add(ant.value); 
        table.value.getData();
        ant.value.allergy='';
        ant.value.reaction=''
    }

    

    
</script>
<template>
    <ui-table   
        ref="table"
        :client="client" 
        :patient_id="props.id"
        :fields="fields" 
        :actions="actions"
        :no-header="true"
    >
        <div class="text-lg font-semibold"> Ajouter </div>
        <el-row class="my-4" :gutter="10">
            <el-col :span="6">
                <el-input v-model="ant.allergy" placeholder="Allérgie" />
            </el-col>
            <el-col :span="14">
                <el-input v-model="ant.reaction" placeholder="Reaction" />
            </el-col>
            <el-col :span="4">
                <el-button class="btn btn-sm btn-accent btn-block" @click="addToLst()"> Ajouter </el-button>
            </el-col>
        </el-row>
    </ui-table>    
</template>