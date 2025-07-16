<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue';
import { ElMessage, ElPagination } from 'element-plus';
import { FichePatient } from '../../../core/Clients/FichePatient';

const props = defineProps<{
  id: number;
}>();

// State management
const patientId = ref<number>(props.id);
const patient = ref<any>(null);
const consultations = ref<any[]>([]);
const totalConsultations = ref<number>(0);
const loading = ref<boolean>(false);
const currentPage = ref<number>(1);
const perPage = ref<number>(5);
const searchMotif = ref<string>('');

const fichePatientClient = new FichePatient();

// Fetch patient data and consultations
async function fetchPatientDetails(id: number, page: number = 1) {
  loading.value = true;
  try {
    const response = await fichePatientClient.getPatientDetails(id, page, perPage.value);
    if (response && response.patient) {
      patient.value = response.patient;
      consultations.value = response.consultations || [];
      totalConsultations.value = response.total_consultations || 0;
    } else {
      ElMessage.error('Aucune donnée trouvée pour ce patient.');
    }
  } catch (error) {
    console.error(error);
    ElMessage.error('Erreur lors de la récupération des données du patient.');
  } finally {
    loading.value = false;
  }
}

// Handle page changes
function handlePageChange(page: number) {
  currentPage.value = page;
  fetchPatientDetails(patientId.value, page);
}

// Parse JSON motifs
function parseMotifs(motifStr: string): string[] {
  try {
    if (!motifStr || motifStr === '[]') return [];
    return JSON.parse(motifStr);
  } catch (e) {
    console.error('Error parsing motifs:', e);
    return [];
  }
}

// Calculate relative time in French
function getRelativeTime(dateStr: string | undefined): string {
  if (!dateStr) return '';

  const now = new Date();
  const pastDate = new Date(dateStr.replace(' ', 'T')); // parse "YYYY-MM-DD HH:mm:ss" as ISO

  if (isNaN(pastDate.getTime())) return ''; // invalid date

  const diffMs = now.getTime() - pastDate.getTime();

  if (diffMs < 0) return 'à l\'instant'; // future date or error, fallback

  const diffSeconds = Math.floor(diffMs / 1000);
  const diffMinutes = Math.floor(diffSeconds / 60);
  const diffHours = Math.floor(diffMinutes / 60);
  const diffDays = Math.floor(diffHours / 24);
  const diffWeeks = Math.floor(diffDays / 7);
  const diffMonths = Math.floor(diffDays / 30);
  const diffYears = Math.floor(diffDays / 365);

  if (diffSeconds < 60) {
    return 'À l\'instant';
  }
  if (diffMinutes < 60) {
    return diffMinutes === 1
      ? 'il y a 1 minute'
      : `il y a ${diffMinutes} minutes`;
  }
  if (diffHours < 24) {
    return diffHours === 1
      ? 'il y a 1 heure'
      : `il y a ${diffHours} heures`;
  }
  if (diffDays === 0) {
    return "aujourd'hui";
  }
  if (diffDays === 1) {
    return 'hier';
  }
  if (diffDays < 7) {
    return `il y a ${diffDays} jours`;
  }
  if (diffWeeks < 5) {
    return diffWeeks === 1
      ? 'il y a 1 semaine'
      : `il y a ${diffWeeks} semaines`;
  }
  if (diffMonths < 12) {
    return diffMonths === 1
      ? 'il y a 1 mois'
      : `il y a ${diffMonths} mois`;
  }
  return diffYears === 1
    ? 'il y a 1 an'
    : `il y a ${diffYears} ans`;
}


// Format date from dd/mm/yyyy to a more readable format
function formatDate(dateStr: string): string {
  if (!dateStr) return '';
  const parts = dateStr.split('/');
  if (parts.length !== 3) return dateStr;
  
  return `${parts[0]} ${getMonthName(parseInt(parts[1]))} ${parts[2]}`;
}

