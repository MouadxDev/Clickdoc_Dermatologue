<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { ElLoading, ElMessage } from 'element-plus';
import { useAuthStore } from '../../core/Data/stores/auth'; 
import ENV from '../../core/env';

const authStore = useAuthStore();

const props = defineProps({
    patient: {
        type: Object,
        required: true
    }
});

// Document related state
const showDocumentForm = ref(false);
const selectedDocument = ref('');
const documentForm = ref({});
const showDocumentPreview = ref(false);
const documentPreviewUrl = ref('');

// Virtual keyboard state
const showVirtualKeyboard = ref(false);
const currentInputField = ref(null);
const keyboardInputValue = ref('');


const documents = ref([
{
  "key": "at-wafa",
  "label": "AT WAFA",
  "filename": "AT WAFA.docx",
  "fields": [
    { "name": "date_accident", "label": "Date d'accident", "type": "date", "required": true },
    { "name": "lieu_accident", "label": "Lieu d'accident", "type": "text", "required": true },
    { "name": "description", "label": "Description de l'accident", "type": "textarea", "required": false },

    { "name": "date_examen", "label": "Date d'examen", "type": "date", "required": true },

    { "name": "date_certificat_initial", "label": "Date certificat initial", "type": "date", "required": true },
    { "name": "medecin_initial", "label": "Médecin certificat initial", "type": "text", "required": true },
    { "name": "itt", "label": "ITT (jours)", "type": "number", "required": true },

    { "name": "date_consolidation", "label": "Date certificat de consolidation", "type": "date", "required": true },
    { "name": "medecin_consolidation", "label": "Médecin certificat de consolidation", "type": "text", "required": true },
    { "name": "ipp", "label": "IPP (%)", "type": "number", "required": true },

    { "name": "date_consolidation_finale", "label": "Date de consolidation finale", "type": "date", "required": true },

    { "name": "examen_clinique", "label": "Examen clinique actuel", "type": "textarea", "required": true }
  ]
},  
{
  key: "avp-cie",
  label: "AVP CIE",
  filename: "AVP_CIE.docx",
        fields: [
        { name: 'date_examen', label: 'Date d\'examen', type: 'date', required: true },
        { name: 'numero_dossier', label: 'N° de dossier', type: 'text', required: true },
        { name: 'date_jugement', label: 'Date du jugement', type: 'date', required: true },
        { name: 'date_accident', label: 'Date de l\'AVP', type: 'date', required: true },
        { name: 'compagnie', label: 'Compagnie d\'assurance', type: 'text', required: true },
        { name: 'date_certificat_initial', label: 'Date certificat initial', type: 'date' },
        { name: 'medecin_initial', label: 'Médecin initial', type: 'text' },
        { name: 'notes', label: 'Notes', type: 'textarea', multiple: true },
        { name: 'date_consolidation', label: 'Date de consolidation', type: 'date' },
        { name: 'medecin_consolidation', label: 'Médecin consolidation', type: 'text' },
        { name: 'ipp', label: 'IPP (%)', type: 'number' },
        { name: 'examen_clinique', label: 'Examen clinique', type: 'textarea', multiple: true },
        { name: 'itt', label: 'ITT', type: 'number' },
        { name: 'pretium_doloris', label: 'Pretium Doloris', type: 'text' },
        { name: 'prejudice_esthetique', label: 'Préjudice Esthétique', type: 'text' },
        { name: 'prejudice_professionnel', label: 'Préjudice Professionnel', type: 'text' }
        ]
},

{
  key: "certificat-medical-at",
  label: "Certificat Médical AT",
  filename: "certificat_medical_at.docx",
  fields: [
    { name: "date_accident", label: "Date d'accident", type: "date", required: true },
    { name: "assurance", label: "Assurance", type: "text", required: false },
    { name: "diagnostic", label: "Diagnostic", type: "textarea", required: false },
    { name: "ipp", label: "IPP (%)", type: "number", required: false },
    { name: "date_consolidation", label: "Date de consolidation", type: "date", required: false }
  ]
},
        { 
            key: "certificat-medical-aptitude-fr", 
            label: "Certificat d'Aptitude Physique",
            filename: "Certificat Aptitude Physique.docx",
            fields: [
                {
                    name: 'carte',
                    label: "Carte d'immatriculation",
                    type: 'text',
                    required: true
                }
            ]
        },
        {
        key: "certificat-arret-travail",
        label: "CERTIFICAT MEDICAL ARRET DE TRAVAIL",
        filename: "CERTIFICAT MEDICAL ARRET DE TRAVAIL.docx",
        fields: [
            { name: 'date_debut', label: 'Date de début', type: 'date', required: true },
            { name: 'date_fin', label: 'Date de fin', type: 'date', required: true }
        ]
        },
        { 
  key: "certificat-aptitude-en", 
  label: "CERTIFICAT APTITUDE ENGLISH", 
  filename: "CERTIFICATE APTITUDE ENGLISH.docx",
  fields: [
    { 
      name: 'fitness_type', 
      label: 'Fitness Type', 
      type: 'select', 
      options: ['Fit', 'Unfit', 'Fit with restrictions'], 
      required: true 
    },
    { 
      name: 'restrictions', 
      label: 'Restrictions', 
      type: 'textarea', 
      required: false 
    },
    {
      name: 'footer_note',
      label: 'Footer Note / Additional Text',
      type: 'textarea',
      required: false
    }
  ]
},

{
    key: "certificat-mariage-ar",
    label: "شهادة طبية للزواج",
    filename: "شهادة طبية للزواج.docx",
    fields: [
        { name: 'patient_name_ar', label: 'اسم المعنية بالأمر', type: 'text', required: true, arabic: true },
        { name: 'date_exam', label: 'تاريخ الفحص', type: 'date', required: true }
    ]
},
        { 
            key: "lettre-assurance", 
            label: "LETTRE A Cie D'ASSURANCE", 
            filename: "LETTRE A Cie D'ASSURANCE.dotx.docx",
            fields: [
                { name: 'patient_name', label: 'Nom du patient', type: 'text', required: true },
                { name: 'dossier_cour', label: "Dossier Cour d'Appel", type: 'text', required: true },
                { name: 'dossier_tpi', label: 'Dossier TPI Casablanca', type: 'text', required: true },
                { name: 'date_jugement', label: 'Date du jugement', type: 'date', required: true },
                { name: 'date_avp', label: 'Date AVP', type: 'date', required: true },
                { name: 'objet_expertise', label: "Objet de l'expertise", type: 'text', required: true },
                { name: 'date_expertise', label: 'Date expertise', type: 'date', required: true },
                { name: 'heure', label: 'Heure (ex: 11h00)', type: 'text', required: true }
            ]
        },
        { 
            key: "lettre-avocat", 
            label: "LETTRE AVOCAT CIE D'ASSURANCE", 
            filename: "LETTRE AVOCAT CIE D'ASSURANCE.docx",
            fields: [
                { name: 'nom_cie', label: "Nom de la compagnie d'assurance", type: 'text', required: true },
                { name: 'numero_dossier', label: 'Numéro de dossier', type: 'text', required: true },
                { name: 'date_jugement', label: 'Date du jugement', type: 'date', required: true },
                { name: 'date_avp', label: 'Date AVP', type: 'date', required: true },
                { name: 'objet_expertise', label: "Objet de l'expertise", type: 'text', required: true },
                { name: 'date_expertise', label: "Date de l'expertise", type: 'date', required: true },
                { name: 'heure', label: 'Heure (ex: 10h30)', type: 'text', required: true }
            ]
        },
        { 
            key: "certificat-avp-consolidation", 
            label: "MODELE CERTIFICAT AVP CONSOLIDATION", 
            filename: "MODELE CERTIFICAT AVP CONSOLIDATION.docx",
            fields: [
                {
                    name: 'observation',
                    label: 'Observations / Remarques',
                    type: 'textarea',
                    required: false
                }
            ]
        },
        { 
            key: "certificat-avp-initial", 
            label: "MODELE CERTIFICAT AVP INITIAL", 
            filename: "MODELE CERTIFICAT AVP INITIAL.dotx.docx",
            fields: [
                {
                    name: 'observation',
                    label: 'Observations / Remarques',
                    type: 'textarea',
                    required: false
                }
            ]
        },
        { 
            key: "insurance-letter-ar", 
            label: "رسالة لشركة تأمين", 
            filename: "رسالة_لشركة_تأمين.docx",
            fields: [
                { name: 'compagnie', label: 'شركة التأمين', type: 'text', required: true, arabic: true },
                { name: 'numero_dossier', label: 'رقم الملف', type: 'text', required: true, arabic: true },
                { name: 'date_jugement', label: 'تاريخ الحكم', type: 'date', required: true },
                { name: 'date_expertise', label: 'تاريخ الخبرة', type: 'date', required: true },
                { name: 'heure', label: 'الساعة', type: 'text', required: true, arabic: true },
                { name: 'minute', label: 'الدقيقة', type: 'text', required: true, arabic: true },
                { name: 'victime', label: 'اسم المعني/الضحية', type: 'text', required: true, arabic: true }
            ]
        },
        { 
            key: "medical-fitness-ar", 
            label: "شهادة طبية لياقة بدنية", 
            filename: "شهادة_طبية_لياقة_بدنية.docx",
            fields: [
                { name: 'cin', label: 'رقم بطاقة التعريف', type: 'text', required: true, arabic: true },
                { name: 'notes', label: 'ملاحظات إضافية', type: 'textarea', required: false, arabic: true }
            ]
        }
    ])

