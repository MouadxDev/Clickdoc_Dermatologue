<script setup lang="ts">
import { onBeforeMount, ref, Ref } from 'vue';
import moment from "moment";
import { ElMessage } from 'element-plus';
import { Edit, Delete, Plus, Calendar, Comment } from '@element-plus/icons-vue';

import { ActeMedical } from '../../../core/Clients/ActeMedical';
import { Soin } from '../../../core/Clients/Soin';

const acteClient = new ActeMedical();
const soinClient = new Soin();

const soins: Ref<any> = ref([]);
const actes: Ref<any> = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const showCommentModal = ref(false);
const showAddSessionModal = ref(false);

// Form states
const newSoin = ref({
  acte_id: null,
  nbr_sceances: 1,
  commentaire: '',
  prix: 0
});

const editSoin = ref({
  id: null,
  nbr_sceances: 0,
  commentaire: '',
  prix: 0
});

const currentComment = ref({
  id: null,
  commentaire: ''
});

const newSession = ref({
  soin_id: null,
  date: new Date().toISOString().split('T')[0],
  commentaire: ''
});

const props = defineProps<{
  id: number
}>();

// Fetch all soins for the patient
async function getSoins() {
  try {
    const response = await soinClient.getAllT(props.id);
    soins.value = response;
  } catch (error) {
    ElMessage.error('Erreur lors de la récupération des soins');
  }
}

// Add new soin
async function addSoin() {
  try {
    await soinClient.add({
      ...newSoin.value,
      patient_id: props.id
    });
    ElMessage.success('Soin ajouté avec succès');
    showAddModal.value = false;
    resetNewSoinForm();
    await getSoins();
  } catch (error) {
    ElMessage.error('Erreur lors de l\'ajout du soin');
  }
}

// Update soin
async function updateSoin() {
  try {
    await soinClient.update(editSoin.value.id, editSoin.value);
    ElMessage.success('Soin mis à jour avec succès');
    showEditModal.value = false;
    await getSoins();
  } catch (error) {
    ElMessage.error('Erreur lors de la mise à jour du soin');
  }
}

// Add comment to soin
async function addComment() {
  try {
    await soinClient.addComment(currentComment.value.id, currentComment.value.commentaire);
    ElMessage.success('Commentaire ajouté avec succès');
    showCommentModal.value = false;
    await getSoins();
  } catch (error) {
    ElMessage.error('Erreur lors de l\'ajout du commentaire');
  }
}

// Add new session
async function addSession() {
  try {
    await soinClient.addSession(newSession.value.soin_id, {
      date: newSession.value.date,
      commentaire: newSession.value.commentaire
    });
    ElMessage.success('Séance ajoutée avec succès');
    showAddSessionModal.value = false;
    resetNewSessionForm();
    await getSoins();
  } catch (error) {
    ElMessage.error('Erreur lors de l\'ajout de la séance');
  }
}

// Delete soin
async function deleteSoin(id: number) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce soin ?')) {
    try {
      await soinClient.delete(id);
      ElMessage.success('Soin supprimé avec succès');
      await getSoins();
    } catch (error) {
      ElMessage.error('Erreur lors de la suppression du soin');
    }
  }
}

// Reset forms
function resetNewSoinForm() {
  newSoin.value = {
    acte_id: null,
    nbr_sceances: 1,
    commentaire: '',
    prix: 0
  };
}

function resetNewSessionForm() {
  newSession.value = {
    soin_id: null,
    date: new Date().toISOString().split('T')[0],
    commentaire: ''
  };
}

// Initialize
onBeforeMount(async () => {
  try {
    actes.value = await acteClient.getAll();
    await getSoins();
  } catch (error) {
    ElMessage.error('Erreur lors du chargement initial');
  }
});
</script>

