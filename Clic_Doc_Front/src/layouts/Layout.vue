<script setup lang="ts">
    import {Item} from "../../core/Types/Components/Item"
    import {Group} from "../../core/Types/Components/Group"
    import { useUiStore } from '../../core/Data/stores/ui';
    import { useAuthStore } from '../../core/Data/stores/auth';
    import { Auth } from '../../core/Clients/Auth';
    import  {IResponse} from "../../core/Types/IResponse"
    import { useRouter } from "vue-router";

    const auth = new Auth();
    const store = useAuthStore()
    const ui = useUiStore()
    const router = useRouter()

    async function logout(){
        const response:IResponse = await auth.logout();

        if(response.status==200)
        {
            router.push("/login");
        }

    }

    const items : Array<Item>=[
        {
            icon:"Monitor",
            link:"/",
            text:"Tableau de bord"
        },
        {
            icon:"Calendar",
            link:"/agenda",
            text:"Agenda"
        },
        {
            icon:"List",
            link:"/salle-attente",
            text:"Salle d'attente"
        },

    ]

    const groups:Array<Group> =[
        {
            name:"Click Cabinet",
            superAdmin:false,
            items:[
                {
                    icon:"Folder",
                    link:"/dossiers",
                    text:"Dossiers patients"
                },
                {
                    icon:"Files",
                    link:"/consultations",
                    text:"Consultations"
                },
                {
                    icon:"Tickets",
                    link:"/ordonnances",
                    text:"Ordonnances"
                },
                {
                    icon:"Loading",
                    link:"/analyses",
                    text:"Analyses"
                },
            ]
        },
        {
            name:"Click ADMIN",
            superAdmin:true,
            items:[
                {
                    icon:"Setting",
                    link:"/laboratoires-medicament",
                    text:"Lab medicament"
                },
                {
                    icon:"Setting",
                    link:"/laboratoires-analyse",
                    text:"Labo analyses"
                },
                {
                    icon:"Setting",
                    link:"/licences",
                    text:"Licences"
                },
            ]
        },
        {
            name:"Click Gestion",
            superAdmin:false,
            items:[
                {
                    icon:"Promotion",
                    link:"/actes-medicaux",
                    text:"Actes médicaux"
                },
                {
                    icon:"UserFilled",
                    link:"/personnel",
                    text:"Personnel"
                },
                {
                    icon:"Mug",
                    link:"/medicaments",
                    text:"Médicaments"
                },
                {
                    icon:"HelpFilled",
                    link:"/analyses",
                    text:"Liste Analyses"
                },
            ]
        },
        {
            name:"Click COMPTA",
            superAdmin:false,
            items:[
                {
                    icon:"Money",
                    link:"/caisse",
                    text:"Caisse"
                }
            ]
        },
        {
            name:"Click AI",
            superAdmin:false,
            items:[
                {
                    icon:"Money",
                    link:"/caisse",
                    text:"Caisse"
                }
            ]
        }
    ]
    const more : any = {
            name:"Click E-Consult",
            superAdmin:false,
            items:[
                {
                    icon:"VideoCamera",
                    link:"/e-consultation/video",
                    text:"Video"
                },
                {
                    icon:"Connection",
                    link:"/e-consultation/messagerie",
                    text:"Messagerie"
                },
                {
                    icon:"Mug",
                    link:"/medicaments",
                    text:"Médicaments"
                },
            ]
        }
</script>
<template>
    <div class="common-layout background-clickdoc h-screen max-h-screen">
        <el-container>
            <el-aside width="250px" class="p-4" v-if="ui.fold==false">
                <ui-menu :has-logo="true" :is-superadmin="store.user.role=='superAdmin'" logo="/logo_with_text.png" :items="items" :has-groups="true" :groups="groups"  />
            </el-aside>
            <el-container class="h-screen overflow-y-auto">
                <el-header class="pt-1">
                    <div class="navbar rounded-xl h-12 mt-1">
                        <div class="navbar-start">
                            <button class="btn btn-ghost btn-circle" @click="ui.setFold(!ui.fold)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                            </button>
                        </div>
                        <div class="navbar-center">
                            <div class="logo-solo-text" >
                            </div>
                        </div>
                        <div class="navbar-end">
                            <button class="btn btn-ghost btn-circle">
                                <div class="indicator">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                    <span class="badge badge-xs badge-primary indicator-item"></span>
                                </div>
                            </button>
                            <div class="dropdown">
                                <label tabindex="0" class="btn btn-ghost rounded-full">
                                    <el-avatar
                                        :size="32"
                                        class="mr-3"
                                        :src="store.user.avatar"
                                    />
                                    <span class="text-large font-600 mr-3"> {{ store.user.name }} </span>
                                </label>
                                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a>Mon profil</a></li>
                                    <li @click="async()=>{await logout()}"><a>De déconnecter</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </el-header>
                <el-main>
                    <slot></slot>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>
  