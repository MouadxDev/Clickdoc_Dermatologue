<script setup lang="ts">
import { Ref, onBeforeMount, ref } from "vue";
import { useConsultStore } from "../../../core/Data/stores/consultation"
import { Ordonnance } from '../../../core/Clients/Ordonnance';
import { Medicament } from '../../../core/Clients/Medicament';
import ENV from '../../../core/env'
import { computed } from "@vue/reactivity";
import { LaboMedicament } from "../../../core/Clients/LaboMed";
import { ElMessage } from "element-plus";
import { watch } from 'vue';
import PrintModal from "../PrintModal.vue";

const consult = useConsultStore();
const ordonnanceClient = new Ordonnance()
const medicamentClient = new Medicament()
const laboMedicamentClient = new LaboMedicament();
const medicaments: Ref<any> = ref([])
const laboratoire_list: Ref<any> = ref([])

const showContent = ref(false);
const inputMedicament = ref("");

// Print Modal Logic
const printModalRef = ref();
const modalTitle = ref("");
const modalUrl = ref("");


const tempMedicamentName = ref('');

function openPrintModal() {
    modalTitle.value = "Aperçu à imprimer";
    modalUrl.value = `${ENV.VITE_BACKEND}/ordonnance/${consult.consult}`;
    printModalRef.value.openModal();
}


const prescription: Ref<any> = ref({
  consultation_id: consult.consult,
  commentaire: "",
  medicament_id: "",
  dose_id: "",
  context: "", // Add treatment context
});

async function getOrdonnance() {
  return await ordonnanceClient.getByID(consult.consult)
}

const handleShowContent = () => {
  showContent.value = !showContent.value;
};

const ordonnance: Ref<any> = ref([])
const doses: Ref<any> = ref([])

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

  // Prepare data to send
  const ordonnanceData = {
    consultation_id: prescription.value.consultation_id,
    medicament_id: prescription.value.medicament_id,
    commentaire: modalForm.value.comment || "",
    administration_mode: modalForm.value.administrationMode,
    duration_value: modalForm.value.durationValue || null,
    duration_unit: modalForm.value.durationUnit || null,
    frequency: modalForm.value.frequency,
    contraindications: modalForm.value.contraindications || [],
    matin: doseValues.value.Matin || 0,
    midi: doseValues.value.Midi || 0,
    soir: doseValues.value.Soir || 0,
    au_coucher: doseValues.value["Au coucher"] || 0,
    treatment_context: modalForm.value.treatmentContext || "",
    application_site: modalForm.value.applicationSite || "",
    // special_instructions: modalForm.value.specialInstructions || "",
  };

  // Save data using API
  try {
    await ordonnanceClient.add(ordonnanceData);

    // Refresh ordonnance list
    ordonnance.value = await getOrdonnance();

    // Reset fields
    prescription.value.medicament_id = "";
    prescription.value.commentaire = "";

    Object.assign(modalForm.value, {
      unit: "",
      administrationMode: "",
      frequency: [],
      durationValue: null,
      durationUnit: "",
      comment: "",
      contraindications: [],
      treatmentContext: "",
      applicationSite: "",
      specialInstructions: "",
    });

    doses.value = [];
    // ElMessage.success("Médicament ajouté à l'ordonnance avec succès.");
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

async function removeOrdonnance(x: number) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?') == true) {
    await ordonnanceClient.delete(x)
  }
  ordonnance.value = await getOrdonnance()
}

onBeforeMount(async () => {
  medicaments.value = await medicamentClient.getAll();
  laboratoire_list.value = await laboMedicamentClient.getAll();
  ordonnance.value = await getOrdonnance()
})

// Modal visibility state
const showModal = ref(false);
const isEditMode = ref(false);
const currentEditId = ref<number | null>(null);

const modalForm = ref({
  unit: "",
  administrationMode: "",
  frequency: [],
  durationValue: null,
  durationUnit: "",
  comment: "",
  contraindications: [],
  treatmentContext: "", 
  applicationSite: "",
  specialInstructions: "",
});

