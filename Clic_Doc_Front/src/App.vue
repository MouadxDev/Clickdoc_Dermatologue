<template>
  <el-config-provider :locale="locale">
    <router-view></router-view>
  </el-config-provider>
</template>
<style>
  .fixed-bar {
    position: fixed;
    bottom: 10px;
    right: 10px;
  }
</style>
<script setup lang="ts">
import fr from 'element-plus/dist/locale/fr.mjs';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../core/Data/stores/auth';
import { useSocketStore } from '../core/Data/stores/socket';
import { onBeforeMount } from 'vue';
import { ElNotification } from 'element-plus';

const locale = fr;
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const ws = useSocketStore().socket;

// Check and request permissions
async function requestNotificationPermission() {
  let permission = Notification.permission;

  // Keep requesting until the user grants permission
  // while (permission !== 'granted') {
    const result = await Notification.requestPermission();
    permission = result;

    if (result === 'granted') {
      console.log('Notification permission granted.');
    } else {
      console.warn('Notification permission denied. ');
    }
  // }
}

async function requestAudioPermission() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    stream.getTracks().forEach((track) => track.stop()); // Close the stream after testing
    console.log('Audio permission granted.');
  } catch (error) {
    console.warn('Audio permission denied. Retrying...');
    await requestAudioPermission(); // Retry until permission is granted
  }
}

// Request permissions on load
onBeforeMount(async () => {
  await requestNotificationPermission();
  await requestAudioPermission();

  if (authStore.token == null && route.path !== '/initiate') {
    router.push('/login');
  }
});

// WebSocket events
ws.addEventListener('open', () => {
  console.log('We are connected');
});

ws.addEventListener('message', (event: any) => {
  const content = JSON.parse(event.data);
  const audio = new Audio('/notification.mp3');

  if (
    authStore.user.id &&
    content.entite === authStore.user.entity_id &&
    authStore.user.role === 'assistant'
  ) {
    audio.play();
    ElNotification({
      title: 'Patient Suivant',
      dangerouslyUseHTMLString: true,
      message: `Le docteur vous demande de faire entrer <b>${content.patient}</b>`,
      type: 'warning',
      duration: 0,
    });
  }
});
</script>


