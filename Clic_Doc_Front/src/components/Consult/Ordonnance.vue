<script setup lang="ts">

import { Ref , onBeforeMount, ref } from "vue";
import {useConsultStore} from "../../../core/Data/stores/consultation"
import { Ordonnance } from '../../../core/Clients/Ordonnance';
import { Medicament } from '../../../core/Clients/Medicament';
import ENV from '../../../core/env'
import { computed, RefSymbol } from "@vue/reactivity";
import { LaboMedicament } from "../../../core/Clients/LaboMed";
import { ElMessage } from "element-plus";
import { watch } from 'vue';

const consult = useConsultStore();
const ordonnanceClient = new Ordonnance()
const medicamentClient = new Medicament()
const laboMedicamentClient = new LaboMedicament();
const medicaments : Ref<any> = ref([])
const laboratoire_list : Ref<any> = ref([])

const showContent = ref(false);
const inputMedicament = ref(""); 

//Filter Logic
const searchTerm: Ref<string> = ref('');

const filteredMedicaments = computed(() => {
  return medicaments.value.filter((medicament: any) => {
    const matchesLab = prescription.value.laboratoire
      ? medicament.lab === prescription.value.laboratoire
      : true;
    const matchesSearch = medicament.nom
      .toLowerCase()
      .includes(searchTerm.value.toLowerCase());
    return matchesLab && matchesSearch;
  });
});

const prescription : Ref<any> = ref({
    consultation_id:consult.consult,
    commentaire:"",
    medicament_id:"",
    dose_id:"",
})

async function getOrdonnance(){
    return  await ordonnanceClient.getByID(consult.consult)
}

const handleShowContent = () => {
  showContent.value = !showContent.value;
};

const ordonnance : Ref<any> = ref([])
const doses : Ref<any> = ref([])

// Watch for changes in the laboratory selection
watch(() => prescription.value.laboratoire, (newLab, oldLab) => {
  if (newLab !== oldLab) {
    prescription.value.medicament_id = ""; // Clear the Medicament selection
  }
});


async function setOrdonnance() {
  // Ensure a medicament is selected
  if (!prescription.value.medicament_id) {
    ElMessage.error("Veuillez sélectionner un médicament.");
    return;
  }

  // Ensure required fields in the modal are provided
  // if (!modalForm.value.unit || !modalForm.value.administrationMode || !modalForm.value.frequency) {
  //   ElMessage.error("Veuillez remplir les champs obligatoires (Unité, Mode d'administration, Fréquence).");
  //   return;
  // }

  // Prepare data to send
  const ordonnanceData = {
    consultation_id: prescription.value.consultation_id,
    medicament_id: prescription.value.medicament_id,
    commentaire: modalForm.value.comment || "",
    administration_mode: modalForm.value.administrationMode,
    duration_value: modalForm.value.durationValue || null,
    duration_unit: modalForm.value.durationUnit || null,
    frequency: modalForm.value.frequency,
    contraindications: modalForm.value.contraindications, // Send as an array
    matin: doseValues.value.Matin || 0,
    midi: doseValues.value.Midi || 0,
    soir: doseValues.value.Soir || 0,
    au_coucher: doseValues.value["Au coucher"] || 0,
  };

  // Save data using API
  try {
    await ordonnanceClient.add(ordonnanceData);

    // Refresh ordonnance list
    ordonnance.value = await getOrdonnance();

    // Reset fields
    prescription.value.medicament_id = "";
    prescription.value.commentaire = "";
    modalForm.value = {
      unit: "",
      administrationMode: "",
      frequency: "",
      durationValue: null,
      durationUnit: "",
      comment: "",
      contraindications: [], // Reset as empty array
    };
    doses.value = [];
  } catch (error) {
    ElMessage.error("Une erreur s'est produite lors de l'enregistrement des données.");
    console.error(error);
  }
}

async function addMore() {
  const medicamentName = inputMedicament.value;

  if (!medicamentName) {
    ElMessage.error("Veuillez entrer un nom de médicament.");
    return;
  }

  const dataToSend = {
    nom: medicamentName,
    lab_id: 0,  
    prix: 0, 
};

  try {
    await medicamentClient.add(dataToSend);
    inputMedicament.value = "";
    ElMessage.success("Médicament ajouté avec succès.");
    medicaments.value = await medicamentClient.getAll();
    
  } catch (error) {
    ElMessage.error("Une erreur s'est produite lors de l'ajout du médicament.");
    console.error(error);
  }
}

