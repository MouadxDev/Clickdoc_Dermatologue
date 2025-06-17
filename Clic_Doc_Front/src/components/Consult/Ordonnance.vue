<script setup lang="ts">
import { Ref, onBeforeMount, ref, watch } from "vue";
import { useConsultStore } from "../../../core/Data/stores/consultation";
import { Ordonnance } from "../../../core/Clients/Ordonnance";
import { Medicament } from "../../../core/Clients/Medicament";
import { LaboMedicament } from "../../../core/Clients/LaboMed";
import ENV from "../../../core/env";
import { ElMessage } from "element-plus";
import PrintModal from "../PrintModal.vue";
import { ArrowDown, Select, Edit, Delete, Printer } from '@element-plus/icons-vue'; // Import icons directly

// --- Stores and API Clients ---
const consultStore = useConsultStore();
const ordonnanceClient = new Ordonnance();
const medicamentClient = new Medicament();
const laboMedicamentClient = new LaboMedicament();

// --- Reactive State ---
const medicaments: Ref<any[]> = ref([]);
const laboratoireList: Ref<any[]> = ref([]);
const ordonnanceList: Ref<any[]> = ref([]); // Renamed for clarity
const showAddMedicamentInput = ref(false); // Renamed for clarity
const newMedicamentName = ref(""); // Renamed for clarity
const showMoreFilters = ref(false);

const prescriptionForm = ref({
  consultation_id: consultStore.consult,
  medicament_id: "",
  commentaire: "",
  dose_id: "", // Consider if this is still needed or can be removed
  context: "", // Add treatment context - consider renaming to treatmentContext for consistency
});

const tempMedicamentName = ref(""); // For autocomplete input

const doseValues = ref<Record<"Matin" | "Midi" | "Soir" | "Au coucher", number>>({
  Matin: 0,
  Midi: 0,
  Soir: 0,
  "Au coucher": 0,
});

// --- Print Modal Logic ---
const printModalRef = ref<InstanceType<typeof PrintModal> | null>(null);
const modalTitle = ref("Aperçu à imprimer");
const modalUrl = ref("");

function openPrintPreviewModal() {
  modalUrl.value = `${ENV.VITE_BACKEND}/ordonnance/${consultStore.consult}`;
  printModalRef.value?.openModal();
}

// --- Modal for Medicament Details (Add/Edit) ---
const showMedicamentDetailModal = ref(false); // Renamed for clarity
const isEditMode = ref(false);
const currentEditOrdonnanceId = ref<number | null>(null); // Renamed for clarity

const medicamentDetailForm = ref({
  unit: "",
  administrationMode: "",
  frequency: [] as string[], // Explicitly type as string array
  durationValue: null as number | null,
  durationUnit: "",
  comment: "",
  contraindications: [] as string[],
  treatmentContext: "",
  applicationSite: "",
  specialInstructions: "", // Retained as per your original code, though you had it commented out in setOrdonnance
});

// --- Data Fetching ---
async function fetchOrdonnance() {
  try {
    ordonnanceList.value = await ordonnanceClient.getByID(consultStore.consult);
  } catch (error) {
    ElMessage.error("Erreur lors du chargement des ordonnances.");
    console.error("Failed to fetch ordonnances:", error);
  }
}

async function fetchMedicaments(query?: string) {
  loadingMedicament.value = true;
  try {
    medicaments.value = await medicamentClient.getAll({ q: query });
  } catch (error) {
    ElMessage.error("Erreur lors du chargement des médicaments.");
    console.error("Failed to fetch medicaments:", error);
  } finally {
    loadingMedicament.value = false;
  }
}

async function fetchLaboratoires() {
  try {
    laboratoireList.value = await laboMedicamentClient.getAll();
  } catch (error) {
    ElMessage.error("Erreur lors du chargement des laboratoires.");
    console.error("Failed to fetch laboratoires:", error);
  }
}

// --- Component Lifecycle ---
onBeforeMount(async () => {
  await Promise.all([fetchMedicaments(), fetchLaboratoires(), fetchOrdonnance()]);
});

// --- Watchers ---
watch(
  () => prescriptionForm.value.medicament_id,
  (newMedicamentId) => {
    // Logic if needed when medicament_id changes, e.g., fetching default doses
    // For now, it seems your original watch was for 'laboratoire', which is not in prescriptionForm.
    // If you intended to clear other fields when medicament changes, add it here.
  }
);

