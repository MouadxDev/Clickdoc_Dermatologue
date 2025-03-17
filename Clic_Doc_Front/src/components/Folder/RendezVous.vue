<script setup lang="ts">
    import { RendezVous } from "../../../core/Clients/RendezVous";
    import { reactive,ref } from "vue";
    import { useUtilStore } from "../../../core/Data/stores/utilitaire";
    import { useRdvStore } from "../../../core/Data/stores/rendez-vous";

    
    const props = defineProps<{id:number}>()
    const client = new RendezVous()
    const util = useUtilStore()
    const fields = [
        {prop:"type",label:"Type"},
        {prop:"date",label:"Date"},
        {prop:"heure",label:"Heure"}
    ]
    const ant = reactive({
        patient_id:props.id,
    })
    const table = ref(null)

    const actions = [
        {
            icon: "Delete",
            action: async (n:number)=>{console.log(n)}
        }
    ]
    
</script>
<template>
    <ui-table   
        ref="table"
        :no-header="true" 
        :filters="ant"
        :has-filters="true"
        :client="client" 
        :patient_id="props.id"
        :fields="fields" 
        :actions="actions"
        :trigger-store="useRdvStore"
    >
        <el-row class="mb-2" :gutter="10">
            <el-col :span="24" class="text-right">
                <el-button class="btn btn-sm btn-accent btn-block" @click="util.setRDV(true)"> Ajouter </el-button>
            </el-col>
        </el-row>
    </ui-table>    
</template>