async function removeOrdonnance(x:number) {
    if(confirm('êtes vous sur de vouloir supprimer cet element')==true )
    {
        await ordonnanceClient.delete(x) 
    }
    ordonnance.value = await getOrdonnance()
}

onBeforeMount(async()=>{
    medicaments.value = await medicamentClient.getAll();
    console.log(medicaments.value)
    laboratoire_list.value = await laboMedicamentClient.getAll();
    
    ordonnance.value = await getOrdonnance()
})



// Modal visibility state


const showModal = ref(false);
const modalForm = ref({
  unit: "",
  administrationMode: "",
  frequency: "",
  durationValue: null,
  durationUnit: "",
  comment: "",
  contraindications:"",
});

const timingOptions: { label: "Matin" | "Midi" | "Soir" | "Au coucher"; icon: string }[] = [
  { label: "Matin", icon: "☕" },
  { label: "Midi", icon: "🍴" },
  { label: "Soir", icon: "🌙" },
  { label: "Au coucher", icon: "🛌" },
];


const frequencyOptions = ["Par minute", "Par heure", "Par jour", "Par semaine", "Par mois", "Par an"];
const durationUnits = ["Minute(s)", "Heure(s)", "Jour(s)", "Semaine(s)", "Mois", "Année(s)"];
const unitOptions = [
  "mg", "ml", "g", "unité(s)", "tablette(s)", "capsule(s)", "ampoule(s)", 
  "suppositoire(s)", "sachet(s)", "goutte(s)", "cuillère(s)", "flacon(s)"
];
const commentOptions = [
  "A prendre à jeun",
  "A prendre avant le repas",
  "A prendre pendant le repas",
  "A prendre après le repas",
];
const administrationModes = [
  "Orale", "Intraveineuse", "Intramusculaire", "Sous-cutanée", 
  "Topique", "Inhalée", "Rectale", "Sublinguale", "Transdermique"
];

function handleSaveModalData() {
  console.log("Modal data saved:", modalForm.value);
  showModal.value = false;
  const allData = {
    doseValues: doseValues.value, // Contains dose counts for "Matin", "Midi", etc.
    modalForm: modalForm.value, // Contains form data like unit, frequency, etc.
  };

  console.log("All stored data:", allData);

  showModal.value = false; // Close the modal

  setOrdonnance()

}
const doseValues = ref<Record<"Matin" | "Midi" | "Soir" | "Au coucher", number>>({
  Matin: 0,
  Midi: 0,
  Soir: 0,
  "Au coucher": 0,
});

function incrementDose(label: "Matin" | "Midi" | "Soir" | "Au coucher") {
  if (doseValues.value[label] !== undefined) {
    doseValues.value[label]++;
  }
}

function decrementDose(label: "Matin" | "Midi" | "Soir" | "Au coucher") {
  if (doseValues.value[label] !== undefined && doseValues.value[label] > 0) {
    doseValues.value[label]--;
  }
}
const contraindicationOptions = ref([
  "Allergie au médicament",
  "Insuffisance rénale",
  "Insuffisance hépatique",
  "Grossesse",
  "Allaitement",
  "Interaction avec d'autres médicaments",
]);

function addContraindication(newContraindication: string) {
  if (
    newContraindication &&
    !contraindicationOptions.value.includes(newContraindication)
  ) {
    contraindicationOptions.value.push(newContraindication);
  }
}

function handleShowModal() {
    if (prescription.value.medicament_id) {
        showModal.value = true;
    } else {
        ElMessage.error('Veuillez sélectionner un médicament.');
    }
}


