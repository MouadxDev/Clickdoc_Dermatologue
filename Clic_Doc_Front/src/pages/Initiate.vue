<script setup lang="ts">
    import { reactive , onBeforeMount } from 'vue';
    import { Initiation } from '../../core/Clients/Initiate';

    const initiation = new Initiation();
    const credentials = reactive({
        email:"",
        password:""
    })

    onBeforeMount(async ()=>{
        await initiation.request()
    })

    async function initiate() {
        await initiation.go(credentials)
    }



</script>

<template>
    <div class="background w-screen h-screen">
        <div class="hero h-screen ">
            <div class="hero-content w-96 ">
                <el-card class="box-card">
                    <div class="w-72 mb-3 text-center">
                        <img src="/logo.png"  alt="mSpace" class=" w-12 h-12 mx-auto"> 
                    </div>
                    <hr class="my-4">
                        <div class="font-semibold text-xl text-center"> Initiation  </div>
                    <hr class="my-4">
                    <el-form class="w-full"  :label-position="'top'"  label-width="200px" :model="credentials" >
                        <el-form-item >
                            <el-input placeholder="Nom d'utilisateur / email administrateur" size="large" v-model="credentials.email"  />
                        </el-form-item>
                        <el-form-item >
                            <el-input placeholder="Mot de passe" size="large" type="password" v-model="credentials.password"/>
                        </el-form-item>
                        <el-form-item >
                            <el-button  class="mt-4 w-full mx-auto btn-doc" type="primary" size="large" @click="async ()=> await initiate()" > 
                                <el-icon >
                                    <Right />
                                </el-icon> &nbsp; Initier
                            </el-button>
                        </el-form-item>
                    </el-form>
                </el-card>
            </div>
        </div>
    </div>
</template>