<script setup lang="ts">
import { Ref, computed, onBeforeMount, reactive, ref } from 'vue';
import { Consultation } from '../../../core/Clients/Consult';
import { useConsultStore } from '../../../core/Data/stores/consultation';

const client = new Consultation();
const consult = useConsultStore();
const consultation: Ref<any> = ref({
  motif: [],
  isPrivate: false,
  isFinished: false,
});

const liste_motifs = reactive([
  { label: 'Autres', value: 'Autres' },
  { label: 'Acné persistante ou sévère', value: 'Acné persistante ou sévère' },
  { label: 'Démangeaisons cutanées inexpliquées.', value: 'Démangeaisons cutanées inexpliquées.' },
  { label: 'Éruptions cutanées soudaines ou fréquentes.', value: 'Éruptions cutanées soudaines ou fréquentes.' },
  { label: 'Changements dans la couleur, la taille ou la forme des grains de beauté.', value: 'Changements dans la couleur, la taille ou la forme des grains de beauté.' },
  { label: 'Taches sombres ou taches de vieillesse.', value: 'Taches sombres ou taches de vieillesse.' },
  { label: 'Rougeurs, chaleur ou sensibilité cutanée anormales.', value: 'Rougeurs, chaleur ou sensibilité cutanée anormales.' },
  { label: 'Perte de cheveux excessive ou amincissement des cheveux.', value: 'Perte de cheveux excessive ou amincissement des cheveux.' },
  { label: 'Ongles anormaux, cassants ou décolorés.', value: 'Ongles anormaux, cassants ou décolorés.' },
  { label: 'Infection cutanée, abcès ou furoncle.', value: 'Infection cutanée, abcès ou furoncle.' },
  { label: 'Verrues persistantes ou verrues génitales.', value: 'Verrues persistantes ou verrues génitales.' },
  { label: 'Présence de boutons, de nodules ou de kystes sous la peau.', value: 'Présence de boutons, de nodules ou de kystes sous la peau.' },
  { label: 'Peau sèche, rugueuse ou squameuse.', value: 'Peau sèche, rugueuse ou squameuse.' },
  { label: 'Cicatrices', value: 'Cicatrices' },
  { label: 'Rougeurs et irritations', value: 'Rougeurs et irritations' },
  { label: 'Problèmes de pigmentation', value: 'Problèmes de pigmentation' },
  { label: 'Allergies cutanées ou réactions cutanées aux produits cosmétiques.', value: 'Allergies cutanées ou réactions cutanées aux produits cosmétiques.' },
  { label: 'Mauvaises réactions aux médicaments topiques.', value: 'Mauvaises réactions aux médicaments topiques.' },
  { label: 'Taches blanches sur la peau.', value: 'Taches blanches sur la peau.' },
  { label: 'Problèmes pédiatrique.', value: 'Problèmes pédiatrique.' },
  { label :"Acné " , value :"Acné"},
  { label :"Acrochordons (verrues filiformes) " , value :"Acrochordons (verrues filiformes)"},
  { label :"Albinisme " , value :"Albinisme"},
  { label :"Allergie cutanée " , value :"Allergie cutanée"},
  { label :"Angiome " , value :"Angiome"},
  { label :"Angiome stellaire " , value :"Angiome stellaire"},
  { label :"Anthrax " , value :"Anthrax"},
  { label :"Aphtes " , value :"Aphtes"},
  { label :"Atrophie cutanée " , value :"Atrophie cutanée"},
  { label :"Brûlures " , value :"Brûlures"},
  { label :"Carcinome basocellulaire " , value :"Carcinome basocellulaire"},
  { label :"Carcinome épidermoïde " , value :"Carcinome épidermoïde"},
  { label :"Chéloïdes " , value :"Chéloïdes"},
  { label :"Chéilite " , value :"Chéilite"},
  { label :"Cicatrices hypertrophiques " , value :"Cicatrices hypertrophiques"},
  { label :"Cloques " , value :"Cloques"},
  { label :"Couperose " , value :"Couperose"},
  { label :"Crevasses " , value :"Crevasses"},
  { label :"Dermatite atopique " , value :"Dermatite atopique"},
  { label :"Dermatite de contact " , value :"Dermatite de contact"},
  { label :"Dermatite herpétiforme " , value :"Dermatite herpétiforme"},
  { label :"Dermatite périorale " , value :"Dermatite périorale"},
  { label :"Dermatite séborrhéique " , value :"Dermatite séborrhéique"},
  { label :"Dermatophytose (teigne) " , value :"Dermatophytose (teigne)"},
  { label :"Dermographisme " , value :"Dermographisme"},
  { label :"Dyschromies (taches pigmentaires) " , value :"Dyschromies (taches pigmentaires)"},
  { label :"Eczéma " , value :"Eczéma"},
  { label :"Érythème noueux " , value :"Érythème noueux"},
  { label :"Érythème polymorphe " , value :"Érythème polymorphe"},
  { label :"Érythème solaire " , value :"Érythème solaire"},
  { label :"Éruption médicamenteuse " , value :"Éruption médicamenteuse"},
  { label :"Escarres " , value :"Escarres"},
  { label :"Fibrome " , value :"Fibrome"},
  { label :"Folliculite " , value :"Folliculite"},
  { label :"Gale " , value :"Gale"},
  { label :"Granulome annulaire " , value :"Granulome annulaire"},
  { label :"Hémangiome " , value :"Hémangiome"},
  { label :"Herpès labial " , value :"Herpès labial"},
  { label :"Herpès génital " , value :"Herpès génital"},
  { label :"Hidrosadénite suppurée " , value :"Hidrosadénite suppurée"},
  { label :"Hyperhidrose " , value :"Hyperhidrose"},
  { label :"Hypersensibilité cutanée " , value :"Hypersensibilité cutanée"},
  { label :"Intertrigo " , value :"Intertrigo"},
  { label :"Kératose actinique " , value :"Kératose actinique"},
  { label :"Kératose séborrhéique " , value :"Kératose séborrhéique"},
  { label :"Kératodermie palmo-plantaire " , value :"Kératodermie palmo-plantaire"},
  { label :"Kystes épidermoïdes " , value :"Kystes épidermoïdes"},
  { label :"Kystes sébacés " , value :"Kystes sébacés"},
  { label :"Lichen plan " , value :"Lichen plan"},
  { label :"Lichen scléreux " , value :"Lichen scléreux"},
  { label :"Lupus cutané " , value :"Lupus cutané"},
  { label :"Mélanome " , value :"Mélanome"},
  { label :"Milium " , value :"Milium"},
  { label :"Molluscum contagiosum " , value :"Molluscum contagiosum"},
  { label :"Mycose cutanée " , value :"Mycose cutanée"},
  { label :"Naevus (grain de beauté) " , value :"Naevus (grain de beauté)"},
  { label :"Panaris " , value :"Panaris"},
  { label :"Papillomatose " , value :"Papillomatose"},
  { label :"Pemphigus " , value :"Pemphigus"},
  { label :"Pemphigoïde bulleuse " , value :"Pemphigoïde bulleuse"},
  { label :"Pétéchies " , value :"Pétéchies"},
  { label :"Photodermatose " , value :"Photodermatose"},
  { label :"Phytophotodermatose " , value :"Phytophotodermatose"},
  { label :"Pityriasis rosé de Gibert " , value :"Pityriasis rosé de Gibert"},
  { label :"Pityriasis versicolor " , value :"Pityriasis versicolor"},
  { label :"Prurit " , value :"Prurit"},
  { label :"Psoriasis " , value :"Psoriasis"},
  { label :"Purpura " , value :"Purpura"},
  { label :"Pyodermite " , value :"Pyodermite"},
  { label :"Rosacée " , value :"Rosacée"},
  { label :"Sarcome de Kaposi " , value :"Sarcome de Kaposi"},
  { label :"Sclérodermie " , value :"Sclérodermie"},
  { label :"Squames " , value :"Squames"},
  { label :"Télangiectasies " , value :"Télangiectasies"},
  { label :"Toxidermie " , value :"Toxidermie"},
  { label :"Tumeurs cutanées bénignes " , value :"Tumeurs cutanées bénignes"},
  { label :"Tumeurs cutanées malignes " , value :"Tumeurs cutanées malignes"},
  { label :"Urticaire " , value :"Urticaire"},
  { label :"Urticaire cholinergique " , value :"Urticaire cholinergique"},
  { label :"Verrues " , value :"Verrues"},
  { label :"Verrues génitales " , value :"Verrues génitales"},
  { label :"Vitiligo " , value :"Vitiligo"},
  { label :"Xanthome " , value :"Xanthome"},
  { label :"Xanthélasma " , value :"Xanthélasma"},
  { label :"Xérose cutanée " , value :"Xérose cutanée"},
  { label :"Alopécie androgénétique " , value :"Alopécie androgénétique"},
  { label :"Alopécie areata (pelade) " , value :"Alopécie areata (pelade)"},
  { label :"Chromonychie (coloration anormale des ongles) " , value :"Chromonychie (coloration anormale des ongles)"},
  { label :"Dystrophie unguéale " , value :"Dystrophie unguéale"},
  { label :"Hématome sous-unguéal " , value :"Hématome sous-unguéal"},
  { label :"Hippocratisme digital " , value :"Hippocratisme digital"},
  { label :"Koïlonychie (ongles en cuillère) " , value :"Koïlonychie (ongles en cuillère)"},
  { label :"Leuconychie (taches blanches sur les ongles) " , value :"Leuconychie (taches blanches sur les ongles)"},
  { label :"Lignes de Beau " , value :"Lignes de Beau"},
  { label :"Mélanonychie (tache brune/noire sur l’ongle) " , value :"Mélanonychie (tache brune/noire sur l’ongle)"},
  { label :"Mycose des ongles (onychomycose) " , value :"Mycose des ongles (onychomycose)"},
  { label :"Onychatrophie " , value :"Onychatrophie"},
  { label :"Onycholyse (décollement de l’ongle) " , value :"Onycholyse (décollement de l’ongle)"},
  { label :"Onychomadèse (chute soudaine de l’ongle) " , value :"Onychomadèse (chute soudaine de l’ongle)"},
  { label :"Onychophagie (rongement des ongles) " , value :"Onychophagie (rongement des ongles)"},
  { label :"Onychorrhexie (ongles fragiles et cassants) " , value :"Onychorrhexie (ongles fragiles et cassants)"},
  { label :"Pachyonychie " , value :"Pachyonychie"},
  { label :"Panaris " , value :"Panaris"},
  { label :"Psoriasis unguéal " , value :"Psoriasis unguéal"},
  { label :"Trachyonychie (ongles rugueux)Alopécie cicatricielle " , value :"Trachyonychie (ongles rugueux)Alopécie cicatricielle"},
  { label :"Alopécie diffuse " , value :"Alopécie diffuse"},
  { label :"Cheveux cassants " , value :"Cheveux cassants"},
  { label :"Cheveux gras " , value :"Cheveux gras"},
  { label :"Cheveux secs " , value :"Cheveux secs"},
  { label :"Dermite séborrhéique du cuir chevelu " , value :"Dermite séborrhéique du cuir chevelu"},
  { label :"Effluvium télogène " , value :"Effluvium télogène"},
  { label :"Folliculite du cuir chevelu " , value :"Folliculite du cuir chevelu"},
  { label :"Lichen plan pilaire " , value :"Lichen plan pilaire"},
  { label :"Mycose du cuir chevelu (teigne du cuir chevelu) " , value :"Mycose du cuir chevelu (teigne du cuir chevelu)"},
  { label :"Pityriasis amiantacea " , value :"Pityriasis amiantacea"},
  { label :"Psoriasis du cuir chevelu " , value :"Psoriasis du cuir chevelu"},
  { label :"Teigne (dermatophytose du cuir chevelu) " , value :"Teigne (dermatophytose du cuir chevelu)"},
  { label :"Trichotillomanie " , value :"Trichotillomanie"},  
]);