</script>
<template>
    <div class="container">
        <el-form label-position="top" >
            <el-row :gutter="10" >
                <el-col :span="12">
                    <el-form-item label="Medicament">
                        <el-select 
                            class="w-full" 
                            v-model="prescription.medicament_id" 
                            placeholder="Rechercher un médicament"
                            filterable
                        >
                            <el-option
                                v-for="m in filteredMedicaments"
                                :key="m.id"
                                :value="m.id"
                                :label="m.nom"
                            />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Laboratoire">
                            <el-select
                                v-model="prescription.laboratoire"
                                placeholder="Sélectionner laboratoire"
                                filterable
                            >
                                <el-option
                                v-for="laboratoire in laboratoire_list"
                                :key="laboratoire.id"
                                :value="laboratoire.name"
                                :label="laboratoire.name"
                                />
                            </el-select>
                </el-form-item>
                </el-col>
                <!-- <el-col :span="5">
                    <el-form-item label="Posologie (fois/jour)">
                        <el-input v-model="prescription.posologie" />
                    </el-form-item>
                </el-col> -->
                <el-col :span="4">
                <el-form style="display: flex; gap: 10px;">
                    <el-form-item label=" &nbsp ">
                      <el-button type="primary" size="small" @click="handleShowModal" class="btn-gear">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                          <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                          <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                        </svg>
                      </el-button>
                    </el-form-item>

                    <el-form-item label=" &nbsp ">
                      <el-button type="primary" size="small" @click="handleShowContent" class="btn-add">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                      </el-button>
                    </el-form-item>

                    <el-form-item label=" &nbsp ">
                      <el-button @click="async ()=>{await setOrdonnance()}" class="btn btn-sm btn-block background-clickdoc" type="button">
                        <el-icon><Select /></el-icon>
                      </el-button>
                    </el-form-item>
                  </el-form>
                </el-col>

                <el-col :span="24" v-if="showContent">
                  <el-form-item>
                    <el-input
                      v-model="inputMedicament"
                      placeholder="Ajoutez un Medicament">
                      <template #append>
                        <el-button size="small" @click="addMore()">Ajouter</el-button>
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>
                
            </el-row>
        </el-form>
        <hr class="my-3">
        <div class="table-container">

        
        <el-table :data="ordonnance" :border="true">
                <el-table-column label="Médicament"   width="300">
                    <template #default="scope">
                    {{ scope.row.medicament }}
                    </template>
                </el-table-column>
                <el-table-column label="Mode d'administration"  width="300">
                    <template #default="scope">
                    {{ scope.row.administration_mode }}
                    </template>
                </el-table-column>
                <el-table-column label="Fréquence" width="300">
                    <template #default="scope">
                    {{ scope.row.frequency }}
                    </template>
                </el-table-column>
                <el-table-column label="Durée" width="300">
                    <template #default="scope">
                    {{ scope.row.duration_value }} {{ scope.row.duration_unit }}
                    </template>
                </el-table-column>
                <el-table-column label="Commentaire" width="300">
                    <template #default="scope">
                    {{ scope.row.commentaire }}
                    </template>
                </el-table-column>
                <!-- <el-table-column label="Contre-indications" width="300">
                    <template #default="scope">
                    {{ scope.row.contraindications || 'Aucune' }}
                    </template>
                </el-table-column> -->
                <el-table-column label="Timing" width="300">
                    <template #default="scope">
                    Matin: {{ scope.row.matin || 0 }}, 
                    Midi: {{ scope.row.midi || 0 }}, 
                    Soir: {{ scope.row.soir || 0 }}, 
                    Au coucher: {{ scope.row.au_coucher || 0 }}
                    </template>
                </el-table-column>
                <el-table-column width="75px" >
                    <template #default="scope">
                    <el-button 
                        class="btn btn-sm btn-danger background-clickdoc" 
                        type="button" 
                        @click="async ()=>{ await removeOrdonnance(scope.row.id) }"
                    >
                        <el-icon><Delete /></el-icon>
                    </el-button>
                    </template>
                </el-table-column>
        </el-table>
    </div>

        <div class="text-right">
            <a class="btn btn-sm btn-link" target="_blank" :href="ENV.VITE_BACKEND+'/ordonnance/'+consult.consult"> <el-icon> <Printer/></el-icon> Imprimer </a>
        </div>
    </div>
    <!-- Modal -->
    <el-dialog
    title="Configurer les détails du médicament"
    v-model="showModal"
    width="700px"
    :close-on-click-modal="false"
    class="custom-dialog"
  >
    <div class="modal-body">
      <!-- Dosage Timing -->
      <div class="dose-grid">
        <div 
            v-for="time in timingOptions" 
            :key="time.label"
            class="dose-card"
        >
            <div class="dose-content">
            <div class="dose-icon">{{ time.icon }}</div>
            <div class="dose-label">{{ time.label }}</div>
            <div class="dose-controls">
                <!-- Decrement Button -->
                <button class="control-btn" @click="decrementDose(time.label)">
                <span class="minus-icon">−</span>
                </button>
                <!-- Display Current Dose Value -->
                <span class="dose-value">{{ doseValues[time.label] }}</span>
                <!-- Increment Button -->
                <button class="control-btn" @click="incrementDose(time.label)">
                <span class="plus-icon">+</span>
                </button>
            </div>
            <div class="dose-units">dose(s)</div>
            </div>
        </div>
        </div>


      <!-- Main Form -->
      <div class="form-container">
        <!-- Unit & Administration -->
        <div class="form-group">
          <label>Unité</label>
          <el-select 
          v-model="modalForm.unit" 
          placeholder="Sélectionnez une unité" 
          class="unit-select" 
          filterable
        >
          <el-option
            v-for="unit in unitOptions"
            :key="unit"
            :label="unit"
            :value="unit"
          />
        </el-select>
        </div>

        <div class="form-group">
          <label>Par voie</label>
          <el-select 
          v-model="modalForm.administrationMode" 
          placeholder="Filtrer par voie" 
          class="admin-input" 
          filterable
        >
          <el-option
            v-for="mode in administrationModes"
            :key="mode"
            :label="mode"
            :value="mode"
          />
        </el-select>

        </div>

        <!-- Frequency -->
        <div class="form-group">
          <label>Fréquence</label>
          <div class="button-grid">
            <button
              v-for="option in frequencyOptions"
              :key="option"
              :class="['option-button', { active: modalForm.frequency === option }]"
              @click="modalForm.frequency = option"
            >
              {{ option }}
            </button>
          </div>
        </div>

        <!-- Duration -->
        <div class="form-group">
          <label>Durée</label>
          <div class="duration-container">
            <el-input-number
              v-model="modalForm.durationValue"
              :min="0"
              controls-position="right"
              class="duration-input"
            />
            <div class="button-grid duration-buttons">
              <button
                v-for="unit in durationUnits"
                :key="unit"
                :class="['option-button', { active: modalForm.durationUnit === unit }]"
                @click="modalForm.durationUnit = unit"
              >
                {{ unit }}
              </button>
            </div>
          </div>
        </div>

        <!-- Comments -->
        <div class="form-group">
          <label>Commentaire (Produit)</label>
          <div class="button-grid comments-grid">
            <button
              v-for="comment in commentOptions"
              :key="comment"
              :class="['option-button', { active: modalForm.comment === comment }]"
              @click="modalForm.comment = comment"
            >
              {{ comment }}
            </button>
          </div>
        </div>

        <!-- <div class="form-group">
            <label>Contre-indications d'utilisation</label>
            <el-select
                v-model="modalForm.contraindications"
                placeholder="Rechercher ou ajouter une contre-indication"
                filterable
                allow-create
                @create="addContraindication"
                multiple
            >
                <el-option
                v-for="contraindication in contraindicationOptions"
                :key="contraindication"
                :label="contraindication"
                :value="contraindication"
                />
            </el-select>
            </div> -->

      </div>
    </div>

    <!-- Dialog Footer -->
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showModal = false">Annuler</el-button>
        <el-button type="primary" @click="handleSaveModalData">Enregistrer</el-button>
      </div>
    </template>
  </el-dialog>
