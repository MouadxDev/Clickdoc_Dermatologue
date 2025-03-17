//css import
import 'element-plus/dist/index.css';
import 'primevue/resources/themes/lara-light-indigo/theme.css';
import 'ant-design-vue/dist/reset.css';
import './style.css';

import { createApp } from 'vue';

import {Qalendar} from 'qalendar';
import VueGapi from 'vue-gapi'



import ElementPlus from 'element-plus';
import { createPinia } from 'pinia';
import piniaPluginPersistedState from 'pinia-plugin-persistedstate';
import PrimeVue from 'primevue/config';

import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import * as AntIconsVue from '@ant-design/icons-vue';

import App from './App.vue';


//Components du dosier
import AntMedicaux from './components/Folder/AntMedicaux.vue';
import AntChirurgicaux from './components/Folder/AntChirurgicaux.vue';
import Imagerie from './components/Folder/Imagerie.vue';
import AntFamiliaux from './components/Folder/AntFamiliaux.vue';
import AntAllergies from './components/Folder/AntAllergies.vue';
import AntDrugs from './components/Folder/AntDrugs.vue';
import Antecedents from './components/Folder/Antecedents.vue';
import Consultations from './components/Folder/Consultations.vue';
import Ordonnances from './components/Folder/Ordonnances.vue';
import RendezVous from './components/Folder/RendezVous.vue';
import Finance from './components/Folder/Finance.vue';
import Analyses from './components/Folder/Analyses.vue';
import TableauPersonnalise from './components/Folder/TableauPersonalise.vue';


//Components de la consultation
import ExamenPhysique from './components/Consult/ExamenPhysique.vue'
import Diagnostic from './components/Consult/Diagnostic.vue'
import Ordonnance from './components/Consult/Ordonnance.vue'
import Analyse from './components/Consult/Analyse.vue'
import Soins from './components/Consult/Soins.vue'
import Facture from './components/Consult/Facture.vue'
import Details from './components/Consult/Details.vue'
import Observation from './components/Consult/Observation.vue'

import Layout from './layouts/MainLayout.vue';
//import Layout from './layouts/Layout.vue';

// custom stuff
import router from './router';
import uiMenuVue from './components/Ui/ui-menu.vue';
import uiSheetVue from './components/Ui/ui-sheet.vue';
import uiTableVue from './components/Ui/ui-table.vue';
import uiAgendaVue from './components/Ui/ui-agenda.vue';
import uiAccordionVue from './components/Ui/ui-accordion.vue';



// utilities
import addPatientVue from './components/Utilitaires/AddPatient.vue'
import editPatientVue from './components/Utilitaires/EditPatient.vue'
import addRDVVue from './components/Utilitaires/AddRDV.vue'
import TraitementVue from './components/Folder/Traitement.vue';


// TASKS
import TasksVue from './components/Tasks.vue';


const app = createApp(App)

const pinia = createPinia();
pinia.use(piniaPluginPersistedState)

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}
for (const [key, component] of Object.entries(AntIconsVue)) {
    app.component(key, component)
}



app.component("Qalendar",Qalendar)

app.component("main-layout",Layout)

app.component("ui-menu",uiMenuVue)
app.component("ui-sheet",uiSheetVue)
app.component("ui-table",uiTableVue)
app.component("ui-agenda",uiAgendaVue)
app.component("ui-accordion",uiAccordionVue)



app.component("ant-medicaux",AntMedicaux)
app.component("ant-chirurgicaux",AntChirurgicaux)
app.component("ant-familiaux",AntFamiliaux)
app.component("ant-allergies",AntAllergies)
app.component("ant-drugs",AntDrugs)
app.component("consultations",Consultations)
app.component("ant-ordonnances",Ordonnances)
app.component("rendez-vous",RendezVous)
app.component("ants",Antecedents)
app.component("imagerie",Imagerie)
app.component("analyses",Analyses)
app.component("tableau-personnalise",TableauPersonnalise)


app.component("examen-physique",ExamenPhysique)
app.component("diagnostique",Diagnostic)
app.component("ordonnance",Ordonnance)
app.component("analyse",Analyse)
app.component("soins",Soins)
app.component("honoraires",Facture)
app.component("details",Details)
app.component("observation",Observation)
app.component("sit-financiere",Finance)
app.component("traitement",TraitementVue)


app.component("add-patient",addPatientVue)
app.component("edit-patient",editPatientVue)
app.component("add-rdv",addRDVVue)

app.component("tasks",TasksVue)


app.use(router)
app.use(pinia)
app.use(PrimeVue)
app.use(VueGapi, {
    apiKey: 'AIzaSyANE63cFPemidtRqcRWI2y4zuSXLonm23k',
    clientId: '902011582039-muto50mnapeho4a51ngdhmhmkd41646p.apps.googleusercontent.com',
    discoveryDocs:['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'],
    scope : 'https://www.googleapis.com/auth/calendar',
    plugin_name:'clickdoc'
  })

app.use(ElementPlus)
app.mount('#app')