// Arabic keyboard layout
const arabicKeyboard = [
    ['ض', 'ص', 'ث', 'ق', 'ف', 'غ', 'ع', 'ه', 'خ', 'ح', 'ج', 'د'],
    ['ش', 'س', 'ي', 'ب', 'ل', 'ا', 'ت', 'ن', 'م', 'ك', 'ط'],
    ['ء', 'ئ', 'ؤ', 'ر', 'لا', 'ى', 'ة', 'و', 'ز', 'ظ'],
    ['مسافة']
];

const selectedDocumentFields = computed(() => {
    const doc = documents.value.find(d => d.key === selectedDocument.value);
    return doc ? doc.fields : [];
});

// Check if current document has Arabic fields
const hasArabicFields = computed(() => {
    return selectedDocumentFields.value.some(field => field.arabic);
});

function selectDocument(documentKey: string) {
    selectedDocument.value = documentKey;
    const doc = documents.value.find(d => d.key === documentKey);
    if (doc) {
        documentForm.value = {};
        doc.fields.forEach(field => {
            documentForm.value[field.name] = '';
        });
        showDocumentForm.value = true;
    }
}

// Virtual keyboard functions
function openVirtualKeyboard(fieldName: string, fieldType: string) {
    if (fieldType === 'date' || fieldType === 'number' || fieldType === 'select') return;
    
    currentInputField.value = fieldName;
    keyboardInputValue.value = documentForm.value[fieldName] || '';
    showVirtualKeyboard.value = true;
}

