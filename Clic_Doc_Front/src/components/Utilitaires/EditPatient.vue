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
		coverage_number:""
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
            <el-row>
                <el-col :span="6" class="text-center">
                    <div class="demo-image__preview">
                        <el-image
                        style="width: 200px; height: 250px"
                        :src="patient.avatar"
                        fit="cover"
                        />
                    </div>
                    <input type="file" id="file" hidden class="file-input file-input-bordered file-input-xs w-full max-w-xs" @change="async ()=>{await upload()}"/>
                    <label for="file" class="el-button el-button--primary" > Selectionner Image</label>
                </el-col>
                <el-col :span="18">
                    <el-form label-position="top" >
                        <el-row :gutter="10">
                            <el-col :span="6">
                                <el-form-item label="Civilité (*)">
                                    <el-select @change="getAvatar()" v-model="patient.sex" class="w-full" >
                                        <el-option label="Monsieur" :value="'M'" />
                                        <el-option label="Madame" :value="'F'" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="6">
                                <el-form-item label="Nom (*)" >
                                    <el-input v-model="patient.name" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="6">
                                <el-form-item label="Prénom(*)" >
                                    <el-input v-model="patient.surname" />
                                </el-form-item>
                            </el-col>
							<el-col :span="6">
                                <el-form-item label="Diabétique ?" >
                                    <el-select class="w-full" v-model="patient.diabetes">
                                        <el-option :value="0" label="Non" />
                                        <el-option :value="3" label="Prédiabètes" />
                                        <el-option :value="1" label="Type 1" />
                                        <el-option :value="2" label="Type 2" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Date de naissance">
                                    <el-date-picker
                                        v-model="patient.date_of_birth"
                                        type="date"
                                        placeholder="Cliquez pour selectionner"
                                        format="DD/MM/YYYY"
                                        value-format="DD/MM/YYYY"
                                        class="w-full"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="CIN" >
                                    <el-input v-model="patient.CIN" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Téléphone (*)" >
                                    <el-input v-model="patient.phone" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="6">
                                <el-form-item label="Mutuelle ?" >
                                    <el-switch
                                        v-model="patient.coverage"
                                        size="large"
                                        active-text="Oui"
                                        inactive-text="Non"
                                        :active-value="1"
                                        :inactive-value="0"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="6">
                                <el-form-item label="Type mutuelle" >
                                    <el-input :disabled="!patient.coverage" v-model="patient.coverage_type" />
                                </el-form-item>
                            </el-col>
							<el-col :span="6">
                                <el-form-item label="Immatriculation" >
                                    <el-input :disabled="!patient.coverage" v-model="patient.coverage_number" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="6">
                                <el-form-item label="Groupe Sanguin" >
                                    <el-input  v-model="patient.blood_type" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <div class="text-right">
                            <div class="flex items-center justify-end space-x-4 ">
                                    <!-- Delete Button -->
                                    <button 
                                        class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95" 
                                        type="button" 
                                        @click="deletePatient()"
                                    >
                                        <svg class="w-4 h-4 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Supprimer
                                    </button>

                                    <!-- Save Button -->
                                    <button 
    class="group relative inline-flex items-center justify-center px-8 py-3 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg" 
    style="background: linear-gradient(135deg, #0092C5 0%, #00AAD8 100%); 
           --tw-ring-color: #0092C5;"
    onmouseover="this.style.background='linear-gradient(135deg, #007BA3 0%, #0088B6 100%)'"
    onmouseout="this.style.background='linear-gradient(135deg, #0092C5 0%, #00AAD8 100%)'"
    type="button" 
    @click="add()"
>
    <svg class="w-4 h-4 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    Enregistrer
</button>
                                </div>
                        </div>
                    </el-form>
                </el-col>
            </el-row>
        </div>
    </el-dialog>
</template>