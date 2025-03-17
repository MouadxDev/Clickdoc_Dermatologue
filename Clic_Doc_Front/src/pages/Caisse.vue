<script setup lang="ts">
import { Ref,ref , watch,onMounted } from 'vue';
import { Patients } from '../../core/Clients/Patients';

import {Charge} from "../../core/Clients/Charge"
import {Payment} from "../../core/Clients/Payment"
import {Facture} from "../../core/Clients/Facture"
import ENV from '../../core/env';
import { useAuthStore } from '../../core/Data/stores/auth';
import { ElMessage } from 'element-plus';

const patientClient = new Patients();
const client = new Charge();
const clientP = new Payment();
const clientF = new Facture();

const authStore = useAuthStore()

const patients:Ref<Array<any>> = ref([])
const patient:Ref<any> = ref({})
const addCharge:Ref<boolean> = ref(false)
const statut:Ref<string> = ref("")
const addPayment :Ref<boolean> = ref(false)
const filter = ref("")
const table = ref()
const table1 = ref()

let firstId: null = null;

const allPatients:Ref<Array<any>> = ref([])
const allFacture:Ref<Array<any>> = ref([])

const paiement :Ref<any> = ref({
    patient_id: null,
    amount:0,
    motif:'',
    type:"Espèce"
})

const report : Ref<any> = ref({
    entries:0,
    charges:0,
    ca:0
})

const deets : Ref<any> = ref({
    nope:[],
    part:[]
})

const fieldsCharge = [
    {prop:"montant",label:"Montant"},
    {prop:"motif",label:"Motif"},
    {prop:"declarant",label:"Déclarant"},
    {prop:"created_at",isDate:true,label:"Date"},
]

const fieldsPayment = [
    {prop:"uid",label:"UID"},
    {prop:"amount",label:"Montant"},
    {prop:"type",label:"Type"},
    {prop:"created_at",isDate:true,label:"Date"},
]

const charge : Ref<any> =  ref({
    montant:0,
    motif:""
})

async function searchThings()
{
    patients.value = await patientClient.search(filter.value)
    // console.log(patients.value)
}

async function getData(){
    deets.value = await clientF.deets(patient.value.id)
       
    if ('id' in deets.value) {
        firstId = deets.value.id;
    } else {
        // Otherwise, search in nested objects and arrays
        for (const key in deets.value) {
            if (Array.isArray(deets.value[key])) {
                // Check the first item in any array for an `id`
                if (deets.value[key].length > 0 && 'id' in deets.value[key][0]) {
                    firstId = deets.value[key][0].id;
                    break;
                }
            } else if (typeof deets.value[key] === 'object' && 'id' in deets.value[key]) {
                // Check in nested objects
                firstId = deets.value[key].id;
                break;
            }
        }
    }

    // console.log("First ID:", firstId);
}

async function impCharge(){
    await client.add(charge.value)
    addCharge.value=false
    charge.value =  {
        montant:0,
        motif:""
    }
    await table1.value.getData()
    await getReports()
}

async function saveFacture(){
    paiement.value.patient_id =patient.value.id;
    paiement.value.facture_id = firstId;

    await clientP.add(paiement.value)
    paiement.value={
        patient_id:0,
        amount:0,
        type:"Espèce"
    }
    
    // await clientF.update({id:paiement.value.patient_id,statut:statut})
    
    addPayment.value=false
   
    if (table.value) {
        await table.value.getData();
    }
     
    
    await getData()
    await getReports()
}

async function getReports(){
    const data = await clientP.getReport("")

    report.value.entries = 0
    data?.entries.forEach((element:any) => {
        report.value.entries += element.amount
    });
    report.value.charges = 0
    data?.charges.forEach((element:any) => {
        report.value.charges += element.montant
    });
    report.value.ca = 0
    data?.chiffre_affaires.forEach((element:any) => {
        report.value.ca += element.amount
    });

}
const addPaymentDialog = ref(false);
const addPaymentFacture = ref(false);
function handleAddPayment() {
    addPaymentDialog.value = true;
    
    
}

function openAddPaymentFacture() {
    // addPaymentDialog.value = false;
    // addChargeFacture.value = true;
    addPaymentFacture.value = true;
    addPaymentDialog.value = false;

    
}

function openAddPayment() {
    // addPaymentDialog.value = false;
    addPayment.value = true;
    addPaymentDialog.value = false;

}


// function handleAddPayment() {
//     if (!patient.value.id) {
//         ElMessage.error("Veuillez sélectionner un patient avant d'ajouter un paiement.");
//         return;
//     }
    
//     addPayment.value = true;
    
// }

