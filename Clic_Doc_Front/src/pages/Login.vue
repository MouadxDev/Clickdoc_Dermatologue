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
                Se connecter
              </button>
            </el-form-item>
          </el-form>
          <p class="text-center text-sm cursor-pointer text-[#48a9d4]" @click="isLogin = false">
            Pas encore inscrit ? Créez un compte
          </p>
        </el-card>

        <!-- Registration form omitted for brevity; keep your existing one here if needed -->

      </transition>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../core/Data/stores/auth';
import { Auth } from '../../core/Clients/Auth';

const router = useRouter();
const authStore = useAuthStore();
const authService = new Auth();

const isLogin = ref(true);
const credentials = reactive({
  email: "",
  password: ""
});

const token = localStorage.getItem('auth_token');


if (token ) {
  try {
    const user = JSON.parse(userStr);
    const entite = JSON.parse(entiteStr);
    const privileges = JSON.parse(privilegesStr);

    authStore.login(user, token, entite, privileges);
    router.push('/');
  } catch (e) {
    console.error('Failed to parse auth data from localStorage:', e);
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('auth_entite');
    localStorage.removeItem('auth_privileges');
  }
}


async function login() {
  const response = await authService.login(credentials);
  if (response.status === 200) {
    router.push("/");
  } else {
    // Handle login failure (optional)
    alert('Login failed. Please check your credentials.');
  }
}
</script>

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
