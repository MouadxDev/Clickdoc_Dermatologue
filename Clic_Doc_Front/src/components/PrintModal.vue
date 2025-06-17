<script setup lang="ts">
import { ref } from 'vue';

// Props definition
const props = defineProps<{
  title: string;
  url: string;
}>();

// Emits definition
const emit = defineEmits(['close']);

// Refs
const showModal = ref(false);
const printFrame = ref<HTMLIFrameElement | null>(null);
const isLoading = ref(false);

// Methods
function openModal() {
  showModal.value = true;
  isLoading.value = true; // Start loading when modal opens
}

function closeModal() {
  showModal.value = false;
  isLoading.value = false;
  emit('close');
}

function onIframeLoad() {
  isLoading.value = false; // Hide loading when iframe loads
}

function onIframeError() {
  isLoading.value = false; // Hide loading on error
  console.error("Failed to load iframe content");
}

function printIframe() {
  const iframe = document.getElementById('printIframe') as HTMLIFrameElement;

  if (!iframe) {
    console.error("Iframe not found.");
    return;
  }

  // Show loading during print preparation
  isLoading.value = true;

  // Reload the iframe by resetting its src
  const src = iframe.src;
  iframe.src = '';  // Clear src to force reload
  iframe.src = src;

  // Wait for the iframe to reload before printing
  iframe.onload = () => {
    isLoading.value = false; // Hide loading
    if (iframe.contentWindow) {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    } else {
      console.error("Iframe contentWindow not accessible.");
    }
  };
}

// Expose methods to parent component
defineExpose({
  openModal
});
</script>

<template>
  <div>
    <el-dialog v-model="showModal" :title="title" width="50%">
      <div style="display: flex; align-items: center; justify-content: center; width: 100%; position: relative;">
        <!-- Loading Spinner -->
        <div 
          v-if="isLoading" 
          class="loading-overlay"
        >
          <div class="loading-spinner">
            <div class="spinner"></div>
            <p class="loading-text">Chargement en cours...</p>
          </div>
        </div>

        <!-- Iframe -->
        <iframe
          id="printIframe"
          ref="printFrame"
          :src="url"
          style="width: 100%; height: 600px; border: none; max-width: 90%;"
          @load="onIframeLoad"
          @error="onIframeError"
        ></iframe>
      </div>

      <template #footer>
        <el-button @click="closeModal">Fermer</el-button>
        <el-button 
          type="primary" 
          @click="printIframe"
          
        >
          Imprimer / Actualiser
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<style scoped>
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 4px;
}

.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #409eff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  margin: 0;
  color: #606266;
  font-size: 14px;
  font-weight: 500;
}

/* Optional: Add a subtle fade-in animation for the iframe */
iframe {
  opacity: 1;
  transition: opacity 0.3s ease-in-out;
}

iframe[src=""] {
  opacity: 0;
}
</style>