onMounted(async ()=>{
    await getReports()
    allPatients.value = await patientClient.getAll()
    allFacture.value = await clientF.getAll()



})

watch(filter, async () => {
  await searchThings()
})

function handleExport() {
        const url = `${ENV.VITE_BACKEND}/DataTableCaisse/${authStore.user.id}`;
        window.open(url, '_blank'); 
    }
</script>

<template>
    <main-layout>
        <div class="container mx-auto">

            <ui-sheet title="Caisse" :hasBack="true" :hasAction="true" :action="()=>{}" :isTop="true" :export="true"
                :onExport="handleExport" />

            <div class="stats mt-2 shadow w-full">
                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                    <div class="stat-title">Chiffre d'affaire</div>
                    <div class="stat-value"> {{ report.ca }} </div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                    <div class="stat-title">En caisse</div>
                    <div class="stat-value">{{ report.entries - report.charges }}</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-title">Charges</div>
                    <div class="stat-value">{{ report.charges }}</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="inline-block w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                            </path>
                        </svg>
                    </div>
                    <div class="stat-title">Crédit</div>
                    <div class="stat-value">{{ report.ca-report.entries }}</div>
                </div>
            </div>
            <div>
                <el-row :gutter="10">
                    <el-col :span="16">
                        <div class="h-thing p-4 my-2 rounded-2xl w-full bg-white">
                            <el-tabs>
                                <el-tab-pane label="Entrées">
                                    <button class="btn btn-sm btn-block background-clickdoc"
                                        @click.prevent="handleAddPayment()">
                                        Ajouter paiement
                                    </button>
                                    <ui-table ref="table" :hasButton="false" :client="clientP" :fields="fieldsPayment"
                                        :noHeader="true" :isMain="false" :actions="[]" />
                                </el-tab-pane>
                                <el-tab-pane label="Sorties">
                                    <button class="btn btn-sm btn-block background-clickdoc"
                                        @click.prevent="addCharge=true">
                                        Ajouter charge
                                    </button>
                                    <ui-table ref="table1" :hasButton="false" :client="client" :fields="fieldsCharge"
                                        :noHeader="true" :actions="[]" />
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                    </el-col>
                    <el-col :span="8">
                        <div class="h-thing p-4 my-2 rounded-2xl w-full bg-white">
                            <div class="text-uppercase text-xl mb-2 font-bold ">
                                Situation Client
                            </div>
                            <el-form-item label="Rechercher" v-if="!patient.id">
                                <el-input placeholder="CIN / Telephone ..." v-model="filter" />
                            </el-form-item>
                            <el-table :data="patients" size="small" v-if="!patient.id"
                                @row-click="async (row:any)=>{patient=row;filter='';await getData()}">
                                <el-table-column prop="avatar" width="60px">
                                    <template #default="scope">
                                        <div class="flex items-center space-x-3">
                                            <div class="avatar">
                                                <div class="mask mask-squircle w-10 h-10">
                                                    <img :src="scope.row.avatar" :alt="scope.row.name" />
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column label="Nom et Prenom">
                                    <template #default="scope">
                                        <span> {{scope.row.name}} {{ scope.row.surname }} </span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="date_of_birth" label="Date naissance" />
                                <el-table-column prop="CIN" label="CIN" />
                            </el-table>
                            <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl" v-else>
                                <el-row>
                                    <el-col :lg="8" :sm="24" class="text-center">
                                        <div class="demo-image__preview">
                                            <el-image style="width: 80px; height: 80px" :src="patient.avatar"
                                                fit="cover" />
                                        </div>
                                    </el-col>
                                    <el-col :lg="16" :sm="24">
                                        <span class=" font-bold"> {{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{
                                            patient.surname }} </span><br>
                                        <span> né{{patient.sex=="M"?"":"e"}} le {{ patient.date_of_birth }} </span><br>
                                        <div class="mt-3 text-center">
                                            <button class="btn btn-sm btn-error mb-2" @click="()=>{patient={}} ">
                                                Changer le client
                                            </button>
                                        </div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div class="" v-if="patient.uid">
                                <el-tabs>
                                    <el-tab-pane :label="'Factures impayées ('+deets.nope.length+')'">
                                        <el-table :data="deets.nope">
                                            <el-table-column prop="uid" label="UID" />
                                            <el-table-column prop="amount" label="Montant" />
                                        </el-table>
                                    </el-tab-pane>
                                    <el-tab-pane :label="'Factures payées partiellement ('+deets.part.length+')'">
                                        <el-table :data="deets.part">
                                            <el-table-column width="150px" prop="uid" label="UID" />
                                            <el-table-column prop="amount" label="Montant" />
                                            <el-table-column prop="paid" label="Payé" />
                                            <el-table-column label="Reste">
                                                <template #default="slot">
                                                    <span> {{ slot.row.amount - slot.row.paid }} </span>
                                                </template>
                                            </el-table-column>
                                        </el-table>
                                    </el-tab-pane>
                                </el-tabs>
                            </div>
                        </div>
                    </el-col>
                </el-row>

                <el-dialog v-model="addPaymentDialog" title="Ajouter un paiement" align-center>
                    <p>Souhaitez-vous lier ce paiement à une facture ou à un patient ?</p>
                    <div class="flex justify-end mt-4">
                        <button class="btn btn-sm background-clickdoc" @click="openAddPaymentFacture">Lier à une
                            facture/patient</button>
                        <button class="btn btn-sm btn-error ml-2" @click="openAddPayment">Ne pas lié</button>
                    </div>
                </el-dialog>

                <el-dialog v-model="addPaymentFacture" title="Ajouter un paiement lié à une facture / patient"
                    align-center>
                    <el-form label-position="top">
                        <div class="flex justify-between gap-5">
                            <!-- Patient Dropdown -->
                            <el-form-item label="Patient" class="w-1/2">
                                <el-select class="w-full" filterable v-model="paiement.patient_id" >
                                <el-option v-for="patient in allPatients" :value="patient.id" :label="patient.name + ' ' + patient.surname " />
                            </el-select>
                            </el-form-item>

                            <!-- Facture UID Dropdown -->
                            <el-form-item label="Facture UID" class="w-1/2">
                                <el-select class="w-full" v-model="paiement.facture_id"
                                    placeholder="Sélectionner une facture">
                                    <el-option v-for="facture in allFacture" :key="facture.id" :label="facture.uid" :value="facture.id" />
                                </el-select>
                            </el-form-item>
                        </div>

                        <!-- Statut Dropdown -->
                        <el-form-item label="Statut">
                            <el-select class="w-full" v-model="paiement.statut">
                                <el-option value="non payé" label="Non Payé" />
                                <el-option value="payé" label="Payé en totalité" />
                                <el-option value="payé partiellement" label="Payé partiellement" />
                            </el-select>
                        </el-form-item>

                        <!-- Modalité de paiement -->
                        <el-form-item label="Modalité de paiement">
                            <el-select class="w-full" v-model="paiement.type">
                                <el-option value="Espèce" label="Espèce" />
                                <el-option value="Chèque" label="Chèque" />
                                <el-option value="Carte bancaire" label="Carte bancaire" />
                            </el-select>
                        </el-form-item>

                        <!-- Montant -->
                        <el-form-item label="Montant">
                            <el-input type="number" v-model="paiement.amount" />
                        </el-form-item>
                    </el-form>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button class="btn btn-sm background-clickdoc" type="button"
                            @click.prevent="async ()=>await impPayment()">
                            Enregistrer
                        </button>
                    </div>
                </el-dialog>




                <el-dialog v-model="addCharge" title="Ajouter une nouvelle charge" align-center>
                    <el-form label-position="top">
                        <el-form-item label="Montant">
                            <el-input type="number" v-model="charge.montant" />
                        </el-form-item>
                        <el-form-item label="Motif">
                            <el-input type="textarea" v-model="charge.motif" />
                        </el-form-item>
                    </el-form>
                    <div class="text-right">
                        <button class="btn btn-sm background-clickdoc" type="button"
                            @click.prevent="async ()=>await impCharge()">
                            Enregistrer
                        </button>
                    </div>
                </el-dialog>
                <el-dialog v-model="addPayment" title="Ajouter nouveau paiement" align-center>
                    <el-form v-model="paiement" label-position="top">
                        <el-form-item label="Modalité de paiement">
                            <el-select class="w-full" v-model="paiement.type">
                                <el-option value="Espèce" label="Espèce" />
                                <el-option value="Chèque" label="Chèque" />
                                <el-option value="Carte bancaire" label="Carte bancaire" />
                            </el-select>
                        </el-form-item>

                        <el-form-item label="Montant">
                            <el-input v-model="paiement.amount" />
                        </el-form-item>

                        <el-form-item label="Motif">
                            <el-input type="textarea" v-model="paiement.motif" />
                        </el-form-item>

                        <el-form-item>
                            <button class="btn btn-sm btn-block background-clickdoc" type="button"
                                @click="async () => await saveFacture()"> Enregistrer </button>
                        </el-form-item>
                    </el-form>
                </el-dialog>

            </div>
        </div>
    </main-layout>
</template>