// Reset modal form to default values
function resetModalForm() {
  modalForm.value = {
    unit: "",
    administrationMode: "",
    frequency: [],
    durationValue: null,
    durationUnit: "",
    comment: "",
    contraindications: [],
    treatmentContext: "",
    applicationSite: "",
    specialInstructions: "",
  };
  
  doseValues.value = {
    Matin: 0,
    Midi: 0,
    Soir: 0,
    "Au coucher": 0,
  };
}

function handleShowModal() {
  if (prescription.value.medicament_id) {
    isEditMode.value = false;
    currentEditId.value = null;
    resetModalForm();
    showModal.value = true;
  } else {
    ElMessage.error('Veuillez sélectionner un médicament.');
  }
}

// Helper function to clean frequency data
function cleanFrequencyData(frequency: any): string[] {
  if (!frequency) return [];
  
  try {
    // If it's already an array, return it
    if (Array.isArray(frequency)) {
      return frequency.map(f => {
        // If the item is a stringified array, parse it
        if (typeof f === 'string' && f.startsWith('[')) {
          try {
            const parsed = JSON.parse(f);
            return Array.isArray(parsed) ? parsed[0] : parsed;
          } catch {
            return f;
          }
        }
        return f;
      }).flat();
    }
    
    // If it's a string, try to parse it
    if (typeof frequency === 'string') {
      try {
        const parsed = JSON.parse(frequency);
        return Array.isArray(parsed) ? parsed : [parsed];
      } catch {
        return [frequency];
      }
    }
    
    return [frequency];
  } catch {
    return [];
  }
}

function handleEditOrdonnance(row: any) {
  // Set edit mode
  isEditMode.value = true;
  currentEditId.value = row.id;
  
  // Set the prescription medicament_id
  prescription.value.medicament_id = row.medicament_id;
  
  // Clean and set frequency data
  const cleanedFrequency = cleanFrequencyData(row.frequency);
  
  // Populate the modal form with existing values
  modalForm.value = {
    unit: row.unit || "",
    administrationMode: row.administration_mode || "",
    frequency: cleanedFrequency,
    durationValue: row.duration_value || null,
    durationUnit: row.duration_unit || "",
    comment: row.commentaire || "",
    contraindications: row.contraindications || [],
    treatmentContext: row.treatment_context || "",
    applicationSite: row.application_site || "",
    specialInstructions: row.special_instructions || "",
  };

  // Set the dose values
  doseValues.value = {
    Matin: row.matin || 0,
    Midi: row.midi || 0,
    Soir: row.soir || 0,
    "Au coucher": row.au_coucher || 0,
  };

  // Show the modal
  showModal.value = true;
}

async function handleSaveModalData() {
  // Prepare data to send
  const ordonnanceData = {
    consultation_id: prescription.value.consultation_id,
    medicament_id: prescription.value.medicament_id,
    commentaire: modalForm.value.comment || "",
    administration_mode: modalForm.value.administrationMode,
    duration_value: modalForm.value.durationValue || null,
    duration_unit: modalForm.value.durationUnit || null,
    frequency: modalForm.value.frequency,
    contraindications: modalForm.value.contraindications || [],
    matin: doseValues.value.Matin || 0,
    midi: doseValues.value.Midi || 0,
    soir: doseValues.value.Soir || 0,
    au_coucher: doseValues.value["Au coucher"] || 0,
    treatment_context: modalForm.value.treatmentContext || "",
    application_site: modalForm.value.applicationSite || "",
  };

  try {
    if (isEditMode.value && currentEditId.value) {
      // Update existing ordonnance
      await ordonnanceClient.update(currentEditId.value, ordonnanceData);
      ElMessage.success("Ordonnance mise à jour avec succès.");
    } else {
      // Create new ordonnance
      await ordonnanceClient.add(ordonnanceData);
      ElMessage.success("Médicament ajouté à l'ordonnance avec succès.");
    }

    // Refresh ordonnance list
    ordonnance.value = await getOrdonnance();

    // Reset fields and close modal
    prescription.value.medicament_id = "";
    resetModalForm();
    showModal.value = false;
    isEditMode.value = false;
    currentEditId.value = null;

  } catch (error) {
    ElMessage.error("Une erreur s'est produite lors de l'enregistrement des données.");
    console.error(error);
  }
}