// --- Handlers for Main Section ---
const toggleAddMedicamentInput = () => {
  showAddMedicamentInput.value = !showAddMedicamentInput.value;
};

async function addNewMedicament() {
  const medicamentName = newMedicamentName.value.trim();

  if (!medicamentName) {
    ElMessage.error("Veuillez entrer un nom de médicament.");
    return;
  }

  const dataToSend = {
    nom: medicamentName,
    lab_id: 0, // Assuming default or not required on add
    prix: 0, // Assuming default or not required on add
  };

  try {
    await medicamentClient.add(dataToSend);
    newMedicamentName.value = "";
    ElMessage.success("Médicament ajouté avec succès.");
    await fetchMedicaments(); // Refresh the list
  } catch (error) {
    ElMessage.error("Une erreur s'est produite lors de l'ajout du médicament.");
    console.error("Failed to add medicament:", error);
  }
}

async function removeOrdonnance(id: number) {
  // if (confirm("Êtes-vous sûr de vouloir supprimer cet élément ?")) {
    try {
      await ordonnanceClient.delete(id);
      ElMessage.success("Ordonnance supprimée avec succès.");
      await fetchOrdonnance(); // Refresh the list
    } catch (error) {
      ElMessage.error("Une erreur s'est produite lors de la suppression.");
      console.error("Failed to delete ordonnance:", error);
    }
  }
// }  

// --- Modal Logic (Add/Edit Ordonnance) ---