function insertArabicChar(char: string) {
    if (char === 'مسافة') {
        keyboardInputValue.value += ' ';
    } else {
        keyboardInputValue.value += char;
    }
}

function deleteLastChar() {
    keyboardInputValue.value = keyboardInputValue.value.slice(0, -1);
}

function clearKeyboardInput() {
    keyboardInputValue.value = '';
}

function applyKeyboardInput() {
    if (currentInputField.value) {
        documentForm.value[currentInputField.value] = keyboardInputValue.value;
    }
    closeVirtualKeyboard();
}

function closeVirtualKeyboard() {
    showVirtualKeyboard.value = false;
    currentInputField.value = null;
    keyboardInputValue.value = '';
}

async function generateDocument() {
    try {
        const loading = ElLoading.service({
            lock: true,
            text: 'Génération du document...',
            background: 'rgba(0, 0, 0, 0.7)',
        });

        // Build URL parameters from form data
        const urlParams = new URLSearchParams();
        urlParams.append('patient_uid', props.patient.uid);
        urlParams.append('doctor_id', authStore.user.id);
        urlParams.append('auto_print', 'true');
        
        // Add all form data as URL parameters
        Object.keys(documentForm.value).forEach(key => {
            if (documentForm.value[key]) {
                urlParams.append(key, documentForm.value[key]);
            }
        });

        // Generate the document URL with all parameters
        documentPreviewUrl.value = `${ENV.VITE_BACKEND}/documents/${selectedDocument.value}?${urlParams.toString()}`;
        
        showDocumentForm.value = false;
        showDocumentPreview.value = true;
        
        loading.close();
    } catch (error) {
        console.error('Error:', error);
        ElMessage.error('Erreur lors de la génération du document');
        loading.close();
    }
}

