<script lang="ts" setup>
import { useConsultStore } from '../../../core/Data/stores/consultation';
import { Patients } from '../../../core/Clients/Patients';
import { ExamenPhysique } from '../../../core/Clients/Examen';
import { Diagnostic } from '../../../core/Clients/Diagnostic';
import { Ordonnance } from '../../../core/Clients/Ordonnance';
import { Medicament } from '../../../core/Clients/Medicament';
import { ActeMedical } from '../../../core/Clients/ActeMedical';
import { Demande } from '../../../core/Clients/DemandeAnalyse';
import { Analyses } from '../../../core/Clients/Analyses';
import { Facture } from '../../../core/Clients/Facture';
import { Article } from '../../../core/Clients/Article';
import { Soin } from '../../../core/Clients/Soin';
import { onBeforeMount,ref,Ref,reactive } from 'vue';
import { ElLoading } from 'element-plus';
import { Consultation } from '../../../core/Clients/Consult';
import { useRouter } from 'vue-router';
import ENV from '../../../core/env';
import moment from 'moment';

const router : any = useRouter()
const consult = useConsultStore();
const patient : Ref<any> = ref({})
const finalisation : any = reactive({
    id:consult.consult,
    isFinished:0,
    isPrivate:0
})
const examen : Ref<any> = ref({})
const finaliser : Ref<boolean> = ref(false)

const prescription : Ref<any> = ref({
    consultation_id:consult.consult,
    commentaire:"",
    medicament_id:"",
    dose_id:"",
})
const demande : Ref<any> = ref({
    consultation_id:consult.consult,
    analyse_id:"",
})
const soin : Ref<any> = ref({
    consultation_id:consult.consult,
    nbr_sceances:"",
    acte_id:""
})

const diagnostic : Ref<any> = ref({})
const ordonnance : Ref<any> = ref([])
const demandes : Ref<any> = ref([])
const soins : Ref<any> = ref([])
const medicaments : Ref<any> = ref([])
const doses : Ref<any> = ref([])
const actes : Ref<any> = ref([])
const analyses : Ref<any> = ref([])
const client = new Patients()
const clientConsult = new Consultation()
const ordonnanceClient = new Ordonnance()
const medicamentClient = new Medicament()
const examenClient = new ExamenPhysique()
const analyseClient = new Analyses()
const demandeClient = new Demande()
const diagnosticClient = new Diagnostic()
const acteClient = new ActeMedical()
const soinClient = new Soin()
const factureClient = new Facture()
const articleClient = new Article()


const data_visage = [
    {label:"Douleur à l'oreille (otalgie)",value:"Douleur à l'oreille (otalgie)"},
    {label:"Perte auditive",value:"Perte auditive"},
    {label:"Écoulement de l'oreille (otorrhée)",value:"Écoulement de l'oreille (otorrhée)"},
    {label:"Sensation de plénitude ou de pression dans l'oreille",value:"Sensation de plénitude ou de pression dans l'oreille"},
    {label:"Bourdonnements dans l'oreille (acouphènes)",value:"PBourdonnements dans l'oreille (acouphènes)"},
    {label:"Vertiges ou sensations de vertiges (vertiges)",value:"Vertiges ou sensations de vertiges (vertiges)"}
]
const data_corps  = [
    {label:"Congestion nasale",value:"Congestion nasale"},
    {label:"Écoulement nasal (rhinorrhée)",value:"Écoulement nasal (rhinorrhée)"},
    {label:"Éternuements",value:"Éternuements"},
    {label:"Obstruction nasale",value:"Obstruction nasale"},
    {label:"Perte de l'odorat (anosmie)",value:"Perte de l'odorat (anosmie)"},
    {label:"Sensation de pression faciale ou de douleur (sinusite)",value:"Sensation de pression faciale ou de douleur (sinusite)"},
    {label:"Autres",value:"Autres"}
]
const data_ongles = [
    {label:"Onychomycose",value:"Onychomycose"},
    {label:"Paronychie",value:"Paronychie"},
    {label:"Onycholyse",value:"Onycholyse"},
    {label:"Onychogryphose",value:"Onychogryphose"},
    {label:"Onychomadèse",value:"Onychomadèse"},
    {label:"Leuconychie",value:"Leuconychie"},
    {label:"Pachyonychie",value:"Pachyonychie"},
    {label:"Onychorrhexie",value:"Onychorrhexie"},
    {label:"Onychoschizie",value:"Onychoschizie"},
    {label:"Onychomadesis",value:"Onychomadesis"},
    {label:"Koïlonychie",value:"Koïlonychie"},
    {label:"Onychauxis",value:"Onychauxis"},
    {label:"Onychite",value:"Onychite"},
    {label:"Hématome sous-unguéal",value:"Hématome sous-unguéal"},
    {label:"Onychocryptose (ongle incarné)",value:"Onychocryptose (ongle incarné)"},
    {label:"Onychophagie (rongement des ongles)",value:"Onychophagie (rongement des ongles)"},
    {label:"Onychotillomanie (arrachement compulsif des ongles)",value:"Onychotillomanie (arrachement compulsif des ongles)"},
    {label:"Trou d'épingle",value:"Trou d'épingle"},
    {label:"Lignes de Beau Pitting",value:"Lignes de Beau Pitting"},
    ]