// Reset modal form to default values
function resetMedicamentDetailForm() {
  medicamentDetailForm.value = {
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
  showMoreFilters.value = false; // Reset filter visibility
}

function openMedicamentDetailModal() {
  // Always allow opening the modal, we'll handle creation in saveOrdonnanceDetails
  isEditMode.value = false;
  currentEditOrdonnanceId.value = null;
  resetMedicamentDetailForm();
  showMedicamentDetailModal.value = true;
}

// Helper function to clean frequency data
function cleanFrequencyData(frequency: any): string[] {
  if (!frequency) return [];

  try {
    if (Array.isArray(frequency)) {
      return frequency
        .map((f) => {
          if (typeof f === "string" && f.startsWith("[")) {
            try {
              const parsed = JSON.parse(f);
              return Array.isArray(parsed) ? parsed[0] : parsed;
            } catch {
              return f;
            }
          }
          return f;
        })
        .flat();
    }

    if (typeof frequency === "string") {
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
  isEditMode.value = true;
  currentEditOrdonnanceId.value = row.id;

  prescriptionForm.value.medicament_id = row.medicament_id;
  tempMedicamentName.value = row.medicament; // Set the name for autocomplete

  const cleanedFrequency = cleanFrequencyData(row.frequency);

  medicamentDetailForm.value = {
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

  doseValues.value = {
    Matin: row.matin || 0,
    Midi: row.midi || 0,
    Soir: row.soir || 0,
    "Au coucher": row.au_coucher || 0,
  };

  showMedicamentDetailModal.value = true;
}

async function saveOrdonnanceDetails() {
  let medicamentId = prescriptionForm.value.medicament_id;
  
  // If no medicament_id but we have a name, create new medication first
  if (!medicamentId && tempMedicamentName.value.trim()) {
    const newMedicament = await handleNewMedicament(tempMedicamentName.value.trim());
    if (newMedicament && newMedicament.id) {
      medicamentId = newMedicament.id;
      prescriptionForm.value.medicament_id = medicamentId;
    } else {
      // Failed to create new medication
      ElMessage.error("Impossible de créer le nouveau médicament.");
      return;
    }
  }
  
  // Final check - we must have a medicament_id at this point
  if (!medicamentId) {
    ElMessage.error("Veuillez sélectionner ou saisir un médicament.");
    return;
  }

  const payload = {
    consultation_id: consultStore.consult,
    medicament_id: medicamentId,
    commentaire: medicamentDetailForm.value.comment || "",
    administration_mode: medicamentDetailForm.value.administrationMode,
    duration_value: medicamentDetailForm.value.durationValue || null,
    duration_unit: medicamentDetailForm.value.durationUnit || null,
    frequency: medicamentDetailForm.value.frequency,
    contraindications: medicamentDetailForm.value.contraindications || [],
    matin: doseValues.value.Matin || 0,
    midi: doseValues.value.Midi || 0,
    soir: doseValues.value.Soir || 0,
    au_coucher: doseValues.value["Au coucher"] || 0,
    treatment_context: medicamentDetailForm.value.treatmentContext || "",
    application_site: medicamentDetailForm.value.applicationSite || "",
  };

  try {
    if (isEditMode.value && currentEditOrdonnanceId.value) {
      await ordonnanceClient.update(currentEditOrdonnanceId.value, payload);
      ElMessage.success("Ordonnance mise à jour avec succès.");
    } else {
      await ordonnanceClient.add(payload);
      ElMessage.success("Médicament ajouté à l'ordonnance avec succès.");
    }

    await fetchOrdonnance(); // Refresh the list
    prescriptionForm.value.medicament_id = null; // Reset for next entry
    tempMedicamentName.value = ""; // Clear autocomplete input
    resetMedicamentDetailForm();
    showMedicamentDetailModal.value = false;
    isEditMode.value = false;
    currentEditOrdonnanceId.value = null;
  } catch (error) {
    ElMessage.error("Une erreur s'est produite lors de l'enregistrement des données.");
    console.error("Failed to save ordonnance details:", error);
  }
}

function incrementDose(label: "Matin" | "Midi" | "Soir" | "Au coucher") {
  doseValues.value[label]++;
}

function decrementDose(label: "Matin" | "Midi" | "Soir" | "Au coucher") {
  if (doseValues.value[label] > 0) {
    doseValues.value[label]--;
  }
}

function toggleFrequency(option: string) {
  const index = medicamentDetailForm.value.frequency.indexOf(option);
  if (index > -1) {
    medicamentDetailForm.value.frequency.splice(index, 1);
  } else {
    medicamentDetailForm.value.frequency.push(option);
  }
}

function addContraindication(newContraindication: string) {
  const trimmedContra = newContraindication.trim();
  if (trimmedContra && !contraindicationOptions.value.includes(trimmedContra)) {
    contraindicationOptions.value.push(trimmedContra);
  }
}

// --- Autocomplete Logic ---
const loadingMedicament = ref(false);

function fetchMedicamentSuggestions(queryString: string, cb: (results: any[]) => void) {
  if (!queryString) {
    cb([]);
    return;
  }
  medicamentClient
    .getAll({ q: queryString })
    .then((res) => {
      // It's good practice to update the main medicaments list if fetched
      medicaments.value = res;
      cb(
        res.map((m: any) => ({
          value: m.nom, // Display in autocomplete
          id: m.id,
        }))
      );
    })
    .catch((err) => {
      console.error("Error fetching medicament suggestions:", err);
      cb([]);
    });
}

function handleMedicamentSelect(item: { value: string; id: number }) {
  tempMedicamentName.value = item.value;
  prescriptionForm.value.medicament_id = item.id;
}

function onMedicamentInput(val: string) {
  tempMedicamentName.value = val;
  // Reset medicament_id when user starts typing (it's a new/different value)
  prescriptionForm.value.medicament_id = null;
}

// Add this new function to handle new medication creation
async function handleNewMedicament(medicamentName: string) {
  if (!medicamentName.trim()) {
    ElMessage.error("Le nom du médicament ne peut pas être vide.");
    return null;
  }

  const dataToSend = {
    nom: medicamentName.trim(),
    lab_id: 0, // Default value
    prix: 0, // Default value
  };

  try {
    const response = await medicamentClient.add(dataToSend);
    // PHP returns the medicament object with the new ID
    if (response && response.id) {
      ElMessage.success("Nouveau médicament créé avec succès.");
      await fetchMedicaments(); // Refresh the medicaments list
      return response; // Return the full medicament object with ID
    }
    return null;
  } catch (error) {
    ElMessage.error("Erreur lors de la création du nouveau médicament.");
    console.error("Failed to create new medicament:", error);
    return null;
  }
}


// Modify the onMedicamentBlur function to handle new medications
async function onMedicamentBlur() {
  const found = medicaments.value.find((m) => m.nom === tempMedicamentName.value?.trim());
  if (found) {
    // Existing medication found
    prescriptionForm.value.medicament_id = found.id;
  } else {
    // New medication or no input - keep ID as null
    prescriptionForm.value.medicament_id = null;
  }
}

// --- Constants (Options for Modals) ---
const timingOptions = [
  { label: "Matin", icon: "☀️" },
  { label: "Midi", icon: "🍴" },
  { label: "Soir", icon: "🌙" },
  { label: "Au coucher", icon: "🛌" },
];

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
  "À vie",
];

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
  "pulvérisation(s)",
];

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
  "Sublinguale",
];

const treatmentContextOptions = [
  "Traitement d'attaque",
  "Traitement d'entretien",
  "Traitement préventif",
  "Traitement symptomatique",
  "Traitement curatif",
];

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
  "Zones sèches uniquement",
];

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
</script>