function closeDocumentPreview() {
    showDocumentPreview.value = false;
    documentPreviewUrl.value = '';
    selectedDocument.value = '';
    documentForm.value = {};
}
</script>

<template>
    <div>
        <!-- Documents Dropdown -->
        <el-dropdown @command="selectDocument">
            <el-button class="btn background-clickdoc">
                Documents <el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item 
                        v-for="doc in documents" 
                        :key="doc.key" 
                        :command="doc.key"
                    >
                        {{ doc.label }}
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>

        <!-- Document Form Dialog -->
        <el-dialog 
            :title="`Générer ${documents.find(d => d.key === selectedDocument)?.label || ''}`" 
            v-model="showDocumentForm"
            width="800px"
            :close-on-click-modal="false"
        >
            <el-form label-position="top" v-if="selectedDocument">
                <el-form-item 
                    v-for="field in selectedDocumentFields" 
                    :key="field.name"
                    :label="field.label"
                    :required="field.required"
                    :class="{ 'arabic-field': field.arabic }"
                >
                    <!-- Text Input with Arabic keyboard button -->
                    <div v-if="field.type === 'text'" class="input-with-keyboard">
                        <el-input 
                            v-model="documentForm[field.name]"
                            :placeholder="field.label"
                            :style="field.arabic ? 'direction: rtl; text-align: right;' : ''"
                        />
                        <el-button 
                            v-if="field.arabic"
                            @click="openVirtualKeyboard(field.name, field.type)"
                            size="small"
                            class="keyboard-btn"
                            type="primary"
                            plain
                        >
                            ع
                        </el-button>
                    </div>
                    
                    <!-- Number Input -->
                    <el-input-number 
                        v-else-if="field.type === 'number'" 
                        v-model="documentForm[field.name]"
                        :min="0"
                    />
                    
                    <!-- Date Input -->
                    <el-date-picker
                        v-else-if="field.type === 'date'"
                        v-model="documentForm[field.name]"
                        type="date"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                    
                    <!-- Textarea with Arabic keyboard button -->
                    <div v-else-if="field.type === 'textarea'" class="input-with-keyboard">
                        <el-input 
                            v-model="documentForm[field.name]"
                            type="textarea"
                            :rows="3"
                            :placeholder="field.label"
                            :style="field.arabic ? 'direction: rtl; text-align: right;' : ''"
                        />
                        <el-button 
                            v-if="field.arabic"
                            @click="openVirtualKeyboard(field.name, field.type)"
                            size="small"
                            class="keyboard-btn"
                            type="primary"
                            plain
                        >
                            ع
                        </el-button>
                    </div>
                    
                    <!-- Select -->
                    <el-select 
                        v-else-if="field.type === 'select'" 
                        v-model="documentForm[field.name]"
                        style="width: 100%"
                        :placeholder="`Sélectionner ${field.label.toLowerCase()}`"
                    >
                        <el-option
                            v-for="option in field.options"
                            :key="option"
                            :label="option"
                            :value="option"
                        />
                    </el-select>
                </el-form-item>
            </el-form>
            
            <!-- Virtual Arabic Keyboard -->
            <div v-if="showVirtualKeyboard && hasArabicFields" class="virtual-keyboard">
                <div class="keyboard-header">
                    <h4>لوحة المفاتيح العربية</h4>
                    <el-button @click="closeVirtualKeyboard" size="small" circle>
                        <el-icon><Close /></el-icon>
                    </el-button>
                </div>
                
                <div class="keyboard-input">
                    <el-input 
                        v-model="keyboardInputValue"
                        type="textarea"
                        :rows="2"
                        placeholder="اكتب هنا..."
                        style="direction: rtl; text-align: right;"
                        readonly
                    />
                </div>
                
                <div class="keyboard-keys">
                    <div v-for="(row, rowIndex) in arabicKeyboard" :key="rowIndex" class="keyboard-row">
                        <el-button 
                            v-for="key in row" 
                            :key="key"
                            @click="insertArabicChar(key)"
                            size="small"
                            class="keyboard-key"
                            :class="{ 'space-key': key === 'مسافة' }"
                        >
                            {{ key }}
                        </el-button>
                    </div>
                    
                    <div class="keyboard-controls">
                        <el-button @click="deleteLastChar" size="small" type="warning">
                            ⌫ حذف
                        </el-button>
                        <el-button @click="clearKeyboardInput" size="small" type="danger">
                            مسح الكل
                        </el-button>
                        <el-button @click="applyKeyboardInput" size="small" type="success">
                            تطبيق
                        </el-button>
                    </div>
                </div>
            </div>
            
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showDocumentForm = false">Annuler</el-button>
                    <el-button type="primary" @click="generateDocument">Générer le document</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- Document Preview Dialog -->
        <el-dialog 
            :title="`Document: ${documents.find(d => d.key === selectedDocument)?.label || ''}`"
            v-model="showDocumentPreview"
            width="90%"
            top="5vh"
            :close-on-click-modal="false"
        >
            <div style="height: 80vh; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <iframe 
                    v-if="documentPreviewUrl"
                    :src="documentPreviewUrl"
                    width="100%" 
                    height="100%"
                    frameborder="0"
                    style="border-radius: 8px;"
                ></iframe>
                <div v-else class="flex items-center justify-center h-full">
                    <el-icon class="is-loading" size="50"><Loading /></el-icon>
                    <span class="ml-2">Chargement du document...</span>
                </div>
            </div>
            
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="closeDocumentPreview">Fermer</el-button>
                    <el-button type="primary" @click="() => window.open(documentPreviewUrl, '_blank')">
                        <el-icon><Download /></el-icon>
                        Télécharger
                    </el-button>
                    <el-button type="success" @click="() => window.print()">
                        <el-icon><Printer /></el-icon>
                        Imprimer
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<style scoped>
.dialog-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.input-with-keyboard {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    flex-direction: row-reverse;
}