const data_cheveux= [
    {label:"Alopécie",value:"Alopécie"},
    {label:"Pellicules (dermatite séborrhéique)",value:"Pellicules (dermatite séborrhéique)"},
    {label:"Calvitie (alopécie androgénétique)",value:"Calvitie (alopécie androgénétique)"},
    {label:"Alopécie areata",value:"Alopécie areata"},
    {label:"Traction alopécie",value:"Traction alopécie"},
    {label:"Perte de cheveux due au stress (effluvium télogène)",value:"Perte de cheveux due au stress (effluvium télogène)"},
    {label:"Perte de cheveux due à la chimiothérapie",value:"Perte de cheveux due à la chimiothérapie"},
    {label:"Alopécie cicatricielle",value:"Alopécie cicatricielle"},
    {label:"Poux de tête (pédiculose capitis)",value:"Poux de tête (pédiculose capitis)"},
    {label:"Trichotillomanie (arrachement compulsif des cheveux)",value:"Trichotillomanie (arrachement compulsif des cheveux)"},
    {label:"Perte de cheveux due à une carence nutritionnelle",value:"Perte de cheveux due à une carence nutritionnelle"},
    {label:"Alopécie de la couronne (effluvium anagène)",value:"Alopécie de la couronne (effluvium anagène)"},
    {label:"Perte de cheveux due à une maladie du cuir chevelu",value:"Perte de cheveux due à une maladie du cuir chevelu"},
    {label:"Hypertrichose",value:"Hypertrichose"},
    {label:"Perte de cheveux due à une maladie systémique",value:"Perte de cheveux due à une maladie systémique"},
    {label:"Perte de cheveux due à des médicaments",value:"Perte de cheveux due à des médicaments"},
    {label:"Perte de cheveux liée à l'âge",value:"Perte de cheveux liée à l'âge"},
    {label:"Cheveux cassants et fragiles",value:"Cheveux cassants et fragiles"},
    {label:"Chute de cheveux saisonnière",value:"Chute de cheveux saisonnière"},
    {label:"Cheveux gras (séborrhée)",value:"Cheveux gras (séborrhée)"}
]

async function getPatient(){
    return await client.getByUID(consult.patient_id)
}

async function getExamenPhysique(){
    const data :any =  await examenClient.getByID(consult.examen_id)
    return {
        id:data.id,
        hair : JSON.parse(data.hair),
        nails : JSON.parse(data.nails),
        face : JSON.parse(data.face),
        body : JSON.parse(data.body)
    }
}
async function setExamenPhysique(){
     await examenClient.update(examen.value)
     examen.value = await getExamenPhysique()
}

