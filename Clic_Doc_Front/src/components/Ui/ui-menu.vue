<script setup lang="ts">
    import {Menu} from "../../../core/Types/Components/Menu"
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '../../../core/Data/stores/auth';

    const props = defineProps<Menu>()
    const router = useRouter()
    const store = useAuthStore()
  
    
    
</script>
<template>
    <div class="rounded-r-2xl p-4 pt-6 bg-white  overflow-y-auto">
        <div class="text-center rounded-2xl mb-3 " v-if="props.hasLogo==true">
            <img :src="props.logo" class="mx-auto h-20" >
            <img src="/click-doc-text.png" class="mx-auto" >
        </div>
        <div class="">
            <ul class="menu menu-sm rounded-box">
                <li  @click="router.push('/')">
                    <a>
                        <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/tableau-de-bord.png" > </el-icon> Tableau de bord
                    </a>
                </li>
                <li  @click="router.push('/agenda')">
                    <a>
                        <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/agenda.png" > </el-icon> Agenda
                    </a>
                </li>
            </ul>
            <ul class="menu menu-sm rounded-box" >
                <span  v-if="store.user.role=='superAdmin'">
                    <li class="menu-title text-uppercase"> Click Admin </li>
                    <li @click="router.push('/licences')">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/business-people.png" ></el-icon> Licences
                        </a>
                    </li>
                    <li @click="router.push('/laboratoires-medicament')">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/medicaments.png" ></el-icon> Meds Labo
                        </a>
                    </li>
                </span>
                <span  v-else>
                    <li class="menu-title text-uppercase"> Click Cabinet </li>
                    <li  @click="router.push('/salle-attente')" v-if="store.user.role !== 'assistant' || (store.privileges[0]?.enable === 1)">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/salle-dattente.png" > </el-icon> Salle d'attente
                        </a>
                    </li>
                    <li @click="router.push('/dossiers')" v-if="store.user.role != 'assistant' || (store.privileges[1].enable==1 && store.privileges[1].view==1)" >
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/dossiers.png" ></el-icon> Dossiers
                        </a>
                    </li>
                    <li @click="router.push('/consultations')" v-if="store.user.role != 'assistant' || store.privileges[2].enable==1">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/consultant.png" ></el-icon> Consultations
                        </a>
                    </li>
                    <li @click="router.push('/ordonnances')" v-if="store.user.role != 'assistant' || store.privileges[3].enable==1 " >
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/ordonnance.png" ></el-icon> Ordonnances
                        </a>
                    </li>
                    <li @click="router.push('/analyses')" v-if="store.user.role != 'assistant' || store.privileges[4].enable==1" >
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/analyse-de-sang.png" ></el-icon> Analyses
                        </a>
                    </li>
                    <li class="menu-title text-uppercase "> Click Gestion </li>
                    <li  @click="router.push('/actes-medicaux')">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/soins-medicaux.png" > </el-icon> Actes médicaux
                        </a>
                    </li>
                    <li @click="router.push('/personnel')" v-if="store.user.role != 'assistant' || store.privileges[5].enable==1">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/business-people.png"  ></el-icon> Personnel
                        </a>
                    </li>
                    <li @click="router.push('/medicaments')">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/medicaments.png" ></el-icon> Médicaments
                        </a>
                    </li>
                    <li @click="router.push('/analyses')">
                        <a>
                            <el-icon> <img src="https://clickdoc.webredirect.org/public/icons/analyses.png" ></el-icon> Liste Analyses
                        </a>
                    </li>
                    <li class="menu-title text-uppercase "> Click COMPTA </li>
                    <li  @click="router.push('/caisse')">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/caisse-enregistreuse.png" > </el-icon> Caisse
                        </a>
                    </li>
                    <li  @click="router.push('/Medecinetravail')" style="display: none;">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/office-building.png" > </el-icon> Medecine travail
                        </a>
                    </li>
					<li  @click="router.push('/rapports')">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/rapport.png" > </el-icon> Rapports
                        </a>
                    </li>
                    <li  @click="router.push('/Stock')">
                        <a>
                            <el-icon  > <img src="https://clickdoc.webredirect.org/public/icons/drugstore.png" > </el-icon> Ma pharmacie
                        </a>
                    </li>
                    <li class="menu-title text-uppercase"> Click AI </li>
                    <li class="disabled-menu">
                        <a class="disabled-link">
                            <!-- <el-icon> 
                                <img src="https://clickdoc.webredirect.org/public/icons/ai.png"> 
                            </el-icon> 
                            Assistant Ai -->
                            <span class="coming-soon">Bientôt disponible</span>
                        </a>
                    </li>

                </span>
            </ul>
        </div>
    </div>
</template>

<style>


.disabled-link {
    pointer-events: none; /* Prevents clicks */
    display: flex;
    align-items: center;
    gap: 8px; /* Adds space between icon and text */
}


.coming-soon {
    position: absolute;
    right: 16px;
    background: linear-gradient(135deg, #ff9800, #ff5722);
    color: white;
    font-size: 10px;
    padding: 5px 10px;
    border-radius: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(255, 152, 0, 0.2);
    margin-top: 25px;
    
  }
</style>