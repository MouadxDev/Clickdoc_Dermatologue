<script setup lang="ts">
    import { reactive,ref } from "vue";
    import {TableauPersonnalise} from "../../../core/Clients/TableauPersonnalise"

    
    const props = defineProps<{id:number}>()
    const client = new TableauPersonnalise()
    const fields = [
        {prop:"date",label:"Date"},
        {prop:"indication",label:"Indication"},
        {prop:"laser",label:"Laser"},
        {prop:"pore",label:"Pore"},
        {prop:"longueur_onde",label:"Longueur d'onde"},
        {prop:"pm",label:"PM"},
        {prop:"lt",label:"LT"},
        {prop:"fu",label:"FU"},
        {prop:"Note",label:"Note"},
    ] 
    const ant = reactive({
        patient_id:props.id,
    })
    const table = ref(null)
    const ligne = ref({
        date:"",
        indication:"",
        laser:"",
        pore:"",
        Note:"",
        longueur_onde:"0",
        pm:"0",
        fu:"0",
        lt:"0",
        patient_id:props.id
    })

    const actions :any = [
        {
            icon: "Delete",
            action: async (n:number)=>{await deleteLigne(n)}
        }
    ]
    const addLigne = async()=>{
        await client.add(ligne.value)
        table.value.getData()
        ligne.value = {
            date:"",
            indication:"",
            laser:"",
            pore:"",
            Note:"",
            longueur_onde:"0",
            pm:"0",
            fu:"0",
            lt:"0",
            patient_id:props.id
        }
        visible.value=false
    }

    const deleteLigne = async (n:any)=>{
        await client.delete(n.id)
        table.value.getData()
    }

    const visible = ref(false)
    
</script>
<template>
    <div>
        <el-dialog width="950px" title="Ajouter" v-model="visible">
            <div>
                <el-form label-position="top">
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-form-item label="Date">
                                <el-date-picker
                                    v-model="ligne.date"
                                    type="date"
                                    placeholder="Cliquez pour selectionner"
                                    format="DD/MM/YYYY"
                                    value-format="DD/MM/YYYY"
                                    class="w-full"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Indication">
                                <el-input v-model="ligne.indication" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Laser">
                                <el-input v-model="ligne.laser" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Pore">
                                <el-input v-model="ligne.pore" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="laser">
                                <el-input v-model="ligne.longueur_onde" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="PM">
                                <el-input v-model="ligne.pm" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="FU">
                                <el-input v-model="ligne.fu" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="LT">
                                <el-input v-model="ligne.lt" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-form-item label="Note">
                                <el-input v-model="ligne.Note" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <button class="btn background-clickdoc btn-block" @click="addLigne()" type="button"> 
                                Ajouter
                            </button>
                        </el-col>
                    </el-row>
                </el-form>
            </div>
        </el-dialog>
        <ui-table   
            ref="table"
            :no-header="true" 
            :filters="ant"
            :has-filters="true"
            :client="client" 
            :patient_id="props.id"
            :fields="fields" 
            :actions="actions"
        >
            <el-row class="mb-2" :gutter="10">
                <el-col :span="24" class="text-right">
                    <el-button class="btn btn-sm btn-accent btn-block" @click="visible=true"> Ajouter </el-button>
                </el-col>
            </el-row>
        </ui-table>   
    </div>
     
</template>