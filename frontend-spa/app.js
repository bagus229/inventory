const { createApp } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

// =====================================
// KONFIGURASI API
// =====================================
const apiUrl = 'https://e-inventory.up.railway.app';
axios.defaults.baseURL = apiUrl; // <-- PERBAIKAN: Hubungkan ke Axios

// =====================================
// AXIOS REQUEST INTERCEPTOR
// =====================================
axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('userToken');
        if (token) {
            config.headers['Authorization'] = 'Bearer ' + token;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// =====================================
// AXIOS RESPONSE INTERCEPTOR
// =====================================
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Jika error adalah murni masalah CORS / Jaringan putus
        if (!error.response) {
            console.error('CORS Error atau Masalah Jaringan! Server tidak merespons header CORS.');
        }

        if (error.response && error.response.status === 401) {
            alert('Sesi login habis. Silakan login kembali.');
            localStorage.removeItem('userToken');
            localStorage.removeItem('isLoggedIn');
            window.location.href = '#/login';
        }
        return Promise.reject(error);
    }
);

// =====================================
// ROUTES & APP INITIALIZATION (Sudah Benar)
// =====================================
const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/kategori', component: Kategori, meta: { requiresAuth: true } },
  { path: '/supplier', component: Supplier, meta: { requiresAuth: true } },
  { path: '/barang', component: Barang, meta: { requiresAuth: true } },
  { path: '/histori', component: Histori, meta: { requiresAuth: true } }
];

const router = VueRouter.createRouter({
  history: VueRouter.createWebHashHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (to.meta.requiresAuth && !isLoggedIn) {
    next(false); 
  } else {
    next();
  }
});

const app = Vue.createApp({
  template: '<router-view></router-view>'
});
app.component('Navbar', Navbar);
app.use(router);
app.mount('#app');