function getMonthName(monthNum: number): string {
  const months = [
    'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
    'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
  ];
  return months[monthNum - 1] || '';
}

// Get diabetes status text
function getDiabetesStatus(code: number): string {
  switch(code) {
    case 1: return 'Type 1';
    case 2: return 'Type 2';
    case 3: return 'Gestationnel';
    default: return 'Non diabétique';
  }
}

// Filter consultations by motif search term
const filteredConsultations = computed(() => {
  if (!searchMotif.value.trim()) return consultations.value;
  
  const searchTerm = searchMotif.value.toLowerCase();
  
  return consultations.value.filter(consultation => {
    // Check motifs
    const motifs = parseMotifs(consultation.motif);
    const matchesMotif = motifs.some(motif => motif.toLowerCase().includes(searchTerm));
    
    // Check notes (plain text with line breaks)
    const notes = consultation.notes ? consultation.notes.split('\n') : [];
    const matchesNotes = notes.some(note => note.toLowerCase().includes(searchTerm));
    
    // Check medications
    const meds = consultation.medications || [];
    const matchesMedications = meds.some(med => med.toLowerCase().includes(searchTerm));
    
    // Check analyses
    const analyses = consultation.analyses || [];
    const matchesAnalyses = analyses.some(analysis => analysis.toLowerCase().includes(searchTerm));
    
    return matchesMotif || matchesMedications || matchesAnalyses || matchesNotes;
  });
});



// Get avatar initials as fallback
function getInitials(name: string, surname: string): string {
  if (!name && !surname) return '?';
  return `${name.charAt(0)}${surname.charAt(0)}`.toUpperCase();
}

watchEffect(() => {
  if (patientId.value) {
    fetchPatientDetails(patientId.value, currentPage.value);
  }
});
</script>

