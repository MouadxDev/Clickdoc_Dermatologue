<script setup lang="ts">
    import { Ref , onBeforeMount, ref } from "vue";
    import {useConsultStore} from "../../../core/Data/stores/consultation"
    import { ExamenPhysique } from '../../../core/Clients/Examen';

    const consult = useConsultStore();
    const examenClient = new ExamenPhysique()

    const data_visage = [
        {label:"Acné",value:"Acné"},
        {label:"Rosacée",value:"Rosacée"},
        {label:"Eczéma",value:"Eczéma"},
        {label:"Dermatite de contact",value:"Dermatite de contact"},
        {label:"Psoriasis Urticaire Verrues",value:"Psoriasis Urticaire Verrues"},
        {label:"Herpès labial",value:"Herpès labial"},
        {label:"Taches de vieillesse",value:"Taches de vieillesse"},
        {label:"Taches de rousseur",value:"Taches de rousseur"},
        {label:"Milium Angiome stellaire",value:"Milium Angiome stellaire"},
        {label:"Kératose actinique",value:"Kératose actinique"},
        {label:"Comédons",value:"Comédons"},
        {label:"Pétéchies",value:"Pétéchies"},
        {label:"Hyperpigmentation",value:"Hyperpigmentation"},
        {label:"Hypopigmentation",value:"Hypopigmentation"},
        {label:"Acné (comédons, papules, pustules, nodules)",value:"Acné (comédons, papules, pustules, nodules)" },
        {label:"Angiomes",value:"Angiomes" },
        {label:"Chéilite (lèvres sèches, fissurées, enflammées)",value:"Chéilite (lèvres sèches, fissurées, enflammées)" },
        {label:"Couperose (rougeurs, petits vaisseaux visibles)",value:"Couperose (rougeurs, petits vaisseaux visibles)" },
        {label:"Dépigmentation (taches blanches)",value:"Dépigmentation (taches blanches)" },
        {label:"Dyschromie (taches foncées ou claires)",value:"Dyschromie (taches foncées ou claires)" },
        {label:"Érythème facial (rougeurs diffuses)",value:"Érythème facial (rougeurs diffuses)" },
        {label:"Herpès labial (vésicules, croûtes)",value:"Herpès labial (vésicules, croûtes)" },
        {label:"Hyperpigmentation (mélasma, taches brunes)",value:"Hyperpigmentation (mélasma, taches brunes)" },
        {label:"Hypopigmentation (vitiligo, pityriasis alba)",value:"Hypopigmentation (vitiligo, pityriasis alba)" },
        {label:"Lésions croûteuses",value:"Lésions croûteuses" },
        {label:"Lichenification (épaississement de la peau)",value:"Lichenification (épaississement de la peau)" },
        {label:"Macules hypo/hyperpigmentées",value:"Macules hypo/hyperpigmentées" },
        {label:"Œdème facial",value:"Œdème facial" },
        {label:"Papules (petites bosses rouges)",value:"Papules (petites bosses rouges)" },
        {label:"Pétéchies (petites taches rouges non blanchissantes)",value:"Pétéchies (petites taches rouges non blanchissantes)" },
        {label:"Pityriasis rosé (plaques rosées)",value:"Pityriasis rosé (plaques rosées)" },
        {label:"Prurit facial (démangeaisons)",value:"Prurit facial (démangeaisons)" },
        {label:"Pustules (lésions remplies de pus)",value:"Pustules (lésions remplies de pus)" },
        {label:"Squames (peau qui pèle)",value:"Squames (peau qui pèle)" },
        {label:"Télangiectasies (petits vaisseaux dilatés)",value:"Télangiectasies (petits vaisseaux dilatés)" },
        {label:"Ulcérations cutanées",value:"Ulcérations cutanées" },
        {label:"Xanthélasma (plaques jaunâtres autour des yeux)",value:"Xanthélasma (plaques jaunâtres autour des yeux)" },
        {label:"Xérose cutanée (sécheresse cutanée)",value:"Xérose cutanée (sécheresse cutanée)" },

    ]
    const data_corps  = [
        {label:"Eczéma",value:"Eczéma"},
        {label:"Dermatite de contact",value:"Dermatite de contact"},
        {label:"Psoriasis",value:"Psoriasis"},
        {label:"Urticaire",value:"Urticaire"},
        {label:"Verrues",value:"Verrues"},
        {label:"Molluscum contagiosum",value:"Molluscum contagiosum"},
        {label:"Pityriasis rosé de Gibert",value:"Pityriasis rosé de Gibert"},
        {label:"Impétigo",value:"Impétigo"},
        {label:"Mycose de la peau (dermatophytose)",value:"Mycose de la peau (dermatophytose)"},
        {label:"Infections bactériennes de la peau",value:"Infections bactériennes de la peau"},
        {label:"Cellulite",value:"Cellulite"},
        {label:"Furoncles et anthrax",value:"Furoncles et anthrax"},
        {label:"Folliculite",value:"Folliculite"},
        {label:"Lipome",value:"Lipome"},
        {label:"Kystes épidermiques",value:"Kystes épidermiques"},
        {label:"Hidradénite suppurée",value:"Hidradénite suppurée"},
        {label:"Pétéchies",value:"Pétéchies"},
        {label:"Érythème polymorphe",value:"Érythème polymorphe"},
        {label:"Syndrome de Stevens-Johnson",value:"Syndrome de Stevens-Johnson"},
        {label:'Acrochordons (verrues filiformes)',value:'Acrochordons (verrues filiformes)'},
        {label:'Angiomes',value:'Angiomes'},
        {label:'Bulle (lésion remplie de liquide clair)',value:'Bulle (lésion remplie de liquide clair)'},
        {label:'Cicatrices hypertrophiques',value:'Cicatrices hypertrophiques'},
        {label:'Crevasses cutanées',value:'Crevasses cutanées'},
        {label:'Croûtes hémorragiques',value:'Croûtes hémorragiques'},
        {label:'Cyanose cutanée',value:'Cyanose cutanée'},
        {label:'Dépigmentation',value:'Dépigmentation'},
        {label:'Dermographisme (rougeurs après frottement)',value:'Dermographisme (rougeurs après frottement)'},
        {label:'Érosion cutanée',value:'Érosion cutanée'},
        {label:'Érythème généralisé',value:'Érythème généralisé'},
        {label:'Escarres (ulcérations sur zones d’appui)',value:'Escarres (ulcérations sur zones d’appui)'},
        {label:'Excoriations (lésions dues au grattage)',value:'Excoriations (lésions dues au grattage)'},
        {label:'Fissures cutanées',value:'Fissures cutanées'},
        {label:'Hématomes spontanés',value:'Hématomes spontanés'},
        {label:'Hyperhidrose (transpiration excessive)',value:'Hyperhidrose (transpiration excessive)'},
        {label:'Hyperkératose (épaississement de la peau)',value:'Hyperkératose (épaississement de la peau)'},
        {label:'Hyperséborrhée',value:'Hyperséborrhée'},
        {label:'Hypopigmentation (dépigmentation partielle)',value:'Hypopigmentation (dépigmentation partielle)'},
        {label:'Lésions maculaires (taches rouges, brunes, blanches)',value:'Lésions maculaires (taches rouges, brunes, blanches)'},
        {label:'Lésions nodulaires',value:'Lésions nodulaires'},
        {label:'Lichenification (épaississement de la peau avec stries)',value:'Lichenification (épaississement de la peau avec stries)'},
        {label:'Macules érythémateuses',value:'Macules érythémateuses'},
        {label:'Nodules sous-cutanés',value:'Nodules sous-cutanés'},
        {label:'Papules (petites bosses rouges)',value:'Papules (petites bosses rouges)'},
        {label:'Pétéchies (petites taches rouges)',value:'Pétéchies (petites taches rouges)'},
        {label:'Phlyctènes (ampoules)',value:'Phlyctènes (ampoules)'},
        {label:'Pigmentation irrégulière',value:'Pigmentation irrégulière'},
        {label:'Plaques érythémateuses',value:'Plaques érythémateuses'},
        {label:'Prurit généralisé ou localisé',value:'Prurit généralisé ou localisé'},
        {label:'Pustules (lésions purulentes)',value:'Pustules (lésions purulentes)'},
        {label:'Squames épaisses ou fines',value:'Squames épaisses ou fines'},
        {label:'Stries atrophiques (vergetures)',value:'Stries atrophiques (vergetures)'},
        {label:'Télangiectasies (dilatation des petits vaisseaux)',value:'Télangiectasies (dilatation des petits vaisseaux)'},
        {label:'Tumeurs cutanées bénignes ou malignes',value:'Tumeurs cutanées bénignes ou malignes'},
        {label:'Ulcérations cutanées',value:'Ulcérations cutanées'},
        {label:'Urticaire (papules rouges prurigineuses)',value:'Urticaire (papules rouges prurigineuses)'},
        {label:'Xanthomes (dépôts lipidiques jaunâtres)',value:'Xanthomes (dépôts lipidiques jaunâtres)'},
        {label:'Xérose cutanée (sécheresse extrême)',value:'Xérose cutanée (sécheresse extrême)'},

    ]
    const data_ongles = [
        {label:"Onychomycose",value:"Onychomycose"},
        {label:"Paronychie",value:"Paronychie"},
        {label:"Onycholyse",value:"Onycholyse"},
        {label:"Onychogryphose",value:"Onychogryphose"},
        {label:"Onychomadèse",value:"Onychomadèse"},
        {label:"Leuconychie",value:"Leuconychie"},
        {label:"Pachyonychie",value:"Pachyonychie"},
        {label:"Onychorrhexie",value:"Onychorrhexie"},
        {label:"Onychoschizie",value:"Onychoschizie"},
        {label:"Onychomadesis",value:"Onychomadesis"},
        {label:"Koïlonychie",value:"Koïlonychie"},
        {label:"Onychauxis",value:"Onychauxis"},
        {label:"Onychite",value:"Onychite"},
        {label:"Hématome sous-unguéal",value:"Hématome sous-unguéal"},
        {label:"Onychocryptose (ongle incarné)",value:"Onychocryptose (ongle incarné)"},
        {label:"Onychophagie (rongement des ongles)",value:"Onychophagie (rongement des ongles)"},
        {label:"Onychotillomanie (arrachement compulsif des ongles)",value:"Onychotillomanie (arrachement compulsif des ongles)"},
        {label:"Trou d'épingle",value:"Trou d'épingle"},
        {label:"Lignes de Beau Pitting",value:"Lignes de Beau Pitting"},
        {label:"Chromonychie (coloration anormale)",value:"Chromonychie (coloration anormale)"},
        {label:"Déformation de l’ongle",value:"Déformation de l’ongle"},
        {label:"Dystrophie unguéale (altération de la surface de l’ongle)",value:"Dystrophie unguéale (altération de la surface de l’ongle)"},
        {label:"Hématome sous-unguéal",value:"Hématome sous-unguéal"},
        {label:"Hippocratisme digital (ongles en verre de montre)",value:"Hippocratisme digital (ongles en verre de montre)"},
        {label:"Hyperkératose sous-unguéale",value:"Hyperkératose sous-unguéale"},
        {label:"Koïlonychie (ongles en cuillère)",value:"Koïlonychie (ongles en cuillère)"},
        {label:"Leuconychie (taches blanches)",value:"Leuconychie (taches blanches)"},
        {label:"Lignes de Beau (dépressions transversales)",value:"Lignes de Beau (dépressions transversales)"},
        {label:"Mélanonychie (stries pigmentées)",value:"Mélanonychie (stries pigmentées)"},
        {label:"Mycose des ongles (onychomycose)",value:"Mycose des ongles (onychomycose)"},
        {label:"Ongles cassants",value:"Ongles cassants"},
        {label:"Ongles fragiles",value:"Ongles fragiles"},
        {label:"Ongles striés",value:"Ongles striés"},
        {label:"Onycholyse (décollement de l’ongle)",value:"Onycholyse (décollement de l’ongle)"},
        {label:"Onychomadèse (chute soudaine de l’ongle)",value:"Onychomadèse (chute soudaine de l’ongle)"},
        {label:"Onychophagie (rongement des ongles)",value:"Onychophagie (rongement des ongles)"},
        {label:"Onychorrhexie (ongles fissurés)",value:"Onychorrhexie (ongles fissurés)"},
        {label:"Pachyonychie (épaississement anormal)",value:"Pachyonychie (épaississement anormal)"},
        {label:"Panaris (infection péri-unguéale)",value:"Panaris (infection péri-unguéale)"},
        {label:"Psoriasis unguéal",value:"Psoriasis unguéal"},
        {label:"Trachyonychie (surface rugueuse de l’ongle)",value:"Trachyonychie (surface rugueuse de l’ongle)"},


        ]
    const data_cheveux= [
        {label:"Alopécie",value:"Alopécie"},
        {label:"Pellicules (dermatite séborrhéique)",value:"Pellicules (dermatite séborrhéique)"},
        {label:"Calvitie (alopécie androgénétique)",value:"Calvitie (alopécie androgénétique)"},
        {label:"Alopécie areata",value:"Alopécie areata"},
        {label:"Traction alopécie",value:"Traction alopécie"},
        {label:"Perte de cheveux due au stress (effluvium télogène)",value:"Perte de cheveux due au stress (effluvium télogène)"},
        {label:"Perte de cheveux due à la chimiothérapie",value:"Perte de cheveux due à la chimiothérapie"},
        {label:"Alopécie cicatricielle",value:"Alopécie cicatricielle"},
        {label:"Poux de tête (pédiculose capitis)",value:"Poux de tête (pédiculose capitis)"},
        {label:"Trichotillomanie (arrachement compulsif des cheveux)",value:"Trichotillomanie (arrachement compulsif des cheveux)"},
        {label:"Perte de cheveux due à une carence nutritionnelle",value:"Perte de cheveux due à une carence nutritionnelle"},
        {label:"Alopécie de la couronne (effluvium anagène)",value:"Alopécie de la couronne (effluvium anagène)"},
        {label:"Perte de cheveux due à une maladie du cuir chevelu",value:"Perte de cheveux due à une maladie du cuir chevelu"},
        {label:"Hypertrichose",value:"Hypertrichose"},
        {label:"Perte de cheveux due à une maladie systémique",value:"Perte de cheveux due à une maladie systémique"},
        {label:"Perte de cheveux due à des médicaments",value:"Perte de cheveux due à des médicaments"},
        {label:"Perte de cheveux liée à l'âge",value:"Perte de cheveux liée à l'âge"},
        {label:"Cheveux cassants et fragiles",value:"Cheveux cassants et fragiles"},
        {label:"Chute de cheveux saisonnière",value:"Chute de cheveux saisonnière"},
        {label:"Cheveux gras (séborrhée)",value:"Cheveux gras (séborrhée)"},
        {label:'Alopécie (chute de cheveux localisée ou diffuse)',value:'Alopécie (chute de cheveux localisée ou diffuse)'},
        {label:'Cassure des cheveux',value:'Cassure des cheveux'},
        {label:'Croûtes sur le cuir chevelu',value:'Croûtes sur le cuir chevelu'},
        {label:'Dépilation localisée',value:'Dépilation localisée'},
        {label:'Démangeaisons du cuir chevelu',value:'Démangeaisons du cuir chevelu'},
        {label:'Desquamation (pellicules, psoriasis)',value:'Desquamation (pellicules, psoriasis)'},
        {label:'Douleurs au cuir chevelu',value:'Douleurs au cuir chevelu'},
        {label:'Effluvium télogène (perte diffuse de cheveux)',value:'Effluvium télogène (perte diffuse de cheveux)'},
        {label:'Érythème du cuir chevelu (rougeurs)',value:'Érythème du cuir chevelu (rougeurs)'},
        {label:'Follicules enflammés',value:'Follicules enflammés'},
        {label:'Hypertrichose (pilosité excessive)',value:'Hypertrichose (pilosité excessive)'},
        {label:'Hypotrichose (perte de pilosité)',value:'Hypotrichose (perte de pilosité)'},
        {label:'Kératose pilaire',value:'Kératose pilaire'},
        {label:'Lésions suintantes',value:'Lésions suintantes'},
        {label:'Nodules sous-cutanés',value:'Nodules sous-cutanés'},
        {label:'Papules du cuir chevelu',value:'Papules du cuir chevelu'},
        {label:'Pustules sur le cuir chevelu',value:'Pustules sur le cuir chevelu'},
        {label:'Séborrhée (cuir chevelu gras)',value:'Séborrhée (cuir chevelu gras)'},
        {label:'Squames épaisses (croûtes blanchâtres ou jaunâtres)',value:'Squames épaisses (croûtes blanchâtres ou jaunâtres)'},
        {label:'Trichorrhexie nodosa (cheveux fragiles, cassants)',value:'Trichorrhexie nodosa (cheveux fragiles, cassants)'},

    ]
    const examen : Ref<any> = ref({})

    async function getExamenPhysique(){
        const data :any =  await examenClient.getByID(consult.examen_id)
        return {
            id:data.id,
            hair : JSON.parse(data.hair),
            nails : JSON.parse(data.nails),
            face : JSON.parse(data.face),
            body : JSON.parse(data.body)
        }
    }
    async function setExamenPhysique(){
        await examenClient.update(examen.value)
        examen.value = await getExamenPhysique()
    }

    onBeforeMount(async ()=>{
        examen.value = await getExamenPhysique()
    })
