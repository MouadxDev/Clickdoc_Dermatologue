<script lang="ts" setup>
import { Ref,ref  } from 'vue';


import { Users } from '../../core/Clients/Users';

const client = new Users();
const newAdmission = ref(false) 
const editAdmission = ref(false) 
const user : Ref<any> = ref({
    name:"",
    email:"",
    password:""
}) 

const privileges : Ref<any> = ref(
    [
        { name:"Salle d'attente", enable:1, view:1,edit:1 },
        { name:"Dossiers", enable:0,view:0,edit:0 },
        { name:"Consultations", enable:0,view:0,edit:0 },
        { name:"Ordonnances", enable:0,view:0,edit:0 },
        { name:"Analyses", enable:0,view:0,edit:0},
        { name:"Personnel", enable:0,view:0,edit:0 },
    ]
)
const table :Ref<any> = ref(null)

async function show(n:any)
{
    console.log(n);
}

const fields =[
    {prop:'name',label:'Nom'},
    {prop:'email',label:'Email'},
    {prop:'role',label:'Role'}
]


const actions = [
    {
        icon: "View",
        text:"voir",
        action: async (n:any)=>{await editUser(n.id)}
    },
    {
        icon:"Delete",
        text:"supprimer",
        action: async (n:any)=>{confirm("êtes vous sur de vouloir supprimer cet utilisateur ?") ? (await client.delete(n.id) && await table.value.getData()):''}
    }
]

async function addUser()
{
    user.value.privileges = privileges.value
    await client.add(user.value)
    newAdmission.value=false
    privileges.value=
    [
        { name:"Salle d'attente", enable:1, view:1,edit:1 },
        { name:"Dossiers", enable:0,view:0,edit:0 },
        { name:"Consultations", enable:0,view:0,edit:0 },
        { name:"Ordonnances", enable:0,view:0,edit:0 },
        { name:"Analyses", enable:0,view:0,edit:0},
        { name:"Personnel", enable:0,view:0,edit:0 },
    ]
    user.value = {
        name:"",
        email:"",
        password:""
    }
    await table.value.getData()
}

async function editUser(x:number)
{
    editAdmission.value = true
    const data =   await client.getByID(x)
    user.value = data.data.user
    privileges.value = data.data.privileges
}


</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :is-main="true"
                :hasButton="true" 
                :client="client" 
                :add="true"  
                :onAdd="()=>{newAdmission=true}"
                title="Personnel" 
                :fields="fields" 
                :actions="actions"
                ref="table"
            />
            <el-dialog
                title="Nouvel utilisateur"
                v-model="newAdmission"
            >
                <el-form label-position="top">
                    <el-row :gutter="10">
                        <el-col :span="8">
                            <el-form-item label="Nom">
                                <el-input v-model="user.name" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="Email">
                                <el-input v-model="user.email" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="Mot de passe">
                                <el-input type="password"  v-model="user.password" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
                <el-table :data="privileges">
                    <el-table-column prop="name" label="Rubrique" />
                    <el-table-column prop="enable" label="Activer">
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.enable"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </el-table-column>
                    <!--el-table-column prop="view" label="Voir">
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.view"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </!--el-table-column>
                    <el-table-column prop="edit" label="Modifier" >
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.edit"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </el-table-column-->
                </el-table>
                <template #footer>
                    <span class="dialog-footer">
                        <button type="button" class="btn btn-m background-clickdoc" @click="async ()=>{await addUser()}">
                            Enregistrer
                        </button>
                    </span>
                </template>
            </el-dialog>
            <el-dialog
                title="Modifier utilisateur"
                v-model="editAdmission"
            >
                <el-form label-position="top">
                    <el-row :gutter="10">
                        <el-col :span="8">
                            <el-form-item label="Nom">
                                <el-input v-model="user.name" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="Email">
                                <el-input v-model="user.email" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="Mot de passe">
                                <el-input type="password"  v-model="user.password" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
                <el-table :data="privileges">
                    <el-table-column prop="name" label="Rubrique" />
                    <el-table-column prop="enable" label="Activer">
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.enable"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="view" label="Voir">
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.view"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="edit" label="Modifier" >
                        <template #default="scope">
                            <el-switch
                                v-model="scope.row.edit"
                                size="small"
                                :active-value="1" :inactive-value="0"
                            />
                        </template>
                    </el-table-column>
                </el-table>
                <template #footer>
                    <span class="dialog-footer">
                        <button type="button" class="btn btn-m background-clickdoc" @click="async ()=>{await addUser()}">
                            Enregistrer
                        </button>
                    </span>
                </template>
            </el-dialog>
        </div>
    </main-layout>
</template>