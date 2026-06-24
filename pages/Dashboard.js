const Dashboard = {
  template: `
    <div>
      <Navbar></Navbar>
      <div class="p-6">
        <h1 class="text-3xl font-bold mb-5">Dashboard</h1>

        <div v-if="loading" class="text-gray-500">
          Memuat data...
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white p-5 rounded shadow border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Barang</p>
            <p class="text-3xl font-bold mt-1">{{ summary.total_barang }}</p>
            <p class="text-gray-400 text-xs mt-1">Stok: {{ summary.total_stok }}</p>
          </div>

          <div class="bg-white p-5 rounded shadow border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Total Kategori</p>
            <p class="text-3xl font-bold mt-1">{{ summary.total_kategori }}</p>
          </div>

          <div class="bg-white p-5 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Total Supplier</p>
            <p class="text-3xl font-bold mt-1">{{ summary.total_supplier }}</p>
          </div>

          <div class="bg-white p-5 rounded shadow border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm">Total Histori</p>
            <p class="text-3xl font-bold mt-1">{{ summary.total_histori }}</p>
          </div>
        </div>
      </div>
    </div>
  `,
  data() {
    return {
      loading: true,
      summary: {
        total_barang: 0,
        total_kategori: 0,
        total_supplier: 0,
        total_histori: 0,
        total_stok: 0
      }
    };
  },
  mounted() {
    this.loadSummary();
  },
  methods: {
    loadSummary() {
      axios.get(apiUrl + '/api/dashboard-summary')
        .then(res => {
          this.summary = res.data;
        })
        .catch(() => {
          console.error('Gagal memuat ringkasan dashboard');
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};