const timingOptions: { label: "Matin" | "Midi" | "Soir" | "Au coucher"; icon: string }[] = [
  { label: "Matin", icon: "☀️" },
  { label: "Midi", icon: "🍴" },
  { label: "Soir", icon: "🌙" },
  { label: "Au coucher", icon: "🛌" },
];

// Enhanced for dermatology
const frequencyOptions = [
  "Une fois par jour",
  "Deux fois par jour",
  "Trois fois par jour",
  "Chaque deux heures",
  "Matin et soir",
  "Au besoin",
  "Par jour",
  "Par semaine",
  "Uniquement lors des poussées",
  "En cas de démangeaison",
  "Avant exposition au soleil",
  "Après exposition au soleil",
  "Après chaque lavage",
];

const durationUnits = [
  "Jour(s)",
  "Semaine(s)",
  "Mois",
  "Année(s)",
  "Jusqu'à amélioration",
  "Jusqu'à disparition des lésions",
  "Pendant toute la durée du traitement",
  "À vie"
];

// Enhanced for dermatology
const unitOptions = [
  "g",
  "mg",
  "ml",
  "application(s)",
  "pression(s)",
  "noisette(s)",
  "FPS",
  "doigt(s)",
  "unité(s)",
  "tablette(s)",
  "capsule(s)",
  "sachet(s)",
  "goutte(s)",
  "pulvérisation(s)"
];

// Enhanced for dermatology
const commentOptions = [
  "À appliquer sur peau propre et sèche",
  "Après la douche",
  "Appliquer avant le coucher",
  "Masser légèrement jusqu'à absorption",
  "Laisser sécher naturellement, ne pas rincer",
  "Éviter le contour des yeux",
  "Ne pas exposer au soleil après application",
  "À utiliser uniquement sur les zones lésées",
  "Ne pas appliquer sur peau lésée",
  "Stopper en cas d'irritation",
  "Ne pas appliquer sur le visage",
  "Éviter tout contact avec les muqueuses",
  "Appliquer en fine couche",
  "Appliquer en couche épaisse",
];

// Enhanced for dermatology
const administrationModes = [
  "Topique",
  "Visage",
  "Visage uniquement",
  "Corps",
  "Corps entier",
  "lésion",
  "Zones inflammées",
  "Lésions",
  "Cuir chevelu",
  "Plis cutanés",
  "Mains",
  "Pieds",
  "Ongles",
  "Zones exposées au soleil",
  "Application occlusse",
  "Sous pansement",
  "Orale",
  "Sublinguale"
];

// Treatment context options
const treatmentContextOptions = [
  "Traitement d'attaque",
  "Traitement d'entretien",
  "Traitement préventif",
  "Traitement symptomatique",
  "Traitement curatif"
];

// Application site specific to dermatology
const applicationSiteOptions = [
  "Zones infectées uniquement",
  "Zones érythémateuses",
  "Plaques de psoriasis",
  "Lésions d'acné",
  "Plis axillaires",
  "Plis inguinaux",
  "Espaces interdigitaux",
  "Cuir chevelu",
  "Visage entier",
  "Zone T du visage",
  "Contour des yeux (éviter)",
  "Zones sèches uniquement"
];

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

function toggleFrequency(option: string) {
  const index = modalForm.value.frequency.indexOf(option);
  if (index > -1) {
    modalForm.value.frequency.splice(index, 1);
  } else {
    modalForm.value.frequency.push(option); 
  }
}

const contraindicationOptions = ref([
  "Allergie au médicament",
  "Insuffisance rénale",
  "Insuffisance hépatique",
  "Grossesse",
  "Allaitement",
  "Interaction avec d'autres médicaments",
  "Infection bactérienne non traitée",
  "Rosacée",
  "Dermatite périorale",
  "Acné",
  "Peau lésée",
]);

function addContraindication(newContraindication: string) {
  if (
    newContraindication &&
    !contraindicationOptions.value.includes(newContraindication)
  ) {
    contraindicationOptions.value.push(newContraindication);
  }
}

const loadingMedicament = ref(false);

