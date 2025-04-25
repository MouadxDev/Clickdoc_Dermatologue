<script setup lang="ts">
import { Ref, onBeforeMount, ref } from 'vue';
import { Consultation } from '../../../core/Clients/Consult';
import { useConsultStore } from '../../../core/Data/stores/consultation';
import { ConstFiles } from '../../../core/Clients/ConstFiles'; // Your existing client

const client = new Consultation();
const constClient = new ConstFiles(); // Use your existing one
const consult = useConsultStore();

const consultation: Ref<any> = ref({
  motif: [],
  isPrivate: false,
  isFinished: false,
});

const liste_motifs = ref<{ label: string; value: string }[]>([]);
const more: Ref<any> = ref('');
const loadingMotifs = ref(false);

// API search when user types
const fetchMotifs = async (query: string) => {
  loadingMotifs.value = true;
  try {
    const response = await constClient.getAll(query, 'liste_motifs');
    liste_motifs.value = response.data.map((item: any) => ({
      label: item.label,
      value: item.label,
    }));
  } catch (e) {
    liste_motifs.value = [];
  } finally {
    loadingMotifs.value = false;
  }
};


async function addMore() {
  consultation.value.motif.push(more.value);
  liste_motifs.value.push({ label: more.value, value: more.value });
  const index = consultation.value.motif.indexOf('Autres');
  if (index !== -1) {
    consultation.value.motif.splice(index, 1);
  }
  await setConsult();
}

async function getConsultation() {
  const data = await client.getOne(consult.consult);
  return data.data.deets;
}

async function setConsult() {
  await client.update({
    id: consult.consult,
    motif: JSON.stringify(consultation.value.motif),
    isFinished: consultation.value.isFinished,
    isPrivate: consultation.value.isPrivate,
  });
  consultation.value = await getConsultation();
  consultation.value.motif = JSON.parse(consultation.value.motif);
}

onBeforeMount(async () => {
  consultation.value = await getConsultation();
  consultation.value.motif = JSON.parse(consultation.value.motif);
});
</script>
<template>
  <div class="container">
    <el-form label-position="top">
      <el-form-item label="Motifs">
        <el-select-v2
          v-model="consultation.motif"
          :options="liste_motifs"
          multiple
          clearable
          filterable
          remote
          reserve-keyword
          placeholder="Sélectionner"
          class="w-full"
          allow-create
          :remote-method="fetchMotifs"
          :loading="loadingMotifs"
          @change="async () => await setConsult()"
          :disabled="!consult.edit"
        />
      </el-form-item>

      <el-form-item v-if="consultation.motif.includes('Autres')">
        <el-input v-model="more" placeholder="Ajoutez un motif">
          <template #append>
            <el-button size="small" @click="addMore()">Ajouter</el-button>
          </template>
        </el-input>
      </el-form-item>
    </el-form>
  </div>
</template>
