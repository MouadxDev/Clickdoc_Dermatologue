<script lang="ts" setup>
import { Ref,reactive,ref,onBeforeMount } from 'vue';
import {useRouter} from "vue-router"

import {useAuthStore} from '../../../core/Data/stores/auth'

import { License } from '../../../core/Clients/Licence';
import { Upload } from '../../../core/Clients/Upload';
import { Users } from '../../../core/Clients/Users';
import { Folder } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';

const client = new License();
const uploadClient = new Upload();
const userClient = new Users()
const store = useAuthStore();

const newAdmission = ref(false) 
const showAdmission = ref(false) 
const addAdmin = ref(false) 
const table :Ref<any> = ref(null)
const model = reactive({
    logo:"",
    city:"",
    adress:"",
    contact_email:"",
    contact_name:"",
    name:"",
    type:"cabinet",
    justification:"",
})
const toShow : Ref<any> = ref({
    logo:"",
    city:"",
    adress:"",
    contact_email:"",
    contact_name:"",
    name:"",
    justification:"",
})

const admin = reactive({
    name:"",
    email:"",
    password:"",
    avatar:"/avatar-m.png",
    entity_id:"",
    role:"Admin",
    degree:"",
    experience:1,
    license_number:"",
    specialty:"",
    gender:"M",
    fee:""
})

const admins :Ref<any> = ref([])

const toUpload : Ref<string> = ref("")

function getAvatar() {
    admin.gender=="M"?admin.avatar="/avatar-m.png":admin.avatar="/avatar-f.jpg"
}

    

async function  show(n:any)
{
    toShow.value = await client.getByID(n)
    admin.entity_id = toShow.value.id
    admins.value = await userClient.getByEntite(toShow.value.id)
    showAdmission.value=true

}

async function addAdministrateur()
{
    await userClient.add(admin)
    admins.value = await userClient.getByEntite(toShow.value.id)
}

const fields =[
    {prop:'name',label:'Libellé', width:"300px"},
    {prop:'adress',label:'Adresse'},
    {prop:'city',label:'Ville' , width:"120px"},
    {prop:'creation',label:"Création", width:"120px"},
    {prop:'expiration',label:"Expiration", width:"120px"},
    {prop:'licence',label:"Active",isBoolean:true,width:"80px"},
]

const actions = [
    {
        icon: "View",
        text:"voir",
        action: async (n:any)=>{await show(n.id)}
    }
]

async function addLicence() {
    await client.add(model);
    table.value.getData();
    model.logo=""
    model.city="";
    model.adress="";
    model.contact_email="";
    model.contact_name="";
    model.name="";
    model.justification="";
    newAdmission.value=false
}


async function updateLicence() {
    await client.update(toShow.value);
    table.value.getData();
    showAdmission.value=false
}

async function upload() {
    var formData = new FormData();
    var file : any = document.querySelector('input[type=file]').files[0];
    formData.append("file",file);
    const resp = await uploadClient.add(formData)
    if(toUpload.value=="justificatif")
    {
        model.justification = resp.data.full_path
    }
    else 
    {
        model.logo = resp.data.full_path
    }
    
}
const router = useRouter()

onBeforeMount(()=>{
    if(store.user.role!="superAdmin")
    {
        ElMessage.error("La destination demandée n'est pas autorisée")
        router.go(-1)
    }
})

