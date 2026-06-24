const { createApp } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

// =====================================
// ROUTES PUBLIC
// =====================================

const routes = [
    { path: '/', component: Home }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

const apiUrl = 'http://localhost:8080';

// =====================================
// APP
// =====================================

const app = createApp({
    template: '<router-view></router-view>'
});

app.component('NavbarPublic', NavbarPublic);

app.use(router);

app.mount('#app');