const Login = {
  template: `
    <div>
      <Navbar></Navbar>
      <div class="flex justify-center mt-10">
        <div class="bg-white p-8 shadow rounded w-96">
          <h2 class="text-2xl font-bold mb-4">Login</h2>
          <input
            v-model="email"
            placeholder="Email"
            class="border p-2 w-full mb-3"
          >
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="border p-2 w-full mb-3"
          >
          <button
            @click="login"
            class="bg-blue-500 text-white w-full p-2 rounded"
          >
            Login
          </button>
        </div>
      </div>
    </div>
  `,
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
    login() {
      axios.post(apiUrl + '/api/login', {
        email: this.email,
        password: this.password
      })
        .then(res => {
          localStorage.setItem('userToken', res.data.token);
          localStorage.setItem('isLoggedIn', 'true');
          this.$router.push('/dashboard');
        })
        .catch(() => {
          alert('Login gagal');
        });
    }
  }
};