async function loadMedicaments(query: string) {
  if (!query) return;
  loadingMedicament.value = true;
  try {
    const response = await medicamentClient.getAll({ q: query });
    medicaments.value = response; // adapt this if the data is nested
  } catch (e) {
    ElMessage.error("Erreur lors du chargement des médicaments");
  } finally {
    loadingMedicament.value = false;
  }
}

function onSelect(value: number) {
  const selected = medicaments.value.find(m => m.id === value);
  if (selected) {
    tempMedicamentName.value = selected.nom;  // Store label text on select
  }
}

function onInput(val: string) {
  tempMedicamentName.value = val;             // Update temp text while typing
}

function onBlur() {
  // On input lose focus, check if temp text matches existing medicament
  const found = medicaments.value.find(m => m.nom === tempMedicamentName.value);

  if (found) {
    prescription.value.medicament_id = found.id;  // Use matched item
  } else {
    // User typed a new/edited medicament name
    prescription.value.medicament_id = null;      // Clear id or handle as needed
    console.log('New or edited medicament:', tempMedicamentName.value);
  }
}

</script>

<template>
  <div class="container">
    <el-form label-position="top">
      <el-row :gutter="10" class="medicament-row">
        <el-col :span="19" class="input-col">
          <el-form-item label="Médicament" class="medicament-form-item">
            <el-select
                class="w-full"
                v-model="prescription.medicament_id"
                placeholder="Rechercher un médicament"
                filterable
                remote
                reserve-keyword
                :remote-method="loadMedicaments"
                :loading="loadingMedicament"
                @change="onSelect"     
                @input="onInput"      
                @blur="onBlur"  
              >
                <el-option
                  v-for="m in medicaments"
                  :key="m.id"
                  :value="m.id"
                  :label="m.nom"
                />
</el-select>


          </el-form-item>
        </el-col>
        <el-col :span="4" class="button-col">
          <div class="button-container">
            <el-button type="primary" size="small" @click="handleShowModal" class="btn-gear">
              <img src="https://clickdoc.webredirect.org/public/Svg/settings.svg" alt="Settings Icon" />
            </el-button>

            <el-button type="primary" size="small" @click="handleShowContent" class="btn-add">
              <img src="https://clickdoc.webredirect.org/public/Svg/plus.svg" alt="Settings Icon" />
            </el-button>

            <el-button @click="async ()=>{await setOrdonnance()}" class="btn btn-sm btn-block background-clickdoc validate-btn" type="button">
              <el-icon>
                <Select />
              </el-icon>
            </el-button>
          </div>
          
        </el-col>

        <el-col :span="24" v-if="showContent" style="margin-top: 10px;">
          <el-form-item>
            <el-input v-model="inputMedicament" placeholder="Ajoutez un Medicament">
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
    <el-table-column label="Médicament" width="220">
      <template #default="scope">
        {{ scope.row.medicament }}
      </template>
    </el-table-column>
    <!-- Hidden columns for data access -->
    <el-table-column v-if="false" label="Mode d'administration" width="220">
      <template #default="scope">
        {{ scope.row.administration_mode }}
      </template>
    </el-table-column>
    <el-table-column v-if="false" label="Site d'application" width="220">
      <template #default="scope">
        {{ scope.row.application_site || 'Non spécifié' }}
      </template>
    </el-table-column>
    <el-table-column v-if="false" label="Contexte" width="150">
      <template #default="scope">
        {{ scope.row.treatment_context || 'Standard' }}
      </template>
    </el-table-column>
    <el-table-column label="Fréquence" width="200">
      <template #default="scope">
        <div class="frequency-tags">
          <template v-if="cleanFrequencyData(scope.row.frequency).length > 0">
            <el-tag 
              v-for="(freq, index) in cleanFrequencyData(scope.row.frequency)" 
              :key="index" 
              type="info" 
              class="tag-space"
            >
              {{ freq }}
            </el-tag>
          </template>
          <template v-else>
            <el-tag type="info" class="tag-space">
              Non spécifié
            </el-tag>
          </template>
        </div>
      </template>
    </el-table-column>
    <el-table-column label="Durée" width="150">
      <template #default="scope">
        {{ scope.row.duration_value }} {{ scope.row.duration_unit }}
      </template>
    </el-table-column>

    <!-- Commentaire Section as Tags -->
    <el-table-column label="Commentaire" width="260">
      <template #default="scope">
        <div class="comment-content">
          <el-tag v-if="scope.row.commentaire" type="info" class="tag-space">
            {{ scope.row.commentaire }}
          </el-tag>
          <el-tag v-else type="info" class="tag-space">
            Aucun commentaire
          </el-tag>
        </div>
      </template>
    </el-table-column>

    <!-- Timing Section as Tags -->
    <el-table-column label="Timing" width="220">
      <template #default="scope">
        <el-tag v-if="scope.row.matin > 0" type="success" class="tag-space">Matin: {{ scope.row.matin }}</el-tag>
        <el-tag v-if="scope.row.midi > 0" type="warning" class="tag-space">Midi: {{ scope.row.midi }}</el-tag>
        <el-tag v-if="scope.row.soir > 0" type="primary" class="tag-space">Soir: {{ scope.row.soir }}</el-tag>
        <el-tag v-if="scope.row.au_coucher > 0" type="danger" class="tag-space">Au coucher: {{ scope.row.au_coucher }}</el-tag>
        <el-tag v-if="scope.row.matin + scope.row.midi + scope.row.soir + scope.row.au_coucher === 0" type="info">
          Non spécifié
        </el-tag>
      </template>
    </el-table-column>

    <el-table-column width="120px" label="Actions">
  <template #default="scope">
    <div class="action-buttons">
      <!-- Edit Button -->
      <el-button 
        size="small" 
        type="warning" 
        @click="handleEditOrdonnance(scope.row)"
        class="action-btn"
      >
        <el-icon><Edit /></el-icon>
      </el-button>
      
      <!-- Delete Button -->
      <el-button 
        size="small" 
        type="danger" 
        @click="async () => { await removeOrdonnance(scope.row.id) }"
        class="action-btn"
      >
        <el-icon><Delete /></el-icon>
      </el-button>
    </div>
  </template>