<template>
  <div class="container">
    <el-form label-position="top">
      <el-row :gutter="10" class="medicament-row">
        <el-col :span="19" class="input-col">
          <el-form-item label="Médicament" class="medicament-form-item">
            <el-autocomplete
              v-model="tempMedicamentName"
              :fetch-suggestions="fetchMedicamentSuggestions"
              placeholder="Rechercher ou saisir un médicament"
              @select="handleMedicamentSelect"
              @blur="onMedicamentBlur"
              @input="onMedicamentInput"
              class="w-full"
            />
          </el-form-item>
        </el-col>
        <el-col :span="4" class="button-col">
          <div class="button-container">
            <el-button type="primary" size="small" @click="openMedicamentDetailModal" class="btn-gear">
              <img src="https://clickdoc.webredirect.org/public/Svg/settings.svg" alt="Settings Icon" />
            </el-button>

            <el-button type="primary" size="small" @click="toggleAddMedicamentInput" class="btn-add">
              <img src="https://clickdoc.webredirect.org/public/Svg/plus.svg" alt="Add Icon" />
            </el-button>

            <el-button @click="saveOrdonnanceDetails" class="btn btn-sm btn-block background-clickdoc validate-btn" type="button">
              <el-icon><Select /></el-icon>
            </el-button>
          </div>
        </el-col>

        <el-col :span="24" v-if="showAddMedicamentInput" style="margin-top: 10px">
          <el-form-item>
            <el-input v-model="newMedicamentName" placeholder="Ajoutez un Medicament">
              <template #append>
                <el-button size="small" @click="addNewMedicament()">Ajouter</el-button>
              </template>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <hr class="my-3" />
    <div class="table-container">
      <el-table :data="ordonnanceList" :border="true">
        <el-table-column label="Médicament" width="220">
          <template #default="scope">
            {{ scope.row.medicament }}
          </template>
        </el-table-column>
        <el-table-column v-if="false" label="Mode d'administration" width="220">
          <template #default="scope">
            {{ scope.row.administration_mode }}
          </template>
        </el-table-column>
        <el-table-column v-if="false" label="Site d'application" width="220">
          <template #default="scope">
            {{ scope.row.application_site || "Non spécifié" }}
          </template>
        </el-table-column>
        <el-table-column v-if="false" label="Contexte" width="150">
          <template #default="scope">
            {{ scope.row.treatment_context || "Standard" }}
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
                <el-tag type="info" class="tag-space"> Non spécifié </el-tag>
              </template>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Durée" width="150">
          <template #default="scope">
            {{ scope.row.duration_value }} {{ scope.row.duration_unit }}
          </template>
        </el-table-column>

        <el-table-column label="Commentaire" width="260">
          <template #default="scope">
            <div class="comment-content">
              <el-tag v-if="scope.row.commentaire" type="info" class="tag-space">
                {{ scope.row.commentaire }}
              </el-tag>
              <el-tag v-else type="info" class="tag-space"> Aucun commentaire </el-tag>
            </div>
          </template>
        </el-table-column>

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
              <el-button size="small" type="warning" @click="handleEditOrdonnance(scope.row)" class="action-btn">
                <el-icon><Edit /></el-icon>
              </el-button>

              <el-button size="small" type="danger" @click="removeOrdonnance(scope.row.id)" class="action-btn">
                <el-icon><Delete /></el-icon>
              </el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <div class="text-right mt-3">
      <el-button class="btn btn-sm btn-link text-right right-0" @click="openPrintPreviewModal" style="float: right; text-decoration: none">
        <el-icon style="margin-right: 5px"><Printer /></el-icon> Imprimer
      </el-button>
    </div>

    <PrintModal ref="printModalRef" :title="modalTitle" :url="modalUrl" @close="() => {}" />
  </div>
  <el-dialog
    title="Configurer les détails du médicament"
    v-model="showMedicamentDetailModal"
    width="700px"
    :close-on-click-modal="false"
    class="custom-dialog"
  >
    <div class="modal-body">
      <div class="dose-grid">
        <div v-for="time in timingOptions" :key="time.label" class="dose-card">
          <div class="dose-content">
            <div class="dose-icon">{{ time.icon }}</div>
            <div class="dose-label">{{ time.label }}</div>
            <div class="dose-controls">
              <button class="control-btn" @click="decrementDose(time.label)">
                <span class="minus-icon">−</span>
              </button>
              <span class="dose-value">{{ doseValues[time.label] }}</span>
              <button class="control-btn" @click="incrementDose(time.label)">
                <span class="plus-icon">+</span>
              </button>
            </div>
            <div class="dose-units">dose(s)</div>
          </div>
        </div>
      </div>

      <div class="form-container">
        <div class="form-group">
          <label>Commentaire</label>
          <el-input
            v-model="medicamentDetailForm.comment"
            type="textarea"
            :rows="3"
            placeholder="Ajouter un commentaire..."
            class="comment-textarea"
          />
        </div>

        <div class="form-group">
          <el-button type="text" @click="showMoreFilters = !showMoreFilters" class="toggle-filters-btn">
            {{ showMoreFilters ? "Masquer les filtres" : "Afficher plus de filtres" }}
            <el-icon class="toggle-icon" :class="{ 'is-active': showMoreFilters }">
              <ArrowDown />
            </el-icon>
          </el-button>
        </div>

        <div v-show="showMoreFilters">
          <div class="form-group">
            <label>Contexte du traitement</label>
            <el-select
              v-model="medicamentDetailForm.treatmentContext"
              placeholder="Contexte du traitement"
              class="context-select"
              filterable
              allow-create
            >
              <el-option v-for="context in treatmentContextOptions" :key="context" :label="context" :value="context" />
            </el-select>
          </div>

          <div class="form-group">
            <label>Par voie</label>
            <el-select
              v-model="medicamentDetailForm.administrationMode"
              placeholder="Filtrer par voie"
              class="admin-input"
              filterable
              allow-create
            >
              <el-option v-for="mode in administrationModes" :key="mode" :label="mode" :value="mode" />
            </el-select>
          </div>

          <div class="form-group">
            <label>Site d'application spécifique</label>
            <el-select
              v-model="medicamentDetailForm.applicationSite"
              placeholder="Site d'application"
              class="site-select"
              filterable
              allow-create
            >
              <el-option v-for="site in applicationSiteOptions" :key="site" :label="site" :value="site" />
            </el-select>
          </div>

          <div class="form-group">
            <label>Unité</label>
            <el-select
              v-model="medicamentDetailForm.unit"
              placeholder="Sélectionnez une unité"
              class="unit-select"
              filterable
              allow-create
            >
              <el-option v-for="unit in unitOptions" :key="unit" :label="unit" :value="unit" />
            </el-select>
          </div>

          <div class="form-group">
            <label>Fréquence</label>
            <div class="button-grid">
              <button
                v-for="option in frequencyOptions"
                :key="option"
                :class="['option-button', { active: medicamentDetailForm.frequency.includes(option) }]"
                @click="toggleFrequency(option)"
              >
                {{ option }}
              </button>
            </div>
          </div>

          <div class="form-group">
            <label>Durée</label>
            <div class="duration-container">
              <el-input-number
                v-model="medicamentDetailForm.durationValue"
                :min="0"
                controls-position="right"
                class="duration-input"
              />
              <div class="button-grid duration-buttons">
                <button
                  v-for="unit in durationUnits"
                  :key="unit"
                  :class="['option-button', { active: medicamentDetailForm.durationUnit === unit }]"
                  @click="medicamentDetailForm.durationUnit = unit"
                >
                  {{ unit }}
                </button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Contre-indications d'utilisation</label>
            <el-select
              v-model="medicamentDetailForm.contraindications"
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
          </div>

          <div class="form-group">
            <label>Instructions spéciales</label>
            <el-select
              v-model="medicamentDetailForm.comment"
              placeholder="Sélectionner instructions"
              filterable
              multiple
              allow-create
              :clearable="true"
              class="w-full"
            >
              <el-option v-for="comment in commentOptions" :key="comment" :label="comment" :value="comment" />
            </el-select>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="showMedicamentDetailModal = false">Annuler</el-button>
        <el-button type="primary" @click="saveOrdonnanceDetails">Enregistrer</el-button>
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
  margin-top: 10px;
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

/* Add these new styles */
.toggle-filters-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
  color: var(--el-color-primary);
}

.toggle-icon {
  transition: transform 0.3s ease;
}

.toggle-icon.is-active {
  transform: rotate(180deg);
}

</style>