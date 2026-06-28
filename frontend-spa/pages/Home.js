const Home = {
    template: `
    <div>
        <Navbar></Navbar>

        <div class="min-h-screen flex items-center justify-center">
            <div class="text-center max-w-3xl px-6">

                <h1 class="text-5xl font-bold text-slate-800 mb-6">
                    Selamat Datang di E-Inventory,
                </h1>

                <h4 class="text-5xl font-bold text-slate-800 mb-6">
                    Halaman Home
                </h4>

                <p class="text-lg text-gray-600 leading-relaxed mb-8">
                    Aplikasi E-Inventory merupakan aplikasi manajemen persediaan barang
                    yang digunakan untuk membantu proses pengelolaan data kategori, supplier,
                    barang, serta pencatatan histori keluar masuk barang
                    secara cepat, aman, dan terintegrasi.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">

                    <div class="bg-white shadow-md rounded-lg p-5">
                        <h3 class="font-semibold text-xl mb-2">Data Barang</h3>
                        <p class="text-3xl font-bold text-blue-500">{{ summary.total_barang }}</p>
                        <p class="text-gray-500 mt-1">Total stok: {{ summary.total_stok }}</p>
                    </div>

                    <div class="bg-white shadow-md rounded-lg p-5">
                        <h3 class="font-semibold text-xl mb-2">Data Kategori</h3>
                        <p class="text-3xl font-bold text-blue-500">{{ summary.total_kategori }}</p>
                        <p class="text-gray-500 mt-1">Daftar Kategori</p>
                    </div>

                    <div class="bg-white shadow-md rounded-lg p-5">
                        <h3 class="font-semibold text-xl mb-2">Data Supplier</h3>
                        <p class="text-3xl font-bold text-green-500">{{ summary.total_supplier }}</p>
                        <p class="text-gray-500 mt-1">Supplier terdaftar</p>
                    </div>

                    <div class="bg-white shadow-md rounded-lg p-5">
                        <h3 class="font-semibold text-xl mb-2">Histori Stok</h3>
                        <p class="text-3xl font-bold text-orange-500">{{ summary.total_histori }}</p>
                        <p class="text-gray-500 mt-1">Total transaksi</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
    `,

    data() {
        return {
            summary: {
                total_barang: '-',
                total_kategori: '-',
                total_supplier: '-',
                total_histori: '-',
                total_stok: '-'
            }
        };
    },

    mounted() {
        axios.get(apiUrl + '/api/dashboard-summary')
            .then(res => {
                this.summary = res.data.data;
            })
            .catch(err => {
                console.error('Gagal load summary', err);
            });
    }
};