</el-table-column>
  </el-table>
</div>


    <div class="text-right mt-3">
      <!-- <a class="btn btn-sm btn-link" target="_blank" :href="ENV.VITE_BACKEND+'/ordonnance/'+consult.consult">
        <el-icon>
          <Printer />
        </el-icon> Imprimer
      </a> -->
        <el-button class="btn btn-sm btn-link text-right right-0" @click="openPrintModal" style="float: right;text-decoration: none; ">
            <el-icon style="margin-right: 5px;"><Printer /></el-icon> Imprimer
        </el-button>
    </div>

    <PrintModal 
        ref="printModalRef" 
        :title="modalTitle" 
        :url="modalUrl" 
        @close="() => {}" 
    />
  </div>
  <!-- Modal -->
  <el-dialog title="Configurer les détails du médicament" v-model="showModal" width="700px" :close-on-click-modal="false"
    class="custom-dialog">
    <div class="modal-body">
      <!-- Dosage Timing -->
      <div class="dose-grid">
        <div v-for="time in timingOptions" :key="time.label" class="dose-card">
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
        <!-- Commentaire -->
        <div class="form-group">
          <label>Commentaire</label>
          <el-input
            v-model="modalForm.comment"
            type="textarea"
            :rows="3"
            placeholder="Ajouter un commentaire..."
            class="comment-textarea"
          />
        </div>

        <!-- Treatment Context -->
        <div class="form-group">
          <label>Contexte du traitement</label>
          <el-select v-model="modalForm.treatmentContext" placeholder="Contexte du traitement" class="context-select"
            filterable allow-create>
            <el-option v-for="context in treatmentContextOptions" :key="context" :label="context" :value="context" />
          </el-select>
        </div>

        <!-- Administration Mode & Site -->
        <div class="form-group">
          <label>Par voie</label>
          <el-select v-model="modalForm.administrationMode" placeholder="Filtrer par voie" class="admin-input"
            filterable allow-create >
            <el-option v-for="mode in administrationModes" :key="mode" :label="mode" :value="mode" />
          </el-select>
        </div>

        <div class="form-group">
          <label>Site d'application spécifique</label>
          <el-select v-model="modalForm.applicationSite" placeholder="Site d'application" class="site-select"
            filterable allow-create>
            <el-option v-for="site in applicationSiteOptions" :key="site" :label="site" :value="site" />
          </el-select>
        </div>

        <!-- Unit -->
        <div class="form-group">
          <label>Unité</label>
          <el-select v-model="modalForm.unit" placeholder="Sélectionnez une unité" class="unit-select" filterable allow-create>
            <el-option v-for="unit in unitOptions" :key="unit" :label="unit" :value="unit" />
          </el-select>
        </div>

        <!-- Frequency -->
        <div class="form-group">
          <label>Fréquence</label>
          <div class="button-grid">
            <button
              v-for="option in frequencyOptions"
              :key="option"
              :class="['option-button', { active: modalForm.frequency.includes(option) }]"
              @click="toggleFrequency(option)"
            >
              {{ option }}
            </button>
          </div>
        </div>

        <!-- Duration -->
        <div class="form-group">
          <label>Durée</label>
          <div class="duration-container">
            <el-input-number v-model="modalForm.durationValue" :min="0" controls-position="right"
              class="duration-input" />
            <div class="button-grid duration-buttons">
              <button v-for="unit in durationUnits" :key="unit"
                :class="['option-button', { active: modalForm.durationUnit === unit }]"
                @click="modalForm.durationUnit = unit">
                {{ unit }}
              </button>
            </div>
          </div>
        </div>

        <!-- Comments -->
        <div class="form-group">
          <label>Instructions spéciales</label>
          <el-select v-model="modalForm.comment" placeholder="Sélectionner instructions" filterable multiple
            allow-create :clearable="true" class="w-full">
            <el-option v-for="comment in commentOptions" :key="comment" :label="comment" :value="comment" />
          </el-select>
        </div>

        <!-- <div class="form-group">
          <label>Contre-indications d'utilisation</label>
          <el-select v-model="modalForm.contraindications" placeholder="Rechercher ou ajouter une contre-indication"
            filterable allow-create @create="addContraindication" multiple>
            <el-option v-for="contraindication in contraindicationOptions" :key="contraindication"
              :label="contraindication" :value="contraindication" />
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
.medicament-row {
  display: flex;
  align-items: end;
  width: 100%;
  gap: 3%;
}