</template>


<style scoped>
.btn-gear , .btn-add{
    height: 31px;
    margin: auto;
  }
  .btn-add {
    background-color:  #28a745!important;
  }
.custom-dialog :deep(.el-dialog) {
  border-radius: 12px;
  max-width: 95vw;
}

.modal-body {
  padding: 20px 0;
}

/* Dose Grid */
.dose-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.dose-card {
  background: #fff;
  border: 1px solid #e4e7ec;
  border-radius: 12px;
  padding: 16px;
}

.dose-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.dose-icon {
  font-size: 24px;
  margin-bottom: 4px;
}

.dose-label {
  color: #111827;
  font-weight: 500;
  font-size: 14px;
}

.dose-controls {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 8px 0;
}

.control-btn {
  width: 32px;
  height: 32px;
  border: 1px solid #e4e7ec;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.control-btn:hover {
  background: #f9fafb;
}

.minus-icon, .plus-icon {
  font-size: 18px;
  color: #4b5563;
}

.dose-value {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  min-width: 24px;
  text-align: center;
}

.dose-units {
  color: #6b7280;
  font-size: 12px;
}

/* Form Styling */
.form-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding: 0 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.button-grid {
  display: flex;
  flex-wrap: wrap;
  /* grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); */
  gap: 8px;
  
}

.option-button {
    
  background: #fff;
  border: 1px solid #e4e7ec;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 14px;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
  white-space: nowrap;
}

.option-button:hover {
  background: #f9fafb;
}

.option-button.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.duration-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.duration-input {
  width: 100%;
}

.duration-buttons {
  margin-top: 8px;
}

.unit-select, .admin-input {
  width: 100%;
}

/* Footer */
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 16px;
}

:deep(.el-input-number) {
  width: 100%;
}

:deep(.el-input__wrapper),
:deep(.el-select) {
  width: 100%;
}

/* Responsive */
@media (max-width: 640px) {
  .dose-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .button-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
.table-container{
    overflow: auto;
}
</style>