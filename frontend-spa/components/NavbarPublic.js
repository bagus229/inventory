const NavbarPublic = {
  template: `
    <div>
      <nav class="bg-slate-800 text-white px-6 py-4">
        <div class="flex justify-between">
          <h1 class="font-bold text-xl">E-Inventory</h1>
          <div class="space-x-4">
            <router-link to="/">Home</router-link>
            
          </div>
        </div>
      </nav>

      <!-- Notifikasi belum login -->
      <div v-if="showAlert" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-6 py-3 flex justify-between items-center">
        <span>⚠️ Akses di tolak. login terlebih dahulu untuk mengakses halaman tersebut.</span>
        <button @click="showAlert = false" class="ml-4 font-bold">✕</button>
      </div>
    </div>
  `,

  data() {
    return {
      showAlert: false
    };
  },

  computed: {
    isLogin() {
      return localStorage.getItem('isLoggedIn') === 'true';
    }
  },

  methods: {
    guard(path) {
  if (!this.isLogin) {
    this.showAlert = true;
    setTimeout(() => {
      this.showAlert = false;
      this.$router.push('/login'); // redirect ke login setelah 3 detik
    }, 3000);
  } else {
    this.showAlert = false;
    this.$router.push(path);
  }
},
    logout() {
      axios.post(apiUrl + '/api/logout')
        .then(() => this.clearSessionAndRedirect())
        .catch(() => this.clearSessionAndRedirect());
    },
    clearSessionAndRedirect() {
      localStorage.removeItem('userToken');
      localStorage.removeItem('isLoggedIn');
      location.href = '#/';
    }
  }
};