.input-col {
  display: flex;
  align-items: center;
}

.medicament-form-item {
  width: 100%;
  margin-bottom: 0;
}

.button-col {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.button-container {
  display: flex;
  align-items: center;
  gap: 10px;
  height: 100%;
}

.btn-gear, .btn-add, .validate-btn {
  height: 35px !important;
  min-height: 35px;
  width: 35px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.btn-gear img, .btn-add img {
  filter: brightness(0) saturate(100%) invert(100%) sepia(2%) saturate(148%) hue-rotate(35deg) brightness(113%) contrast(98%);
  width: 16px;
  height: 16px;
}

.btn-add {
  background-color: #28a745!important;
}

.validate-btn {
  background-color: var(--el-color-primary) !important;
  color: white;
}

.validate-btn .el-icon {
  font-size: 16px;
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

.tag-space {
  margin: 2px;
}

.frequency-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.comment-textarea {
  width: 100%;
}

.comment-textarea :deep(.el-textarea__inner) {
  min-height: 80px;
  resize: vertical;
}

.comment-content {
  white-space: pre-wrap;
  word-break: break-word;
}

.button-form-item {
  margin-bottom: 0;
  display: flex;
  align-items: center;
}

.btn-gear, .btn-add, .validate-btn {
  height: 35px !important;
  min-height: 35px;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-gear img, .btn-add img {
  filter: brightness(0) saturate(100%) invert(100%) sepia(2%) saturate(148%) hue-rotate(35deg) brightness(113%) contrast(98%);
  width: 16px;
  height: 16px;
}

.btn-add {
  background-color: #28a745!important;
}

.validate-btn {
  background-color: var(--el-color-primary) !important;
  color: white;
}

.validate-btn .el-icon {
  font-size: 16px;
}

</style>