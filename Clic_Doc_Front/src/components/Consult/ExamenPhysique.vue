<script setup lang="ts">
import { Ref, onBeforeMount, ref, computed } from "vue";
import { useConsultStore } from "../../../core/Data/stores/consultation";
import { ExamenPhysique } from '../../../core/Clients/Examen';
import { useAuthStore } from "../../../core/Data/stores/auth";
import { ConstFiles } from '../../../core/Clients/ConstFiles';

const consult = useConsultStore();
const examenClient = new ExamenPhysique();
const constClient = new ConstFiles();
const authStore = useAuthStore();
const specialty = authStore.user.specialty;

const examen: Ref<any> = ref({});

// First logic (specialty === 1) - Individual body parts
const data_visage = ref<{ label: string; value: string }[]>([]);
const data_corps = ref<{ label: string; value: string }[]>([]);
const data_ongles = ref<{ label: string; value: string }[]>([]);
const data_cheveux = ref<{ label: string; value: string }[]>([]);

const loadingVisage = ref(false);
const loadingCorps = ref(false);
const loadingOngles = ref(false);
const loadingCheveux = ref(false);

// Second logic (specialty === 2) - Body systems
const systemOptions: Ref<{ label: string; value: string }[]>[] = Array.from(
  { length: 12 },
  () => ref([])
);

const systemLabels = [
  "Système respiratoire",
  "Système cardiovasculaire", 
  "Système neurologique",
  "Système musculo-squelettique",
  "Système gastro-intestinal",
  "Système génito-urinaire",
  "Système endocrinien",
  "Système lymphatique",
  "Système hématologique",
  "Système cutané",
  "Système auditif",
  "Système visuel",
];

// Computed property to determine which logic to use
const isSpecialty1 = computed(() => Number(specialty) === 1);
const isSpecialty2 = computed(() => Number(specialty) === 2);
const isSpecialty3 = computed(() => Number(specialty) === 3);

// Generic fetch function for different data types (specialty 1)
const fetchData = async (query: string, dataType: string, targetArray: Ref<{ label: string; value: string }[]>, loadingRef: Ref<boolean>) => {
    loadingRef.value = true;
    try {
        const response = await constClient.getAll(query, dataType);
        targetArray.value = response.data.map((item: any) => ({
            label: item.label,
            value: item.label,
        }));
    } catch (e) {
        targetArray.value = [];
    } finally {
        loadingRef.value = false;
    }
};

// Specific fetch functions for specialty 1
const fetchVisageData = async (query: string) => {
    await fetchData(query, 'data_visage', data_visage, loadingVisage);
};

const fetchCorpsData = async (query: string) => {
    await fetchData(query, 'data_corps', data_corps, loadingCorps);
};

const fetchOnglesData = async (query: string) => {
    await fetchData(query, 'data_ongles', data_ongles, loadingOngles);
};

const fetchCheveuxData = async (query: string) => {
    await fetchData(query, 'data_cheveux', data_cheveux, loadingCheveux);
};

// Specific fetch functions for specialty 3
const fetchEarOptions = async (query: string) => {
    await fetchData(query, 'data_ear', data_visage, loadingVisage);
};

const fetchNoseAndSinusOptions = async (query: string) => {
    await fetchData(query, 'data_nose_and_sinus', data_corps, loadingCorps);
};

const fetchThroatAndPharynxOptions = async (query: string) => {
    await fetchData(query, 'data_throat_and_pharynx', data_ongles, loadingOngles);
};

const fetchNeckOptions = async (query: string) => {
    await fetchData(query, 'data_neck', data_cheveux, loadingCheveux);
};


// Fetch function for specialty 2
async function fetchSystemOptions(
  query: string,
  type: string,
  target: Ref<{ label: string; value: string }[]>
) {
  try {
    const response = await constClient.getAll(query, type);
    target.value = response.data.map((item: any) => ({
      label: item.label,
      value: item.label,
    }));
  } catch (error) {
    target.value = [];
  }
}

// Load examination data based on specialty
async function getExamenPhysique() {
    const data: any = await examenClient.getByID(consult.examen_id);
    
    if (isSpecialty1.value) {
        // Specialty 1 logic
        return {
            id: data.id,
            hair: JSON.parse(data.hair),
            nails: JSON.parse(data.nails),
            face: JSON.parse(data.face),
            body: JSON.parse(data.body)
        };
    } else if (isSpecialty2.value) {
        // Specialty 2 logic
        return {
            id: data.id,
            sys1: JSON.parse(data.respiratory_system),
            sys2: JSON.parse(data.cardiovascular_system),
            sys3: JSON.parse(data.neurological_system),
            sys4: JSON.parse(data.musculoskeletal_system),
            sys5: JSON.parse(data.gastrointestinal_system),
            sys6: JSON.parse(data.genitourinary_system),
            sys7: JSON.parse(data.endocrine_system),
            sys8: JSON.parse(data.lymphatic_system),
            sys9: JSON.parse(data.hematologic_system),
            sys10: JSON.parse(data.cutaneous_system),
            sys11: JSON.parse(data.auditory_system),
            sys12: JSON.parse(data.visual_system),
        };
    }
     else if (isSpecialty3.value) {
        // Specialty 2 logic
        return {
            id:data.id,
            hair : JSON.parse(data.hair),
            nails : JSON.parse(data.nails),
            face : JSON.parse(data.face),
            body : JSON.parse(data.body)
        };
    }
    
    return { id: data.id };
}