<template>
  <div class="patient-history-container">
    <!-- Loader -->
    <div v-if="loading" class="loader-container">
      <div class="loader"></div>
      <p>Chargement des données patient...</p>
    </div>

    <template v-else>
      <!-- Patient Information Card -->
      <div v-if="patient" class="patient-info-card">
        <div class="patient-header">
          <div class="avatar-container">
            <img v-if="patient.avatar" :src="patient.avatar" :alt="`Photo de ${patient.surname} ${patient.name}`" class="avatar" />
            <div v-else class="avatar-placeholder">{{ getInitials(patient.name, patient.surname) }}</div>
          </div>
          
          <div class="patient-identity">
            <h1 class="patient-name">{{ patient.surname }} {{ patient.name }}</h1>
            <div class="patient-id">ID: {{ patient.uid }}</div>
            <div class="patient-badges">
              <span class="badge badge-blood">{{ patient.blood_type }}</span>
              <span v-if="patient.diabetes > 0" class="badge badge-diabetes">Diabète {{ getDiabetesStatus(patient.diabetes) }}</span>
              <span class="badge badge-coverage">{{ patient.coverage_type }}: {{ patient.coverage_number }}</span>
            </div>
          </div>
        </div>
        
        <div class="patient-details">
          <div class="details-column">
            <div class="detail-item">
              <span class="detail-label">Date de naissance</span>
              <span class="detail-value">{{ formatDate(patient.date_of_birth) }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Sexe</span>
              <span class="detail-value">{{ patient.sex === 'M' ? 'Masculin' : 'Féminin' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">CIN</span>
              <span class="detail-value">{{ patient.CIN || 'Non spécifié' }}</span>
            </div>
          </div>
          
          <div class="details-column">
            <div class="detail-item">
              <span class="detail-label">Téléphone</span>
              <span class="detail-value">{{ patient.phone }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Couverture</span>
              <span class="detail-value">{{ patient.coverage_type }} ({{ patient.coverage_number }})</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Observation</span>
              <span class="detail-value">{{ patient.observation || 'Aucune' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- No Patient Data -->
      <div v-else class="no-data-container">
        <div class="no-data-icon">🔍</div>
        <p>Aucune donnée patient trouvée.</p>
      </div>

      <!-- Consultation Timeline Section -->
      <div class="consultations-section">
        <div class="section-header">
          <h2>Historique des consultations</h2>
          <div class="search-container">
            <input 
              type="text" 
              v-model="searchMotif" 
              placeholder="Rechercher par motif..." 
              class="search-input"
            />
          </div>
        </div>

        <!-- Timeline -->
        <div v-if="filteredConsultations.length > 0" class="timeline-container">
          <div class="timeline-line"></div>
          
          <div 
            v-for="(consultation, index) in filteredConsultations" 
            :key="consultation.consultation_id"
            class="timeline-item"
            :class="{ 'timeline-item-left': index % 2 === 0, 'timeline-item-right': index % 2 === 1 }"
          >
            <div class="timeline-marker"></div>
            <div class="timeline-date">{{ getRelativeTime(consultation.created_at) }}</div>
            
            <div class="timeline-content">
              <div class="consultation-header">
                <h3>Consultation #{{ consultation.consultation_id }}</h3>
              </div>
              
              <div class="consultation-body">
                <!-- Notes -->
                <div class="consultation-section">
                  <h4>Notes</h4>
                  <div v-if="consultation.notes && consultation.notes.trim() !== ''" class="notes-container">
                      <p class="preformatted-text">{{ consultation.notes }}</p>
                  </div>
                  <div v-else class="empty-field">Aucune note spécifiée</div>
              </div>

                <!-- Motifs -->
                <div class="consultation-section">
                  <h4>Motifs</h4>
                  <div v-if="parseMotifs(consultation.motif).length > 0" class="motifs-container">
                    <span 
                      v-for="(motif, motifIndex) in parseMotifs(consultation.motif)" 
                      :key="motifIndex"
                      class="motif-tag"
                    >
                      {{ motif }}
                    </span>
                  </div>
                  <div v-else class="empty-field">Aucun motif spécifié</div>
                </div>
                
                <!-- Analyses -->
                <div class="consultation-section">
                  <h4>Analyses</h4>
                  <div v-if="consultation.analyses && consultation.analyses.length > 0" class="analyses-list">
                    {{ consultation.analyses.join(', ') }}
                  </div>
                  <div v-else class="empty-field">Aucune analyse</div>
                </div>
                
                <!-- Medications -->
                <div class="consultation-section">
                  <h4>Médicaments</h4>
                  <div v-if="consultation.medications && consultation.medications.length > 0" class="medications-list">
                    {{ consultation.medications.join(', ') }}
                  </div>
                  <div v-else class="empty-field">Aucun médicament</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- No Consultations Found -->
        <div v-else class="no-data-container">
          <div class="no-data-icon">📋</div>
          <p>Aucune consultation trouvée.</p>
        </div>
        
        <!-- Pagination -->
        <div v-if="totalConsultations > perPage" class="pagination-container">
          <ElPagination
            :current-page="currentPage"
            :page-size="perPage"
            :total="totalConsultations"
            layout="prev, pager, next"
            @current-change="handlePageChange"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.patient-history-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.5rem;
  font-family: 'Inter', sans-serif;
  color: #2c3e50;
}

/* Loader */
.loader-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
}

.loader {
  border: 4px solid rgba(0, 123, 255, 0.1);
  border-radius: 50%;
  border-top: 4px solid #0077cc;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Patient Information Card */
.patient-info-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.patient-header {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.avatar-container {
  margin-right: 1.5rem;
  flex-shrink: 0;
}

.avatar {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #f2f6fa;
}

.avatar-placeholder {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background-color: #e0ebf5;
  color: #4a90e2;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  border: 3px solid #f2f6fa;
}

.patient-identity {
  flex-grow: 1;
}

.patient-name {
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
  color: #2c3e50;
}

.patient-id {
  font-size: 0.9rem;
  color: #8c9db5;
  margin-bottom: 0.75rem;
}

.patient-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.badge {
  /* display: inline-block; */
  padding: 0.25rem 0.75rem;
  /* border-radius: 20px; */
  font-size: 0.8rem;
  font-weight: 600;
  height: 36px;
  border:none
}

.badge-blood {
  background-color: #ffebee;
  color: #e53935;
}

.badge-diabetes {
  background-color: #fefbe7;
  color: #f59e0b;
}

.badge-coverage {
  background-color: #e8f4fd;
  color: #0288d1;
}

.patient-details {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #f0f3f8;
}

.details-column {
  flex: 1;
  min-width: 250px;
}

.detail-item {
  margin-bottom: 0.75rem;
}

.detail-label {
  display: block;
  font-size: 0.8rem;
  color: #8c9db5;
  margin-bottom: 0.25rem;
}

.detail-value {
  font-size: 1rem;
  color: #2c3e50;
}

/* Consultations Section */
.consultations-section {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  color: #2c3e50;
}

.search-container {
  width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e0e7ee;
  border-radius: 6px;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  background-color: #f9fafb;
}

.search-input:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

/* Timeline */
.timeline-container {
  position: relative;
  padding: 1rem 0 2rem;
}

.timeline-line {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 2px;
  background-color: #e0e7ee;
  transform: translateX(-50%);
}

.timeline-item {
  position: relative;
  margin-bottom: 3rem;
  width: 47%;
}

.timeline-item-left {
  margin-right: auto;
}

.timeline-item-right {
  margin-left: auto;
}

.timeline-marker {
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background-color: #4a90e2;
  top: 0;
  transform: translateY(50%);
  z-index: 2;
}

.timeline-item-left .timeline-marker {
  right: -44px;
}

.timeline-item-right .timeline-marker {
  left: -44px;
}

.timeline-date {
  position: absolute;
  top: 0;
  font-size: 0.8rem;
  color: #8c9db5;
  background: #f2f6fa;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  transform: translateY(-50%);
}

.timeline-item-left .timeline-date {
  right: -140px;
}

.timeline-item-right .timeline-date {
  left: -140px;
}

.timeline-content {
  background-color: #f9fafb;
  border-radius: 8px;
  padding: 1.25rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.timeline-content:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.consultation-header {
  margin-bottom: 1rem;
}

.consultation-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #4a5568;
  margin: 0;
}

.consultation-body {
  font-size: 0.95rem;
}

.consultation-section {
  margin-bottom: 1rem;
}

.consultation-section h4 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #718096;
  margin: 0 0 0.5rem 0;
}

.motifs-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.motif-tag {
  background-color: #e8f4fd;
  color: #0288d1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.analyses-list, .medications-list {
  line-height: 1.5;
  color: #4a5568;
}

.empty-field {
  font-style: italic;
  color: #a0aec0;
  font-size: 0.9rem;
}

/* No Data Display */
.no-data-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
  text-align: center;
  color: #8c9db5;
}

.no-data-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

/* Pagination */
.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .patient-header {
    flex-direction: column;
    text-align: center;
  }
  
  .avatar-container {
    margin-right: 0;
    margin-bottom: 1rem;
  }
  
  .patient-badges {
    justify-content: center;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .search-container {
    width: 100%;
  }
  
  .timeline-line {
    left: 30px;
  }
  
  .timeline-item {
    width: auto;
    margin-left: 60px !important;
    margin-right: 0 !important;
  }
  
  .timeline-item-left .timeline-marker,
  .timeline-item-right .timeline-marker {
    left: -30px;
    right: auto;
  }
  
  .timeline-item-left .timeline-date,
  .timeline-item-right .timeline-date {
    left: 0;
    right: auto;
    top: -30px;
  }
}
.preformatted-text {
    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.6;
}
</style>