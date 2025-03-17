import {
  createRouter,
  createWebHashHistory,
} from 'vue-router';
import { useAuthStore } from '../../core/Data/stores/auth';

const routes = [
  {
    path: '/initiate',
    name: 'Initiation',
    component: () => import('../pages/Initiate.vue'),
    // meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../pages/Login.vue'),
    // meta: { requiresAuth: true },
  },
  {
    path: '/',
    name: 'Home',
    component: () => import('../pages/Home.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/medicaments',
    name: 'Medicaments',
    component: () => import('../pages/Medicaments.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/analyses',
    name: 'Analyses',
    component: () => import('../pages/Analyses.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/agenda',
    name: 'Agenda',
    component: () => import('../pages/Agenda.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/dossiers',
    name: 'Folders',
    component: () => import('../pages/Dossiers/Index.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/dossiers/nouveau',
    name: 'NewFolder',
    component: () => import('../pages/Dossiers/New.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/dossiers/:id',
    name: 'ShowFolder',
    component: () => import('../pages/Dossiers/NewShow.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/salle-attente',
    name: 'WaitingList',
    component: () => import('../pages/SalleAttente/Index.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/consultations',
    name: 'ShowConsult',
    component: () => import('../pages/Consultations/Index.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/consultation',
    name: 'AddConsult',
    component: () => import('../pages/Consultations/NewNew.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/laboratoires-medicament',
    name: 'MedicLab',
    component: () => import('../pages/Admin/Labomed.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/actes-medicaux',
    name: 'ActeMedical',
    component: () => import('../pages/ActeMedical.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/ordonnances',
    name: 'Ordonnances',
    component: () => import('../pages/Ordonnance.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/licences',
    name: 'Licence',
    component: () => import('../pages/Admin/Licence.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/caisse',
    name: 'Caisse',
    component: () => import('../pages/Caisse.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/Medecinetravail',
    name: 'Medecinetravail',
    component: () => import('../pages/medecinetravail.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/personnel',
    name: 'Personnel',
    component: () => import('../pages/Personnel.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/rapports',
    name: 'Rapports',
    component: () => import('../pages/Rapports.vue'),
  },
  {
    path: '/Stock',
    name: 'Stock',
    component: () => import('../pages/Stock.vue'),
    meta: { requiresAuth: true },
  }
]


const router = createRouter({
    history: createWebHashHistory(),
    routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.token) {
    // If the route requires authentication and the user is not authenticated
    next('/login'); // Redirect to login
  } else {
    next(); // Proceed to the route
  }
});


export default router