.input-with-keyboard .el-input {
    flex: 1;
}

.keyboard-btn {
    min-width: 40px;
    font-weight: bold;
    font-size: 16px;
    padding-bottom: 12px;
}

.virtual-keyboard {
    margin-top: 20px;
    padding: 16px;
    border: 1px solid #e4e7ed;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.keyboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.keyboard-header h4 {
    margin: 0;
    color: #409eff;
    font-size: 16px;
}

.keyboard-input {
    margin-bottom: 12px;
}

.keyboard-keys {
    direction: rtl;
}

.keyboard-row {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-bottom: 6px;
    direction: ltr;
}

.keyboard-key {
    min-width: 40px;
    height: 35px;
    font-size: 16px;
    font-weight: bold;
}

.space-key {
    min-width: 200px !important;
}

.keyboard-controls {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    direction: ltr;
}

.keyboard-controls .el-button {
    font-size: 14px;
}

/* RTL support for Arabic fields */
.el-input__inner[style*="direction: rtl"] {
    font-family: 'Amiri', 'Tahoma', 'Arial Unicode MS', sans-serif;
}

.el-textarea__inner[style*="direction: rtl"] {
    font-family: 'Amiri', 'Tahoma', 'Arial Unicode MS', sans-serif;
}

/* Arabic field labels alignment */
.arabic-field :deep(.el-form-item__label) {
    text-align: right !important;
    direction: rtl;
    font-family: 'Amiri', 'Tahoma', 'Arial Unicode MS', sans-serif;
    font-weight: bold;
}

/* Fix input width and modal size */

/* Make the input container take full width */
.input-with-keyboard {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    width: 100%; 
}

/* Make the input field take most of the space */
.input-with-keyboard .el-input {
    flex: 1;
    min-width: 0; /* Prevent flex item from overflowing */
}

/* Make the input inner element take full width */
.input-with-keyboard .el-input .el-input__inner,
.input-with-keyboard .el-input .el-textarea__inner {
    width: 100% !important;
}

/* Ensure keyboard button has fixed width */
.keyboard-btn {
    min-width: 40px;
    max-width: 40px; /* Prevent button from growing */
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0; /* Prevent button from shrinking */
}

/* Fix textarea container width */
.input-with-keyboard .el-textarea {
    flex: 1;
    min-width: 0;
}
</style>