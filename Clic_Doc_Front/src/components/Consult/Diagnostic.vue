<script setup lang="ts">
import { Ref, ref, onBeforeMount } from 'vue';
import { Diagnostic } from '../../../core/Clients/Diagnostic';
import { useConsultStore } from "../../../core/Data/stores/consultation"

// import UiVocal from './components/Ui/UiVocal.vue'; 
import UiVocal from '../Ui/ui-voice.vue';

const diagnostic: Ref<any> = ref({})
const consult = useConsultStore();
const diagnosticClient = new Diagnostic()

async function setDiagnostic() {
    await diagnosticClient.update(diagnostic.value)
    diagnostic.value = await getDiagnostic()
}

async function getDiagnostic() {
    return await diagnosticClient.getByID(consult.diagnostic_id)
}

onBeforeMount(async () => {
    diagnostic.value = await getDiagnostic()
})
</script>

<template>
  <el-form label-position="top">
    <el-form-item label="Diagnostic">
      <el-input 
        type="textarea" 
        :disabled="!consult.edit" 
        v-model="diagnostic.diagnostic"
        @change="async () => { await setDiagnostic() }" 
        :autosize="{ minRows: 10, maxRows: 20 }" 
      />
      <div class="icon_container">
        <!-- Listen to the transcription event and update diagnostic.diagnostic -->
        <UiVocal @transcription="diagnostic.diagnostic = $event" />
      </div>
    </el-form-item>
  </el-form>
</template>

<style>
.icon_container {
  display: flex;
  align-items: end;
  justify-content: end;
  width: 100%;
}
</style>
