import Navbar from './components/Navbar.js';
import Home from './pages/Home.js';
import Login from './pages/Login.js';
import Dashboard from './pages/Dashboard.js';
import Kategori from './pages/Kategori.js';
import Supplier from './pages/Supplier.js';
import Barang from './pages/Barang.js';
import Histori from './pages/Histori.js';


const { createApp } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

// =====================================
// KONFIGURASI API
// =====================================

const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
  ? 'http://localhost:8080'
  : 'https://railway.app'; // Ganti dengan domain Railway Anda nanti jika sudah di-deploy


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

        if (
            error.response &&
            error.response.status === 401
        ) {

            alert(
                'Sesi login habis. Silakan login kembali.'
            );

            localStorage.removeItem('userToken');
            localStorage.removeItem('isLoggedIn');

            window.location.href = '#/login';
        }

        return Promise.reject(error);
    }
);

// =====================================
// ROUTES
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
    next(false); // batalkan navigasi, jangan redirect
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