// Save examination data based on specialty
async function setExamenPhysique() {
    if (isSpecialty1.value) {
        // Specialty 1 logic - update with hair, nails, face, body
        await examenClient.update(examen.value);
    } else if (isSpecialty2.value) {
        // Specialty 2 logic - update with systems
        const payload = {
            id: examen.value.id,
            sys1: examen.value.sys1,
            sys2: examen.value.sys2,
            sys3: examen.value.sys3,
            sys4: examen.value.sys4,
            sys5: examen.value.sys5,
            sys6: examen.value.sys6,
            sys7: examen.value.sys7,
            sys8: examen.value.sys8,
            sys9: examen.value.sys9,
            sys10: examen.value.sys10,
            sys11: examen.value.sys11,
            sys12: examen.value.sys12,
        };
        await examenClient.update(payload);
    }
     else if (isSpecialty3.value) {
        await examenClient.update(examen.value);
    }
    
    examen.value = await getExamenPhysique();
}

// Initialize data on component mount
onBeforeMount(async () => {
    examen.value = await getExamenPhysique();
    
    if (isSpecialty1.value) {
        // Initialize specialty 1 data
        await Promise.all([
            fetchVisageData(''),
            fetchCorpsData(''),
            fetchOnglesData(''),
            fetchCheveuxData('')
        ]);
    } else if (isSpecialty2.value) {
        // Initialize specialty 2 data
        await Promise.all(
            systemOptions.map((refArray, index) =>
                fetchSystemOptions("", `sys${index + 1}`, refArray)
            )
        );
    }
     else if (isSpecialty3.value) {
        // Initialize specialty 2 data
        await Promise.all([
            fetchEarOptions(''),
            fetchNoseAndSinusOptions(''),
            fetchThroatAndPharynxOptions(''),
            fetchNeckOptions('')
        ]);
    }
});
</script>

<template>
    <el-form label-position="top">
        <!-- Specialty 1 Template - Individual body parts -->
        <template v-if="isSpecialty1">
            <el-form-item label="Visage">
                <el-select-v2
                    v-model="examen.face"
                    :options="data_visage"
                    placeholder="Selectionner"
                    multiple
                    filterable
                    remote
                    reserve-keyword
                    allow-create
                    class="w-full"
                    clearable
                    :remote-method="fetchVisageData"
                    :loading="loadingVisage"
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
                    filterable
                    remote
                    reserve-keyword
                    allow-create
                    class="w-full"
                    clearable
                    :remote-method="fetchCorpsData"
                    :loading="loadingCorps"
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
                    filterable
                    remote
                    reserve-keyword
                    allow-create
                    class="w-full"
                    clearable
                    :remote-method="fetchOnglesData"
                    :loading="loadingOngles"
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
                    filterable
                    remote
                    reserve-keyword
                    allow-create
                    class="w-full"
                    clearable
                    :remote-method="fetchCheveuxData"
                    :loading="loadingCheveux"
                    @change="async ()=>{await setExamenPhysique()}"
                    :disabled="!consult.edit"
                />
            </el-form-item>
        </template>

        <!-- Specialty 2 Template - Body systems -->
        <template v-else-if="isSpecialty2">
            <template v-for="(label, i) in systemLabels" :key="i">
                <el-form-item :label="label">
                    <el-select-v2
                        v-model="examen[`sys${i + 1}`]"
                        :options="systemOptions[i]"
                        placeholder="Selectionner"
                        multiple
                        filterable
                        allow-create
                        clearable
                        remote
                        :remote-method="(q) => fetchSystemOptions(q, `sys${i + 1}`, systemOptions[i])"
                        class="w-full"
                        @change="setExamenPhysique"
                        :disabled="!consult.edit"
                    />
                </el-form-item>
            </template>
        </template>

        <template v-else-if="isSpecialty3">
            <el-form-item label="OREILLE">
                <el-select-v2
                    v-model="examen.face"
                    :options="data_visage"
                    placeholder="Selectionner"
                    multiple
                    filterable
                    class="w-full"
                    clearable
                    @change="async () => { await setExamenPhysique() }"
                    :disabled="!consult.edit"
                />
            </el-form-item>
            <el-form-item label="NEZ ET SINUS">
                <el-select-v2
                    v-model="examen.body"
                    :options="data_corps"
                    placeholder="Selectionner"
                    multiple
                    filterable
                    class="w-full"
                    clearable
                    @change="async () => { await setExamenPhysique() }"
                    :disabled="!consult.edit"
                />
            </el-form-item>
            <el-form-item label="GORGE ET PHARYNX">
                <el-select-v2
                    v-model="examen.nails"
                    :options="data_ongles"
                    placeholder="Selectionner"
                    multiple
                    filterable
                    class="w-full"
                    clearable
                    @change="async () => { await setExamenPhysique() }"
                    :disabled="!consult.edit"
                />
            </el-form-item>
            <el-form-item label="COU">
                <el-select-v2
                    v-model="examen.hair"
                    :options="data_cheveux"
                    placeholder="Selectionner"
                    multiple
                    filterable
                    class="w-full"
                    clearable
                    @change="async () => { await setExamenPhysique() }"
                    :disabled="!consult.edit"
                />
            </el-form-item>
</template>


        <!-- Fallback for other specialties -->
        <template v-else>
            <el-form-item>
                <p>Cette spécialité n'est pas encore supportée.</p>
            </el-form-item>
        </template>
    </el-form>
</template>