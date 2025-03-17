<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { Auth } from '../../core/Clients/Auth';

const router = useRouter();
const authService = new Auth();
const isLogin = ref(true);

const credentials = reactive({
  email: "",
  password: ""
});

const registerData = reactive({
  email: "",
  password: "",
  confirmPassword: "",
  phone: "",
  address: "",
  gender: "",
  establishmentName: "",
  establishmentAddress: "",
  city: "",
  contactEmail: "",
  type: "",
  contactName: ""
});

async function login() {
  const response = await authService.login(credentials);
  if (response.status == 200) {
    router.push("/");
  }
}

async function register() {
  if (registerData.password !== registerData.confirmPassword) {
    alert("Passwords do not match");
    return;
  }
  const response = await authService.register(registerData);
  if (response.status == 200) {
    isLogin.value = true; 
  }
}
</script>

<template>
  <div class="background w-screen h-screen flex items-center justify-center">
    <div class="relative w-[37%]">
      <transition name="fade-slide" mode="out-in">
        <el-card v-if="isLogin" key="login" class="box-card">
          <div class="w-[100%] mb-3 text-center">
            <img src="/logo.png" alt="mSpace" class="w-12 h-12 mx-auto" />
          </div>
          <hr class="my-4" />
          <div class="font-semibold text-xl text-center">Connexion</div>
          <hr class="my-4" />
          <el-form class="w-full" :model="credentials">
            <el-form-item>
              <el-input placeholder="Nom d'utilisateur / email" size="large" v-model="credentials.email" />
            </el-form-item>
            <el-form-item>
              <el-input placeholder="Mot de passe" size="large" type="password" v-model="credentials.password" />
            </el-form-item>
            <el-form-item>
              <button class="background-clickdoc btn btn-block" type="button" @click="login">
                <el-icon><Right /></el-icon>&nbsp; Se connecter
              </button>
            </el-form-item>
          </el-form>
          <p class="text-center text-sm cursor-pointer text-[#48a9d4]" @click="isLogin = false">
            Pas encore inscrit ? Créez un compte
          </p>
        </el-card>

        <el-card v-else key="register" class="box-card">
          <div class="w-[100%] mb-3 text-center">
            <img src="/logo.png" alt="mSpace" class="w-12 h-12 mx-auto" />
          </div>
          <hr class="my-4" />
          <div class="font-semibold text-xl text-center">Inscription</div>
          <hr class="my-4" />
          <el-form class="w-full" :model="registerData">
            <el-form-item>
              <el-input placeholder="Email" size="large" v-model="registerData.email" />
            </el-form-item>
            <el-form-item>
              <el-input placeholder="Mot de passe" size="large" type="password" v-model="registerData.password" />
            </el-form-item>
            <el-form-item>
              <el-input placeholder="Confirmez le mot de passe" size="large" type="password" v-model="registerData.confirmPassword" />
            </el-form-item>
            <div class="grid grid-cols-2 gap-4">
              <el-form-item>
                <el-input placeholder="Téléphone" size="large" v-model="registerData.phone" />
              </el-form-item>
              <el-form-item>
                <el-input placeholder="Adresse" size="large" v-model="registerData.address" />
              </el-form-item>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <el-form-item>
                <el-select placeholder="Genre" v-model="registerData.gender">
                  <el-option label="Homme" value="Homme" />
                  <el-option label="Femme" value="Femme" />
                </el-select>
              </el-form-item>
              <el-form-item>
                <el-input placeholder="Ville" size="large" v-model="registerData.city" />
              </el-form-item>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <el-form-item>
                <el-input placeholder="Nom de l'établissement" size="large" v-model="registerData.establishmentName" />
              </el-form-item>
              <el-form-item>
                <el-input placeholder="Adresse de l'établissement" size="large" v-model="registerData.establishmentAddress" />
              </el-form-item>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <el-form-item>
                <el-input placeholder="Email de contact" size="large" v-model="registerData.contactEmail" />
              </el-form-item>
              <el-form-item>
                <el-input placeholder="Type (Cabinet, laboratoire, pharmacie)" size="large" v-model="registerData.type" />
              </el-form-item>
            </div>
            <el-form-item>
              <el-input placeholder="Nom de contact" size="large" v-model="registerData.contactName" />
            </el-form-item>
            <el-form-item>
              <button class="background-clickdoc btn btn-block" type="button" @click="register">
                <el-icon><Right /></el-icon>&nbsp; S'inscrire
              </button>
            </el-form-item>
          </el-form>
          <p class="text-center text-sm cursor-pointer text-[#48a9d4]" @click="isLogin = true">
            Déjà un compte ? Connectez-vous
          </p>
        </el-card>
      </transition>
    </div>
  </div>
</template>

<style>
    .fade-slide-enter-active, .fade-slide-leave-active {
        transition: opacity 0.5s, transform 0.5s;
        }
    .fade-slide-enter-from {
        opacity: 0;
        transform: translateY(-20px);
        }
    .fade-slide-leave-to {
        opacity: 0;
        transform: translateY(20px);
        }
</style>