const more: Ref<any> = ref('');

const filteredMotifs = computed(() => {
  return liste_motifs.filter(option => 
    option.label.toLowerCase().includes(more.value.toLowerCase()) || 
    option.value.toLowerCase().includes(more.value.toLowerCase())
  );
});

async function addMore() {
  consultation.value.motif.push(more.value);
  liste_motifs.push({ label: more.value, value: more.value });
  var index = consultation.value.motif.indexOf('Autres');
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
  await client.update({ id: consult.consult, motif: JSON.stringify(consultation.value.motif), isFinished: consultation.value.isFinished, isPrivate: consultation.value.isPrivate });
  consultation.value = await getConsultation();
  consultation.value.motif = JSON.parse(consultation.value.motif);
}

onBeforeMount(async () => {
  consultation.value = await getConsultation();
  consultation.value.motif = JSON.parse(consultation.value.motif);
  var result: any = [];
  for (var i = 0; i < consultation.value.motif.length; i++) {
    result = liste_motifs.filter((word) => word.value == consultation.value.motif[i]);
    if (result.length == 0) {
      liste_motifs.push({ label: consultation.value.motif[i], value: consultation.value.motif[i] });
    }
    console.log(result);
  }
});
</script>



<template>
    <div class="container">
        <el-form label-position="top">
            <el-form-item label="Motifs">
                <el-select-v2
                    v-model="consultation.motif"
                    :options="filteredMotifs"
                    placeholder="Selectionner"
                    multiple
                    class="w-full"
                    allow-create
                    clearable
                    filterable
                    @change="async ()=>{await setConsult()}"
                    :disabled="!consult.edit"
                />
            </el-form-item>
            <!-- Add custom input for 'Autres' -->
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
