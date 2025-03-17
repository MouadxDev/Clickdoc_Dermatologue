<script setup lang="ts">
    import { useUtilStore } from '../../../core/Data/stores/utilitaire';
    import { usePatientStore } from '../../../core/Data/stores/patient'
    import { reactive,ref } from 'vue';

    import { Patients } from '../../../core/Clients/Patients';
    import { Upload } from '../../../core/Clients/Upload';


    const patientClient = new Patients();
    const uploadClient = new Upload();
    const util = useUtilStore();
    const store = usePatientStore();

    const patient = reactive({
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
            patient.sex=="M"?patient.avatar="/avatar-m.png":patient.avatar="/avatar-f.jpg"
        }
    }

    async function upload() {
        var formData = new FormData();
        var file = document.querySelector('input[type=file]').files[0];
        formData.append("file",file);
        const resp = await uploadClient.add(formData)
        isUploaded.value=true
        patient.avatar=resp.data.full_path
    }

    async function add() {
        const resp = await patientClient.add(patient)
        store.setTrigger(true)
        
        if(resp.status = 200)
        {
            patient.value={
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
            }
            util.setPatient(false)
        }
        
    }
</script>

<template>
    <el-dialog width="950px" title="Nouveau Patient" v-model="util.Patient">
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
							
                            <button class="btn background-clickdoc" type="button" @click="add()" >
                                Enregistrer
                            </button>
                        </div>
                    </el-form>
                </el-col>
            </el-row>
        </div>
    </el-dialog>
</template>