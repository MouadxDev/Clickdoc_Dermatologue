<script setup lang="ts">
import { Ref, computed, onBeforeMount, ref } from "vue";
import { useConsultStore } from "../../../core/Data/stores/consultation";
import { Demande } from '../../../core/Clients/DemandeAnalyse';
import { Analyses } from '../../../core/Clients/Analyses';
import ENV from '../../../core/env';
import { ElMessage } from 'element-plus';
import PrintModal from '../PrintModal.vue'; // Import the new component

const consult = useConsultStore();

const demande: Ref<any> = ref({
    consultation_id: consult.consult,
    analyse_id: "",
    new_analyse: null,
});


const demandes: Ref<any> = ref([]);
const analyses: Ref<any> = ref([]);
const analyseClient = new Analyses();
const demandeClient = new Demande();

const searchTerm: Ref<string> = ref('');
const showContent = ref(false);
const inputAnalyse = ref("");

// Reference to the PrintModal component
const printModalRef = ref();
const modalUrl = ref("");
const modalTitle = ref("");

const filteredAnalyses = computed(() => {
    return analyses.value.filter((analyse: any) =>
        analyse.libelle.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});



async function getDemande() {
    return await demandeClient.getByConsult(consult.consult);
}

async function setDemande() {
    const selectedId = demande.value.analyse_id;
    const selectedAnalyse = analyses.value.find((a: any) => a.id === selectedId);

    if (!selectedAnalyse && selectedId) {
        // If it's a new analysis not found in the list, send as new_analyse
        demande.value.new_analyse = selectedId;
        demande.value.analyse_id = ""; // Clear ID since it's not a known one
    } else {
        demande.value.new_analyse = "";
    }

    await demandeClient.add(demande.value);
    demandes.value = await getDemande();
    demande.value.analyse_id = "";
}

async function removeDemande(x: number) {
    if (confirm('êtes vous sur de vouloir supprimer cet element') == true) {
        await demandeClient.delete(x);
    }
    demandes.value = await getDemande();
}

onBeforeMount(async () => {
    analyses.value = await analyseClient.getAll();
    demandes.value = await getDemande();
});

async function addMore() {
    const analyseName = inputAnalyse.value;

    if (!analyseName) {
        ElMessage.error("Veuillez entrer un nom d'analyse.");
        return;
    }

    const dataToSend = {
        libelle: analyseName,
    };

    try {
        await analyseClient.add(dataToSend);
        inputAnalyse.value = "";
        ElMessage.success("Analyse ajoutée avec succès.");
        analyses.value = await analyseClient.getAll();
    } catch (error) {
        ElMessage.error("Une erreur s'est produite lors de l'ajout de l'analyse.");
        console.error(error);
    }
}

const handleShowContent = () => {
    showContent.value = !showContent.value;
};

// Open the print modal using our new component
function openPrintModal() {
    modalTitle.value = "Aperçu à imprimer";
    modalUrl.value = `${ENV.VITE_BACKEND}/analyse/${consult.consult}`;
    printModalRef.value.openModal();
}
</script>

<template>
    <div class="container">
        <el-form label-position="top">
            <el-row :gutter="10" class="analyse-row">
                <el-col :span="19" class="input-col">
                    <el-form-item label="Analyse" class="analyse-form-item">
                        <el-select
                            class="w-full"
                            v-model="demande.analyse_id"
                            placeholder="Rechercher une analyse"
                            filterable
                            allow-create
                        >
                            <el-option
                                v-for="m in filteredAnalyses"
                                :key="m.id"
                                :value="m.id"
                                :label="m.libelle"
                                
                            />
                            <!-- @click="async ()=>{await setDemande()}" -->
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="4" class="button-col">
                    <div class="button-container">
                        <el-button type="primary" size="small" @click="handleShowContent" class="btn-add">
                            <img src="https://clickdoc.webredirect.org/public/Svg/plus.svg" alt="Add Icon" />
                        </el-button>

                        <el-button @click="async ()=>{await setDemande()}" class="btn btn-sm btn-block background-clickdoc validate-btn" type="button">
                            <el-icon><Select /></el-icon>
                        </el-button>
                    </div>
                </el-col>
            </el-row>

            <el-row v-if="showContent">
                <el-col :span="24">
                    <el-form-item>
                        <el-input v-model="inputAnalyse" placeholder="Ajouter une analyse">
                            <template #append>
                                <el-button size="small" @click="addMore()">Ajouter</el-button>
                            </template>
                        </el-input>
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
                    <a v-if="scope.row.document" 
                        class="btn btn-link btn-accent btn-sm" 
                        :href="ENV.VITE_BACKEND_Download+'/'+scope.row.document+'/download'" >
                        <el-icon> 
                            <Folder />
                        </el-icon> 
                        voir 
                    </a>
                </template>
            </el-table-column>
            <el-table-column width="70px">
                <template #default="scope">
                    <button class="btn btn-sm btn-error" type="button"  v-if="scope.row.state=='soumise'" @click="async ()=>{ await removeDemande(scope.row.id) }" ><el-icon><Delete/></el-icon></button>
                </template>
            </el-table-column>
        </el-table>

        <el-button class="btn btn-sm btn-link text-right right-0" @click="openPrintModal" style="float: right;text-decoration: none; ">
            <el-icon style="margin-right: 5px;"><Printer /></el-icon> Imprimer
        </el-button>
    
    </div>

    <!-- Using our new PrintModal component -->
    <PrintModal 
        ref="printModalRef" 
        :title="modalTitle" 
        :url="modalUrl" 
        @close="() => {}" 
    />
</template>

<style scoped>
.analyse-row {
    display: flex;
    align-items: end;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 8px;
}

.input-col {
    display: flex;
    align-items: end;
    padding-right: 12px;
}

.analyse-form-item {
    width: 100%;
    margin-bottom: 0;
}

.analyse-form-item :deep(.el-form-item__label) {
    padding-bottom: 4px;
    font-weight: 500;
}

.button-col {
    display: flex;
    align-items: end;
    justify-content: flex-end;
}

.button-container {
    display: flex;
    align-items: center;
    gap: 12px;
    height: 100%;
    padding-bottom: 2px;
}

.btn-add, .validate-btn {
    height: 35px !important;
    min-height: 35px;
    width: 35px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-add:hover, .validate-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-add img {
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

/* Input field styling */
.el-input {
    margin-top: 8px;
}

.el-input :deep(.el-input__wrapper) {
    border-radius: 6px;
}

.el-input :deep(.el-input-group__append) {
    padding: 0 12px;
}

.el-input :deep(.el-input-group__append .el-button) {
    margin: 0;
    height: 100%;
    border-radius: 0 6px 6px 0;
}

/* Print button styling */
.text-right {
    margin-top: 16px;
}

.btn-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-link:hover {
    background-color: #f8f9fa;
}

.btn-link .el-icon {
    font-size: 16px;
}
</style>