async function setDiagnostic(){
    await diagnosticClient.update(diagnostic.value)
    diagnostic.value = await getDiagnostic()
}

async function getDiagnostic(){
    return  await diagnosticClient.getByID(consult.diagnostic_id)
}
async function getOrdonnance(){
    return  await ordonnanceClient.getByID(consult.consult)
}

async function setOrdonnance() {
    await ordonnanceClient.add(prescription.value)
    ordonnance.value = await getOrdonnance()
    prescription.value.medicament_id=""
    prescription.value.dose_id=""
    prescription.value.commentaire=""
    doses.value=[]
}

async function removeOrdonnance(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await ordonnanceClient.delete(x) 
    }
    ordonnance.value = await getOrdonnance()
        
}

async function getSoins(){
    return  await soinClient.getAll(consult.consult)
}
async function setSoins(){
    await soinClient.add(soin.value)
    soins.value = await getSoins()
    soin.value.acte_id=""
    soin.value.nbr_sceances=""
}
async function addSoins(x:any){
    await soinClient.update(x)
    soins.value = await getSoins()
}

async function removeSoin(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await soinClient.delete(x) 
    }
    soins.value = await getSoins()
        
}

async function getDemande(){
    return  await demandeClient.getAll(consult.consult)
}
async function setDemande(){
    await demandeClient.add(demande.value)
    demandes.value = await getDemande()
    demande.value.analyse_id=""
}

async function removeDemande(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await demandeClient.delete(x) 
    }
    demandes.value = await getDemande()
}

async function saveConsultation(){
    await clientConsult.update(finalisation)
    router.push("/consultations")
}

