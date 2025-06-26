<template>
    <div class="waiting-room-container">
      <div class="waiting-room-content">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p class="loading-text">Chargement des patients...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="patients.length === 0" class="empty-state">
          <div class="empty-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <h3 class="empty-title">Aucun patient en attente</h3>
          <p class="empty-subtitle">La salle d'attente est vide pour le moment</p>
        </div>
        
        <!-- Patient List -->
        <div v-else class="patients-list">
          <div 
            v-for="patient in patients" 
            :key="patient.id"
            class="patient-card"
            @click="handlePatientClick(patient)"
          >
            <!-- Card Header -->
            <div class="card-header">
              <div class="patient-avatar">
                <img 
                  :src="patient.avatar || '/default-avatar.png'" 
                  :alt="patient.name"
                  class="avatar-image"
                  @error="handleImageError"
                />
                <div class="avatar-fallback">
                  {{ getInitials(patient.surname, patient.name) }}
                </div>
              </div>
              
              <div class="patient-info">
                <h3 class="patient-name">
                  {{ patient.surname }} {{ patient.name }}
                </h3>
                <p class="patient-type">{{ patient.type }}</p>
              </div>
              
              <div class="appointment-time">
                <div class="time-badge">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                  </svg>
                  {{ patient.heure }}
                </div>
              </div>
            </div>
            
            <!-- Card Body -->
            <div class="card-body">
              <div class="patient-details">
                <div class="detail-item" v-if="patient.age">
                  <span class="detail-label">Âge</span>
                  <span class="detail-value">{{ patient.age }} ans</span>
                </div>
                
                <div class="detail-item" v-if="patient.phone">
                  <span class="detail-label">Téléphone</span>
                  <span class="detail-value">{{ formatPhone(patient.phone) }}</span>
                </div>
                
                <div class="detail-item" v-if="patient.statut">
                  <span class="detail-label">Statut</span>
                  <span class="status-badge" :class="getStatusClass(patient.statut)">
                    <span class="status-dot"></span>
                    {{ patient.statut }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Card Actions -->
            <div class="card-actions" v-if="actions && actions.length > 0">
              <button 
                v-for="action in actions" 
                :key="action.icon"
                @click.stop="action.action(patient)"
                class="action-btn"
                :title="action.label"
              >
                <component :is="action.icon" class="action-icon"/>
                <span>{{ action.text || action.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onBeforeMount, watch } from 'vue';
  
  const props = defineProps<{
    client: any;
    actions?: any[];
    triggerStore?: any;
    patient_id?: number;
    hasFilters?: boolean;
    filters?: any;
  }>();
  
  const patients = ref([]);
  const loading = ref(true);
  const current_page = ref(1);
  const page_size = ref(25);
  const store = props.triggerStore ? props.triggerStore() : undefined;
  
  onBeforeMount(async () => {
    await getData();
  });
  
  async function getData() {
    loading.value = true;
    try {
      const toSend = { 
        page: current_page.value, 
        toGet: page_size.value, 
        patient_id: props.patient_id 
      };
      
      if (props.hasFilters === true && props.filters) {
        Object.assign(toSend, props.filters);
      }
  
      console.log('WaitingRoomVertical - sending:', toSend);
      
      const result = await props.client.getAll(toSend);
      
      if (result && result.data) {
        patients.value = result.data;
        current_page.value = result.current_page || 1;
        page_size.value = result.per_page || 25;
      } else if (Array.isArray(result)) {
        patients.value = result;
      } else {
        patients.value = [];
      }
      
      console.log('WaitingRoomVertical - patients loaded:', patients.value.length);
    } catch (error) {
      console.error('Error fetching patients in WaitingRoomVertical:', error);
      patients.value = [];
    } finally {
      loading.value = false;
    }
  }
  
  function handlePatientClick(patient: any) {
    console.log('Patient clicked:', patient);
  }
  
  function handleImageError(event: Event) {
    const img = event.target as HTMLImageElement;
    const fallback = img.nextElementSibling as HTMLElement;
    img.style.display = 'none';
    if (fallback) {
      fallback.style.display = 'flex';
    }
  }
  
  function getInitials(surname: string, name: string) {
    const firstInitial = surname ? surname.charAt(0).toUpperCase() : '';
    const lastInitial = name ? name.charAt(0).toUpperCase() : '';
    return firstInitial + lastInitial;
  }
  
  function formatPhone(phone: string) {
    if (!phone) return '';
    // Simple French phone formatting
    return phone.replace(/(\d{2})(?=\d)/g, '$1 ');
  }
  
  function getStatusClass(status: string) {
    const statusClasses = {
      'salle attente': 'status-waiting',
      'en consultation': 'status-consulting',
      'terminé': 'status-completed',
      'annulé': 'status-cancelled',
      'reporté': 'status-postponed'
    };
    return statusClasses[status?.toLowerCase()] || 'status-default';
  }
  
  // Watch for store changes to refresh data
  if (store) {
    watch(store, async (newState) => {
      if (newState.trigger === true) {
        console.log('WaitingRoomVertical - store trigger detected, refreshing data');
        await getData();
        store.setTrigger(false);
      }
    }, { deep: true });
  }
  
  // Expose getData method for parent component
  defineExpose({
    getData
  });
  </script>
  
  <style scoped>
  /* Container size specific styles */
  .waiting-room-container {
    height: 100%;
    /* background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); */
    border-radius: 12px;
    overflow: hidden;
    min-width: 280px; /* Ensure minimum usable width */
  }
  
  .waiting-room-content {
    height: calc(100vh - 350px);
    padding: 1rem;
    overflow-y: auto;
    min-height: 200px; /* Ensure minimum height */
  }
  
  /* Compact layout for constrained containers */
  .waiting-room-container.compact {
    border-radius: 8px;
  }
  
  .waiting-room-container.compact .waiting-room-content {
    padding: 0.75rem;
  }
  
  .waiting-room-container.compact .patient-card {
    border-radius: 8px;
    margin-bottom: 0.5rem;
  }
  
  .waiting-room-container.compact .card-header {
    padding: 0.75rem;
    flex-wrap: nowrap;
    align-items: center;
  }
  
  .waiting-room-container.compact .patient-avatar {
    width: 40px;
    height: 40px;
  }
  
  .waiting-room-container.compact .patient-name {
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .waiting-room-container.compact .patient-type {
    font-size: 0.75rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .waiting-room-container.compact .time-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
  }
  
  .waiting-room-container.compact .patient-details {
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: 0.375rem;
    padding-bottom: 0.25rem;
  }
  
  .waiting-room-container.compact .detail-item {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    flex-shrink: 0;
  }
  
  .waiting-room-container.compact .card-actions {
    padding: 0.5rem 0.75rem;
    gap: 0.25rem;
  }
  
  .waiting-room-container.compact .action-btn {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
  }
  
  /* Loading State */
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #64748b;
  }
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
  }
  
  .loading-text {
    font-size: 0.95rem;
    font-weight: 500;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* Empty State */
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    color: #64748b;
  }
  
  .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: #94a3b8;
  }
  
  .empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
  }
  
  .empty-subtitle {
    font-size: 0.95rem;
    color: #64748b;
  }
  
  /* Patient List */
  .patients-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .patient-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    overflow: hidden;
  }
  
  .patient-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: #3b82f6;
  }
  
  /* Card Header */
  .card-header {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
  
  .patient-avatar {
    position: relative;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
  }
  
  .avatar-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    transition: border-color 0.2s;
  }
  
  .patient-card:hover .avatar-image {
    border-color: #3b82f6;
  }
  
  .avatar-fallback {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    display: none;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    border: 2px solid #e2e8f0;
  }
  
  .patient-info {
    flex: 1;
    min-width: 120px;
    overflow: hidden;
  }
  
  .patient-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
  }
  
  .patient-type {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
    line-height: 1.2;
  }
  
  .appointment-time {
    flex-shrink: 0;
    margin-left: auto;
  }
  
  .time-badge {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    white-space: nowrap;
  }
  
  /* Card Body */
  .card-body {
    padding: 0 1rem 1rem;
  }
  
  .patient-details {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
  }
  
  .detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
    white-space: nowrap;
    flex-shrink: 0;
  }
  
  .detail-label {
    color: #64748b;
    font-weight: 500;
  }
  
  .detail-value {
    color: #334155;
    font-weight: 600;
  }
  
  /* Status Badges */
  .status-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
  }
  
  .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
  }
  
  .status-waiting {
    background: #dbeafe;
    color: #1e40af;
  }
  
  .status-waiting .status-dot {
    background: #3b82f6;
  }
  
  .status-consulting {
    background: #d1fae5;
    color: #065f46;
  }
  
  .status-consulting .status-dot {
    background: #10b981;
  }
  
  .status-completed {
    background: #f3f4f6;
    color: #374151;
  }
  
  .status-completed .status-dot {
    background: #6b7280;
  }
  
  .status-cancelled {
    background: #fee2e2;
    color: #991b1b;
  }
  
  .status-cancelled .status-dot {
    background: #ef4444;
  }
  
  .status-postponed {
    background: #fef3c7;
    color: #92400e;
  }
  
  .status-postponed .status-dot {
    background: #f59e0b;
  }
  
  .status-default {
    background: #f1f5f9;
    color: #475569;
  }
  
  .status-default .status-dot {
    background: #64748b;
  }
  
  /* Card Actions */
  .card-actions {
    display: flex;
    gap: 0.375rem;
    padding: 0.75rem 1rem;
    border-top: 1px solid #f1f5f9;
    background: #fafafa;
    flex-wrap: wrap;
  }
  
  .action-btn {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    flex-shrink: 0;
  }
  
  .action-btn:hover {
    background: #f8fafc;
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
  }
  
  .action-icon {
    width: 14px;
    height: 14px;
  }
  
  /* Responsive Design */
  @media (max-width: 1024px) {
    .waiting-room-content {
      padding: 1rem;
    }
    
    .card-header {
      padding: 0.75rem;
    }
    
    .patient-name {
      font-size: 0.95rem;
    }
    
    .patient-details {
      gap: 0.375rem;
    }
    
    .detail-item {
      font-size: 0.7rem;
      padding: 0.25rem 0.5rem;
    }
    
    .time-badge {
      font-size: 0.7rem;
      padding: 0.25rem 0.5rem;
    }
  }
  
  @media (max-width: 768px) {
    .waiting-room-content {
      padding: 0.75rem;
    }
    
    .card-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
    
    .patient-info {
      width: 100%;
      min-width: unset;
    }
    
    .appointment-time {
      align-self: flex-end;
      margin-left: 0;
    }
    
    .patient-details {
      flex-direction: column;
      align-items: stretch;
      gap: 0.375rem;
    }
    
    .detail-item {
      justify-content: space-between;
      width: 100%;
    }
    
    .card-actions {
      padding: 0.5rem 0.75rem;
      gap: 0.25rem;
    }
    
    .action-btn {
      flex: 1;
      justify-content: center;
      min-width: 0;
      font-size: 0.7rem;
      padding: 0.375rem 0.5rem;
    }
  }
  
  /* Compact mode for very small containers */
  @media (max-width: 480px) {
    .patient-card {
      border-radius: 8px;
    }
    
    .card-header {
      padding: 0.5rem;
    }
    
    .patient-avatar {
      width: 40px;
      height: 40px;
    }
    
    .avatar-fallback {
      font-size: 0.8rem;
    }
    
    .patient-name {
      font-size: 0.9rem;
      line-height: 1.2;
    }
    
    .patient-type {
      font-size: 0.75rem;
    }
    
    .card-body {
      padding: 0 0.5rem 0.5rem;
    }
    
    .card-actions {
      padding: 0.5rem;
    }
  }
  
  /* Scrollbar Styling */
  .waiting-room-content::-webkit-scrollbar {
    width: 6px;
  }
  
  .waiting-room-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
  }
  
  .waiting-room-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }
  
  .waiting-room-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  </style>