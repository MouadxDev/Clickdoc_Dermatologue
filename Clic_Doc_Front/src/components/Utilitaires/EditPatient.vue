<script setup lang="ts">
    import { useUtilStore } from '../../../core/Data/stores/utilitaire';
    import { usePatientStore } from '../../../core/Data/stores/patient';
    import { ref,watch } from 'vue';

    import { Patients } from '../../../core/Clients/Patients';
    import { Upload } from '../../../core/Clients/Upload';


    const client = new Patients();
    const uploadClient = new Upload();
    const util = useUtilStore();
    const store = usePatientStore();

    const patient = ref({
        sex:"M",
        avatar:"/avatar-m.png",
        name:"",
        surname:"",
        diabetes:0,
        blood_type:"",
        date_of_birth:"",
        phone:"",
        CIN:"",
        coverage:false,
        coverage_type:"",
		coverage_number:"",
        civil_status: 0 // 0 = Célibataire, 1 = Marié(e)
    })
    const isUploaded=ref(false)

    function getAvatar() {
        if(isUploaded.value==false)
        {
            patient.value.sex=="M"?patient.value.avatar="/avatar-m.png":patient.value.avatar="/avatar-f.jpg"
        }
    }

    async function upload() {
        var formData = new FormData();
        const fileInput = document.querySelector('input[type=file]') as HTMLInputElement;
        if (fileInput && fileInput.files && fileInput.files[0]) {
            var file = fileInput.files[0];
            formData.append("file",file);
            const resp = await uploadClient.add(formData)
            isUploaded.value=true
            patient.value.avatar=resp.data.full_path
        }
    }

    async function add() {
        await client.update(patient.value)
        util.setEditPatient(false)
        store.setTrigger(true)
    }

    async function deletePatient() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
            await client.delete(util.patient_id)
            util.setEditPatient(false)
            store.setTrigger(true)
        }
    }

    watch(util,async ()=>{
        console.log("here")
        patient.value=await getPatient()
    },{deep:true})

    async function getPatient(){
        return await client.getByUID(util.patient_id)
    }
</script>

<template>
    <el-dialog width="950px" title="Modifier Patient" v-model="util.editPatient">
        <div>
            <el-row :gutter="20">
                <el-col :span="6">
                    <div class="text-center">
                        <div class="demo-image__preview mb-4">
                            <el-image
                                style="width: 200px; height: 250px; border-radius: 8px;"
                                :src="patient.avatar"
                                fit="cover"
                            />
                        </div>
                        <input 
                            type="file" 
                            id="file" 
                            hidden 
                            accept="image/*"
                            @change="async ()=>{await upload()}"
                        />
                        <label for="file" class="el-button el-button--primary">
                            Sélectionner Image
                        </label>
                    </div>
                </el-col>
                <el-col :span="18">
                    <el-form label-position="top" :model="patient">
                        <el-row :gutter="15">
                            <el-col :span="8">
                                <el-form-item label="Civilité (*)">
                                    <el-select 
                                        @change="getAvatar()" 
                                        v-model="patient.sex" 
                                        class="w-full"
                                        placeholder="Sélectionner"
                                    >
                                        <el-option label="Monsieur" value="M" />
                                        <el-option label="Madame" value="F" />
                                        <el-option label="Demoiselle" value="Mlle" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Nom (*)">
                                    <el-input 
                                        v-model="patient.name" 
                                        placeholder="Nom"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Prénom (*)">
                                    <el-input 
                                        v-model="patient.surname" 
                                        placeholder="Prénom"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="15">
                            <el-col :span="8">
                                <el-form-item label="État civil">
                                    <el-select 
                                        v-model="patient.civil_status" 
                                        class="w-full"
                                        placeholder="Sélectionner"
                                    >
                                        <el-option :value="0" label="Célibataire" />
                                        <el-option :value="1" label="Marié(e)" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Date de naissance">
                                    <el-date-picker
                                        v-model="patient.date_of_birth"
                                        type="date"
                                        placeholder="Cliquez pour sélectionner"
                                        format="DD/MM/YYYY"
                                        value-format="DD/MM/YYYY"
                                        class="w-full"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="CIN">
                                    <el-input 
                                        v-model="patient.CIN" 
                                        placeholder="CIN"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="15">
                            <el-col :span="8">
                                <el-form-item label="Téléphone (*)">
                                    <el-input 
                                        v-model="patient.phone" 
                                        placeholder="Téléphone"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Groupe Sanguin">
                                    <el-input 
                                        v-model="patient.blood_type" 
                                        placeholder="Groupe Sanguin"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Diabétique ?">
                                    <el-select 
                                        class="w-full" 
                                        v-model="patient.diabetes"
                                        placeholder="Sélectionner"
                                    >
                                        <el-option :value="0" label="Non" />
                                        <el-option :value="3" label="Prédiabètes" />
                                        <el-option :value="1" label="Type 1" />
                                        <el-option :value="2" label="Type 2" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="15">
                            <el-col :span="8">
                                <el-form-item label="Mutuelle ?">
                                    <el-switch
                                        v-model="patient.coverage"
                                        size="large"
                                        active-text="Oui"
                                        inactive-text="Non"
                                        :active-value="true"
                                        :inactive-value="false"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Type mutuelle">
                                    <el-input 
                                        :disabled="!patient.coverage" 
                                        v-model="patient.coverage_type" 
                                        placeholder="Type mutuelle"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Immatriculation">
                                    <el-input 
                                        :disabled="!patient.coverage" 
                                        v-model="patient.coverage_number" 
                                        placeholder="Numéro d'immatriculation"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <div class="text-right mt-4">
                            <div class="flex items-center justify-end space-x-4">
                                <!-- Delete Button -->
                                <el-button 
                                    type="danger" 
                                    @click="deletePatient()"
                                    size="large"
                                    :icon="Delete"
                                >
                                    Supprimer
                                </el-button>

                                <!-- Save Button -->
                                <el-button 
                                    type="primary" 
                                    @click="add()"
                                    size="large"
                                    :icon="Check"
                                >
                                    Enregistrer
                                </el-button>
                            </div>
                        </div>
                    </el-form>
                </el-col>
            </el-row>
        </div>
    </el-dialog>
</template>

<style scoped>
.demo-image__preview {
    margin-bottom: 16px;
}

.w-full {
    width: 100%;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.mt-4 {
    margin-top: 16px;
}

.mb-4 {
    margin-bottom: 16px;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.justify-end {
    justify-content: flex-end;
}

.space-x-4 > * + * {
    margin-left: 16px;
}
</style>