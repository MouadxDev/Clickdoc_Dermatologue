<script lang="ts" setup>
import { useRouter } from 'vue-router';
import { Patients } from '../../../core/Clients/Patients';
import { useUtilStore } from '../../../core/Data/stores/utilitaire';
import { useAuthStore } from '../../../core/Data/stores/auth';
import { usePatientStore } from '../../../core/Data/stores/patient'; 
import ENV from '../../../core/env';

const patientClient = new Patients();
const router = useRouter()
const util = useUtilStore();
const auth = useAuthStore();

function show(n: any) {
    router.push("/dossiers/" + n)
}

const fields = [
    { prop: 'uid', label: 'Reference' },
    { prop: 'surname', label: 'Prénom' },
    { prop: 'name', label: 'Nom' },
    { prop: 'date_of_birth', label: 'Date de naissance' },
    { prop: 'phone', label: 'Télephone' }
]

const actions = [
    {
        icon: "View",
        text: "voir",
        action: (n: any) => { show(n.uid) }
    },
    {
        icon: "Edit",
        text: "Edit",
        action: (n: any) => {
            util.patient_id = n.uid;
            util.setEditPatient(true);
        }
    },
]

function handleExport() {
    const url = `${ENV.VITE_BACKEND}/patients`;
    window.open(url, '_blank');
}
</script>

<template>
    <main-layout>
        <div class="container mx-auto">
            <ui-table
                :is-main="true"
                :hasAvatar="true"
                :hasButton="true"
                :client="patientClient"
                :add="true"
                :export="true"
                :onExport="handleExport"
                :onAdd="() => { util.setPatient(true) }"
                :triggerStore="usePatientStore"
                title="Patients"
                :fields="fields"
                :actions="actions"
                :filter="true"
                :useSearchMethod="true"
                searchMethod="search"
            />
        </div>
    </main-layout>
</template>