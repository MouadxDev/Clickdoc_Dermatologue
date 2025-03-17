<script setup lang="ts">
import { Ref, ref, onBeforeMount } from 'vue';
import { Observation } from '../../../core/Clients/Observation';
import { useConsultStore } from "../../../core/Data/stores/consultation";
import UiVocal from '../Ui/ui-voice.vue'; // Import the UiVocal component

const observation: Ref<any> = ref({});
const consult = useConsultStore();
const observationClient = new Observation();

async function setObservation() {
    await observationClient.update(observation.value);
    observation.value = await getObservation();
}

async function getObservation() {
    return await observationClient.getByID(consult.observation_id);
}

onBeforeMount(async () => {
    observation.value = await getObservation();
});
</script>

<template>
  <el-form label-position="top">
    <el-form-item label="Observation">
      <el-input 
        type="textarea" 
        :disabled="!consult.edit" 
        v-model="observation.observation" 
        @change="async () => { await setObservation() }" 
        :autosize="{ minRows: 10, maxRows: 20 }" 
      />
      <div class="icon_container">
        <!-- Integrate UiVocal and listen to transcription event -->
        <UiVocal @transcription="observation.observation = $event" />
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