async function initPage(){
    const loading = ElLoading.service({
        lock: true,
        text: 'Loading',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    patient.value = await getPatient()
    examen.value = await getExamenPhysique()
    diagnostic.value = await getDiagnostic()
    ordonnance.value = await getOrdonnance()
    medicaments.value = await medicamentClient.getAll();
    demandes.value = await getDemande()
    actes.value = await acteClient.getAll()
    soins.value = await getSoins()
    analyses.value = await analyseClient.getAll()
    if(consult.edit==false)
    {
        await getFacture()
    }
    loading.close()
}

onBeforeMount(async ()=>{
    await initPage()
})

const facture : Ref<any> = ref({
    facture:{},
    doctor_fee:[],
    liste:[],
    autres:[]
})

const article : Ref<any> = ref({
    facture_id:consult.facture_id,
    libelle:"",
    prix:0,
    type:1
})

async function getFacture() {
    facture.value=await factureClient.facture(consult.consult)
    console.log("facture.value")
    console.log(facture.value)
}

async function generateFacture() {
    facture.value=await factureClient.add({consultation_id:consult.consult})
}

async function saveFacture() {
    facture.value.facture.amount=total();
    await factureClient.update(facture.value.facture)
    await getFacture()
}

async function addArticle() {
    if(article.value.id==undefined)
    {
        await articleClient.add(article.value)
    }
    else
    {
        await articleClient.update(article.value)
    }
    article.value={
        facture_id:consult.facture_id,
        libelle:"",
        prix:0,
        type:1
    }
    await getFacture();
}
async function removeArticle(x:any) {
    await articleClient.delete(x)
    await getFacture();
}

function total(){
    var t = 0;
    for (var i =0;i< facture.value.liste.length;i++ )
    {
        t=t+parseFloat(facture.value.liste[i].prix)
    }

    return t;
}

</script>

<template>
    <main-layout >
        <ui-sheet :title="'Consultation '+consult.consult_uid ">
            <el-row :gutter="10">
                <el-col :span="7">
                    <ui-sheet :title="patient.uid"  :noHeader="true" >
                        <el-row>
                            <el-col :span="24" class="text-center">
                                <div class="demo-image__preview">
                                    <el-image
                                    
                                    :src="patient.avatar"
                                    fit="cover"
                                    />
                                </div>
                            </el-col>
                            <el-col :span="24" class="text-center">
                                <span class="text-lg font-bold"> {{ patient.sex=="M"?"M.":"Mme"}} {{patient.name }} {{ patient.surname }} </span><br>
                                <span> né{{patient.sex=="M"?"":"e"}} le {{ patient.date_of_birth }}  </span><br>
                                <span> CIN : {{patient.CIN}} </span> <br>
                                <span> Téléphone : {{patient.phone}} </span> <br>
                            </el-col>
                        </el-row>
                        <el-button @click="finaliser=true" class="btn-block mt-4" type="primary"  v-if="consult.edit==true">
                            Finaliser et enregistrer
                        </el-button>
                    </ui-sheet>
                </el-col>
                <el-col :span="17">
                    <el-tabs type="border-card">
                        <el-tab-pane label="Examen Physique">
                            <el-form label-position="top">
                                <el-form-item label="Visage">
                                    <el-select-v2
                                        v-model="examen.face"
                                        :options="data_visage"
                                        placeholder="Selectionner"
                                        multiple
                                        class="w-full"
                                        clearable
                                        @change="async ()=>{await setExamenPhysique()}"
                                        :disabled="!consult.edit"
                                    />
                                </el-form-item>
                                <el-form-item label="Corps">
                                    <el-select-v2
                                        v-model="examen.body"
                                        :options="data_corps"
                                        placeholder="Selectionner"
                                        multiple
                                        class="w-full"
                                        clearable
                                        @change="async()=>{await setExamenPhysique()} "
                                        :disabled="!consult.edit"
                                   />
                                </el-form-item>
                                <el-form-item label="Ongles">
                                    <el-select-v2
                                        v-model="examen.nails"
                                        :options="data_ongles"
                                        placeholder="Selectionner"
                                        multiple
                                        class="w-full"
                                        clearable
                                        @change="async ()=>{await setExamenPhysique()}"
                                        :disabled="!consult.edit"
                                    />
                                </el-form-item>
                                <el-form-item label="Cheveux">
                                    <el-select-v2
                                        v-model="examen.hair"
                                        :options="data_cheveux"
                                        placeholder="Selectionner"
                                        multiple
                                        class="w-full"
                                        clearable
                                        @change="async ()=>{await setExamenPhysique()}"
                                        :disabled="!consult.edit"
                                    />
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="Diagnostic">
                            <el-form label-position="top">
                                <el-form-item label="Diagnostic">
                                    <el-input type="textarea" :disabled="!consult.edit" v-model="diagnostic.diagnostic" @change="async ()=>{await setDiagnostic()}" :autosize="{ minRows: 10, maxRows: 20 }" />
                                </el-form-item>
                            </el-form>
                        </el-tab-pane>
                        <el-tab-pane label="Ordonnance">
                            <el-form label-position="top" v-if="consult.edit==true">
                                <el-row :gutter="10" >
                                    <el-col :span="7">
                                        <el-form-item label="Medicament">
                                            <el-select class="w-full" v-model="prescription.medicament_id" @change="async () => { doses = await medicamentClient.getByID(prescription.medicament_id)  }" >
                                                <el-option 
                                                    v-for="m in medicaments"
                                                    :key="m.id"
                                                    :value="m.id"
                                                    :label="m.nom"
                                                />
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="7">
                                        <el-form-item label="Dose">
                                            <el-select class="w-full" v-model="prescription.dose_id" >
                                                <el-option 
                                                    v-for="d in doses"
                                                    :key="d.id"
                                                    :value="d.id"
                                                    :label="d.dose+' '+d.unit"
                                                />
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="7">
                                        <el-form-item label="Commentaire">
                                            <el-input v-model="prescription.commentaire" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="3">
                                        <el-form-item label=" &nbsp ">
                                            <el-button @click="async ()=>{await setOrdonnance()}" class="w-full" type="primary"> <el-icon><Select /></el-icon> </el-button>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-form>
                            <hr class="my-3">
                            <el-table :data="ordonnance" :border="true">
                                <el-table-column label="Médicament">
                                    <template #default="scope">
                                        {{ scope.row.medicament }} ({{ scope.row.dose }} {{ scope.row.unit }} )
                                    </template>
                                </el-table-column>
                                <el-table-column label="Commentaire" prop="commentaire"  />
                                <el-table-column width="50px">
                                    <template #default="scope">
                                        <el-button link type="danger" @click="async ()=>{ await removeOrdonnance(scope.row.id) }"><el-icon><Delete/></el-icon></el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <div class="text-right">
                                <a class="btn btn-sm btn-link" target="_blank" :href="ENV.VITE_BACKEND+'/ordonnance/'+consult.consult"> <el-icon> <Printer/></el-icon> Imprimer </a>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Analyses">
                            <el-form label-position="top" v-if="consult.edit==true">
                                <el-row :gutter="10" >
                                    <el-col :span="21">
                                        <el-form-item label="Analyse">
                                            <el-select class="w-full" v-model="demande.analyse_id"  >
                                                <el-option 
                                                    v-for="m in analyses"
                                                    :key="m.id"
                                                    :value="m.id"
                                                    :label="m.libelle"
                                                />
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="3">
                                        <el-form-item label=" &nbsp ">
                                            <el-button @click="async ()=>{await setDemande()}" class="w-full" type="primary"> <el-icon><Select /></el-icon> </el-button>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-form>
                            <hr class="my-3">
                            <el-table :data="demandes" :border="true">
                                <el-table-column label="Analyse" prop="libelle" />
                                <el-table-column label="Etat" prop="state" />
                                <el-table-column label="Document" prop="document">
                                    <template #default="scope">
                                        <a v-if="scope.row.document!=null" class="btn btn-link btn-accent btn-sm"> <el-icon> <Folder /></el-icon> voir </a>
                                    </template>
                                </el-table-column>
                                <el-table-column width="50px">
                                    <template #default="scope">
                                        <el-button link type="danger"  v-if="scope.row.state=='soumise'" @click="async ()=>{ await removeDemande(scope.row.id) }"><el-icon><Delete/></el-icon></el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </el-tab-pane>
                        <el-tab-pane label="Traitements">
                            <el-form label-position="top" v-if="consult.edit==true">
                                <el-row :gutter="10" >
                                    <el-col :span="14">
                                        <el-form-item label="Soin/Acte médical">
                                            <el-select class="w-full" v-model="soin.acte_id" @change="async () => { doses = await medicamentClient.getByID(prescription.medicament_id)  }" >
                                                <el-option 
                                                    v-for="a in actes"
                                                    :key="a.id"
                                                    :value="a.id"
                                                    :label="a.libelle"
                                                />
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="7">
                                        <el-form-item label="Nombre de scéances">
                                            <el-input v-model="soin.nbr_sceances" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="3">
                                        <el-form-item label=" &nbsp ">
                                            <el-button @click="async ()=>{await setSoins()}" class="w-full" type="primary"> <el-icon><Select /></el-icon> </el-button>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-form>
                            <hr class="my-3">
                            <el-table :data="soins" :border="true">
                                <el-table-column label="Libellé" prop="soin"  />
                                <el-table-column label="Nombre de scéances" prop="nbr_sceances"  />
                                <el-table-column label="Scéances faites" >
                                    <template #default="scope">
                                        {{scope.row.nbr_performed.length}}
                                    </template>
                                </el-table-column>
                                <el-table-column label="Historique" >
                                    <template #default="scope">
                                        <ul>
                                            <li v-for="h in scope.row.nbr_performed" :key="h.id">
                                                {{ moment(h.created_at).format("DD/MM/yyyy") }}
                                            </li>
                                        </ul>
                                    </template>
                                </el-table-column>
                                <el-table-column width="100px">
                                    <template #default="scope">
                                        <el-button link type="primary" v-if="scope.row.nbr_sceances>scope.row.nbr_performed.length" @click=" async()=>{ await addSoins(scope.row.id) }"><el-icon><Plus/></el-icon></el-button>
                                        <el-button link type="danger" v-if="scope.row.nbr_performed==0" @click="async ()=>{ await removeSoin(scope.row.id) }"><el-icon><Delete/></el-icon></el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </el-tab-pane>
                        <el-tab-pane label="Agenda" v-if="consult.edit==true">
                            <ui-agenda />
                        </el-tab-pane>
                        <el-tab-pane label="Facture" v-if="consult.edit==false">
                            <div v-if="facture.facture!=undefined">
                                <el-form label-position="top" v-if="facture.facture.amount==0">
                                    <el-row :gutter="10">
                                        <el-col :span="12">
                                            <el-form-item label="libellé">
                                                <el-input v-model="article.libelle" />
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="4">
                                            <el-form-item label="Prix">
                                                <el-input v-model="article.prix" />
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="4">
                                            <el-form-item label="Type">
                                                <el-select v-model="article.type">
                                                    <el-option
                                                        :value="1"
                                                        label="Consultation"
                                                    />
                                                    <el-option
                                                        :value="2"
                                                        label="Acte médical"
                                                    />
                                                    <el-option
                                                        :value="2"
                                                        label="Autre"
                                                    />
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="4">
                                            <el-form-item label="&nbsp">
                                                <el-button class="w-full" type="primary" @click="async()=>{await addArticle()}"> Ajouter </el-button>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </el-form>
                                <br>
                                <el-table :data="facture.liste" >
                                    <el-table-column prop="libelle" label="Libellé" />
                                    <el-table-column prop="prix" label=" Prix" width="150px" />
                                    <el-table-column width="100px">
                                        <template #default="scope">
                                            <el-button link type="primary" @click="()=>{article=scope.row;facture.liste.splice(scope.index,-1)}" ><el-icon><Edit/></el-icon></el-button>
                                            <el-button link type="danger" @click="async ()=>{ await removeArticle(scope.row.id) }"><el-icon><Delete/></el-icon></el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                                <br>
                                <div class="text-right">
                                    total : {{ total() }}
                                </div>
                                <hr class="my-3">
                                <div class="text-right">
                                    <el-button type="primary" @click="async()=>await saveFacture()" v-if="facture.facture.amount==0">
                                        Enregistrer la facture 
                                    </el-button>
                                    <el-button type="primary" @click="async()=>await saveFacture()" v-else="facture.facture.amount==0">
                                        <el-icon><Printer/></el-icon>Imprimer la facture 
                                    </el-button>
                                </div>
                            </div>
                            <div class="text-center" v-else>
                                <el-button type="primary" @click="async ()=> await generateFacture()">
                                    Générer facture
                                </el-button>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </el-col>
            </el-row>
            <el-dialog
                v-model="finaliser"
                title="Finaliser et enregistrer"
            >
                Voulez vous laisser d'autres docteurs du réseau Clickdoc voir votre consultation/diagnostic? <br><br>
                <el-row :gutter="10">
                    <el-col :span="12" class="text-center">
                        <el-button type="danger" class="btn-block" @click="async () => { finalisation.isPrivate=true ; await saveConsultation()  }"> 
                            <el-icon>
                                <Lock/>  
                            </el-icon>&nbsp;&nbsp;&nbsp;&nbsp; Garder privé 
                        </el-button>
                    </el-col>
                    <el-col :span="12" class="text-center" @click="async () => { finalisation.isPrivate=false ; await saveConsultation()  }">
                        <el-button type="success"  class="btn-block"> 
                            <el-icon>
                                <Unlock/>  
                            </el-icon>&nbsp;&nbsp;&nbsp;&nbsp; Laisser public
                        </el-button>
                    </el-col>
                </el-row>
            </el-dialog>
        </ui-sheet>
    </main-layout>
</template>