</script>
<template>
    <main-layout >
        <div class="container mx-auto">
            <ui-table 
                :hasButton="true" 
                :hasLogo="true" 
                :client="client" 
                :add="true"  
                :onAdd="()=>{newAdmission=true}"
                title="Licences" 
                :fields="fields" 
                :actions="actions"
                ref="table"
                :is-main="true"
            />
            <input type="file" id="file" hidden class="file-input file-input-bordered file-input-xs w-full max-w-xs" @change="async ()=>{await upload()}"/>
            <el-dialog
                title="Nouvelle licence"
                v-model="newAdmission"
            >
                <el-form v-model="model" label-position="top">
                    <el-form-item label="Nom de l'établissement">
                        <el-input v-model="model.name" />
                    </el-form-item>
                    <el-form-item label="Adresse">
                        <el-input v-model="model.adress" />
                    </el-form-item>
                    <el-form-item label="Ville">
                        <el-input v-model="model.city" />
                    </el-form-item>
                    <el-form-item label="Email de contact">
                        <el-input v-model="model.contact_email" />
                    </el-form-item>
                    <el-form-item label="Type">
                        <el-select v-model="model.type"  class="w-full">
                            <el-option value="cabinet" label="Cabinet" />
                            <el-option value="laboratoire" label="Laboratoire" />
                            <el-option value="pharmacie" label="Pharmacie" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Nom de contact">
                        <el-input v-model="model.contact_name" />
                    </el-form-item>
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-form-item label="Logo">
                                <label for="file" @click="toUpload = 'logo' " v-if="model.logo.length==0" class="el-button el-button--primary" > Selectionner Image</label>
                                <a class="btn btn-link" :href="model.logo" v-else >
                                    <el-icon><Folder/></el-icon> Afficher
                                </a>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Justificatifs d'existence">
                                <label for="file" v-if="model.justification.length==0" @click="toUpload = 'justificatif' " class="el-button el-button--primary" > Selectionner fichier</label>
                                <a class="btn btn-link" :href="model.logo" v-else >
                                    <el-icon><Folder/></el-icon> Afficher
                                </a>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <div class="text-right">
                        <el-button type="primary" @click="async ()=>{await addLicence()}">
                            Enregistrer
                        </el-button>
                    </div>
                </el-form>
            </el-dialog>
            <el-dialog
                v-model="showAdmission"
                title="Details de la licence"
                width="900px"
            >
                <el-form v-model="toShow" label-position="top">
                    <el-row :gutter="10">
                        <el-col :span="24" >
                            <el-form-item label="Nom de l'établissement" >
                                <el-input v-model="toShow.name" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="4" >
                            <el-form-item label="Active" >
                                <el-switch v-model="toShow.licence" :active-value="1" :inactive-value="0" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="10" >
                            <el-form-item label="Date de création" >
                                <el-date-picker
                                    v-model="toShow.creation"
                                    type="date"
                                    placeholder="Cliquez pour selectionner"
                                    format="DD/MM/YYYY"
                                    value-format="DD/MM/YYYY"
                                    class="w-full"
                                    disabled
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :span="10" >
                            <el-form-item label="Date d'expiration" >
                                <el-date-picker
                                    v-model="toShow.expiration"
                                    type="date"
                                    placeholder="Cliquez pour selectionner"
                                    format="DD/MM/YYYY"
                                    value-format="DD/MM/YYYY"
                                    class="w-full"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8" >
                            <el-form-item label="Cycle de paiement" >
                                <el-select v-model=" toShow.payment_cycle ">
                                    <el-option
                                        :value="0"
                                        label="Essai"
                                    />
                                    <el-option
                                        :value="1"
                                        label="Mensuel"
                                    />
                                    <el-option
                                        :value="2"
                                        label="Annuel"
                                    />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8" >
                            <el-form-item label="Nom de contact" >
                                <el-input v-model="toShow.contact_name" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8" >
                            <el-form-item label="Email" >
                                <el-input v-model="toShow.contact_email" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-table class="my-2" :data="admins">
                        <template #empty>
                            Aucun administrateur , <el-button link @click="addAdmin=true"> clickez pour en ajouter </el-button>
                        </template>
                        <el-table-column label=" Nom  " prop="name" />
                        <el-table-column label=" Email " prop="email" />
                        <el-table-column label=" Specialité " prop="specialty"/>
                    </el-table>

                    <div class="text-right">
                        <el-button type="primary" @click="async ()=>{await updateLicence()}">
                            Enregistrer
                        </el-button>
                    </div>
                </el-form>
            </el-dialog>
            <el-dialog v-model="addAdmin" title="Ajouter administrateur" width="900">
                <el-form v-model="admin" label-position="top">
                    <el-row :gutter="10">
                        <el-col :span="4">
                            <el-form-item label="Civilité">
                                <el-select @change="getAvatar()" v-model="admin.gender" class="w-full" >
                                    <el-option label="Monsieur" :value="'M'" />
                                    <el-option label="Madame" :value="'F'" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="10">
                            <el-form-item label="Nom" >
                                <el-input v-model="admin.name" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="10">
                            <el-form-item label="Specialité" >
                                <el-select v-model="admin.specialty" class="w-full">
                                    <el-option 
                                        value="dermatologue"
                                        label="Dermatologue"
                                    />
                                    <el-option 
                                        value="dentiste"
                                        label="Dentiste"
                                    />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="email" >
                                <el-input v-model="admin.email" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Mot de passe" >
                                <el-input type="password" v-model="admin.password" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <div class="text-right">
                        <button type="button" class="btn btn-m background-clickdoc" @click="async () => {await addAdministrateur()}" >
                            Enregistrer
                        </button>
                    </div>
                </el-form>
            </el-dialog>
        </div>
    </main-layout>
</template>