</script>

<template>
    <el-form label-position="top">
        <el-form-item label="Visage">
            <el-select-v2
                v-model="examen.face"
                :options="data_visage"
                placeholder="Selectionner"
                multiple
                filterable
                allow-create
                class="w-full"
                clearable
                @change="async ()=>{await setExamenPhysique()}"
                :disabled="!consult.edit"
            />
        </el-form-item>
        <el-form-item label="Corps">
            <el-select-v2
                v-model="examen.body"
                :options="data_corps"
                placeholder="Selectionner"
                multiple
                filterable
                allow-create
                class="w-full"
                clearable
                @change="async()=>{await setExamenPhysique()} "
                :disabled="!consult.edit"
            />
        </el-form-item>
        <el-form-item label="Ongles">
            <el-select-v2
                v-model="examen.nails"
                :options="data_ongles"
                placeholder="Selectionner"
                multiple
                filterable
                allow-create
                class="w-full"
                clearable
                @change="async ()=>{await setExamenPhysique()}"
                :disabled="!consult.edit"
            />
        </el-form-item>
        <el-form-item label="Cheveux">
            <el-select-v2
                v-model="examen.hair"
                :options="data_cheveux"
                placeholder="Selectionner"
                multiple
                filterable
                allow-create
                class="w-full"
                clearable
                @change="async ()=>{await setExamenPhysique()}"
                :disabled="!consult.edit"
            />
        </el-form-item>
    </el-form>
</template>