<template>
  <div class="traitement-container">
    <!-- Header with Add Button -->
    <div class="header-actions">
      <h2>Traitements et soins</h2>
      <el-button type="primary" @click="showAddModal = true">
        <el-icon><Plus /></el-icon>
        Nouveau soin
      </el-button>
    </div>

    <!-- Main Table -->
    <el-table :data="soins" :border="true" class="soins-table">
      <el-table-column label="Libellé" prop="libelle" />
      <el-table-column label="Nombre de séances" prop="nbr_sceances" />
      <el-table-column label="Séances effectuées">
        <template #default="scope">
          {{ scope.row.nbr_performed.length }}
        </template>
      </el-table-column>
      <el-table-column label="Historique">
        <template #default="scope">
          <div class="history-list">
            <div v-for="h in scope.row.nbr_performed" :key="h.id" class="history-item">
              <span class="date">{{ moment(h.created_at).format("DD/MM/yyyy") }}</span>
              <span v-if="h.commentaire" class="comment">{{ h.commentaire }}</span>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Commentaire">
        <template #default="scope">
          <div class="comment-cell">
            <span v-if="scope.row.commentaire">{{ scope.row.commentaire }}</span>
            <el-button link type="primary" @click="currentComment = { id: scope.row.id, commentaire: scope.row.commentaire }; showCommentModal = true">
              <el-icon><Comment /></el-icon>
            </el-button>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Actions" width="200">
        <template #default="scope">
          <div class="action-buttons">
            <el-button 
              link 
              type="primary" 
              v-if="scope.row.nbr_sceances > scope.row.nbr_performed.length"
              @click="newSession.soin_id = scope.row.id; showAddSessionModal = true"
            >
              <el-icon><Calendar /></el-icon>
            </el-button>
            <el-button 
              link 
              type="primary" 
              @click="editSoin = { ...scope.row }; showEditModal = true"
            >
              <el-icon><Edit /></el-icon>
            </el-button>
            <el-button 
              link 
              type="danger" 
              @click="deleteSoin(scope.row.id)"
            >
              <el-icon><Delete /></el-icon>
            </el-button>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <!-- Add New Soin Modal -->
    <el-dialog 
      title="Nouveau soin" 
      v-model="showAddModal"
      width="50%"
    >
      <el-form :model="newSoin" label-position="top">
        <el-form-item label="Type de soin">
          <el-select v-model="newSoin.acte_id" class="w-full">
            <el-option
              v-for="acte in actes"
              :key="acte.id"
              :label="acte.libelle"
              :value="acte.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Nombre de séances">
          <el-input-number v-model="newSoin.nbr_sceances" :min="1" />
        </el-form-item>
        <el-form-item label="Prix">
          <el-input-number v-model="newSoin.prix" :min="0" />
        </el-form-item>
        <el-form-item label="Commentaire">
          <el-input
            v-model="newSoin.commentaire"
            type="textarea"
            :rows="3"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showAddModal = false">Annuler</el-button>
        <el-button type="primary" @click="addSoin">Ajouter</el-button>
      </template>
    </el-dialog>

    <!-- Edit Soin Modal -->
    <el-dialog 
      title="Modifier le soin" 
      v-model="showEditModal"
      width="50%"
    >
      <el-form :model="editSoin" label-position="top">
        <el-form-item label="Nombre de séances">
          <el-input-number v-model="editSoin.nbr_sceances" :min="1" />
        </el-form-item>
        <el-form-item label="Prix">
          <el-input-number v-model="editSoin.prix" :min="0" />
        </el-form-item>
        <el-form-item label="Commentaire">
          <el-input
            v-model="editSoin.commentaire"
            type="textarea"
            :rows="3"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showEditModal = false">Annuler</el-button>
        <el-button type="primary" @click="updateSoin">Mettre à jour</el-button>
      </template>
    </el-dialog>

    <!-- Add Comment Modal -->
    <el-dialog 
      title="Ajouter un commentaire" 
      v-model="showCommentModal"
      width="50%"
    >
      <el-form :model="currentComment" label-position="top">
        <el-form-item label="Commentaire">
          <el-input
            v-model="currentComment.commentaire"
            type="textarea"
            :rows="3"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showCommentModal = false">Annuler</el-button>
        <el-button type="primary" @click="addComment">Enregistrer</el-button>
      </template>
    </el-dialog>

    <!-- Add Session Modal -->
    <el-dialog 
      title="Ajouter une séance" 
      v-model="showAddSessionModal"
      width="50%"
    >
      <el-form :model="newSession" label-position="top">
        <el-form-item label="Date">
          <el-date-picker
            v-model="newSession.date"
            type="date"
            format="DD/MM/YYYY"
            value-format="YYYY-MM-DD"
          />
        </el-form-item>
        <el-form-item label="Commentaire">
          <el-input
            v-model="newSession.commentaire"
            type="textarea"
            :rows="3"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showAddSessionModal = false">Annuler</el-button>
        <el-button type="primary" @click="addSession">Ajouter</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<style scoped>
.traitement-container {
  padding: 1rem;
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.soins-table {
  margin-bottom: 1.5rem;
}

.history-list {
  max-height: 150px;
  overflow-y: auto;
}

.history-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
}

.history-item:last-child {
  border-bottom: none;
}

.date {
  font-weight: 500;
  color: #409EFF;
}

.comment {
  font-size: 0.875rem;
  color: #666;
}

.comment-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .header-actions {
    flex-direction: column;
    gap: 1rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>