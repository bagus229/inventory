const Login = {
  template: `
    <div>
      <Navbar></Navbar>
      <div class="flex justify-center mt-12">
        <div class="bg-white w-96 p-8 rounded-2xl shadow-xl border border-gray-100">
          <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-3xl mb-3">
              🔐
            </div>
            <h2 class="text-3xl font-bold text-slate-800">
              Login Admin
            </h2>
            <p class="text-gray-500 text-sm mt-2">
              Masuk untuk mengakses dashboard E-Inventory
            </p>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Username
            </label>
            <input
              v-model="username"
              type="text"
              placeholder="Masukkan username"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            >
          </div>
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Password
            </label>
            <input
              v-model="password"
              type="password"
              placeholder="Masukkan password"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            >
          </div>
          <button
            @click="login"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold shadow-md transition duration-300"
          >
            Login
          </button>
          <p class="text-center text-xs text-gray-400 mt-4">
            E-Inventory Management System
          </p>
        </div>
      </div>
    </div>
  `,
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    login() {
      axios.post(apiUrl + '/api/login', {
        username: this.username,
        password: this.password
      })
        .then(res => {
          localStorage.setItem('userToken', res.data.data.token);
          localStorage.setItem('isLoggedIn', 'true');
          this.$router.push('/dashboard');
        })
        .catch(() => {
          alert('Login gagal');
        });
    }
  }
};
