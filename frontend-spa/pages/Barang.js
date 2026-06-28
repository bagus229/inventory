const Barang = {
    template: `
    <div>
        <Navbar></Navbar>
        <div class="max-w-6xl mx-auto mt-10 px-4">

            <h2 class="text-2xl font-bold mb-4">Manajemen Data Barang</h2>

            <button @click="tambah" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
                Tambah Data
            </button>

            <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow w-96 max-h-screen overflow-y-auto">
                    <h3 class="text-xl font-bold mb-4">{{ formTitle }}</h3>

                    <input type="text" v-model="formData.kode_barang" placeholder="Kode Barang" class="border p-2 w-full mb-3">
                    <input type="text" v-model="formData.nama_barang" placeholder="Nama Barang" class="border p-2 w-full mb-3">

                    <select v-model="formData.id_kategori" class="border p-2 w-full mb-3">
                        <option value="">-- Pilih Kategori --</option>
                        <option v-for="k in kategori" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
                    </select>

                    <select v-model="formData.id_supplier" class="border p-2 w-full mb-3">
                        <option value="">-- Pilih Supplier --</option>
                        <option v-for="s in supplier" :key="s.id" :value="s.id">{{ s.nama_supplier }}</option>
                    </select>

                    <input type="number" v-model="formData.stok" placeholder="Stok" class="border p-2 w-full mb-3">
                    <input type="text" v-model="formData.satuan" placeholder="Satuan (pcs, kg, dll)" class="border p-2 w-full mb-3">
                    <input type="number" v-model="formData.harga_beli" placeholder="Harga Beli" class="border p-2 w-full mb-3">
                    <input type="number" v-model="formData.harga_jual" placeholder="Harga Jual" class="border p-2 w-full mb-3">

                    <div class="flex gap-2">
                        <button @click="saveData" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                        <button @click="showForm = false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    </div>
                </div>
            </div>

            <table class="w-full border text-left text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="p-2">Kode</th>
                        <th class="p-2">Nama Barang</th>
                        <th class="p-2">Kategori</th>
                        <th class="p-2">Supplier</th>
                        <th class="p-2">Stok</th>
                        <th class="p-2">Satuan</th>
                        <th class="p-2">Harga Beli</th>
                        <th class="p-2">Harga Jual</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in barang" :key="row.id" class="border-b">
                        <td class="p-2">{{ row.kode_barang }}</td>
                        <td class="p-2">{{ row.nama_barang }}</td>
                        <td class="p-2">{{ row.nama_kategori }}</td>
                        <td class="p-2">{{ row.nama_supplier }}</td>
                        <td class="p-2">{{ row.stok }}</td>
                        <td class="p-2">{{ row.satuan }}</td>
                        <td class="p-2">{{ row.harga_beli }}</td>
                        <td class="p-2">{{ row.harga_jual }}</td>
                        <td class="p-2 text-center">
                            <a href="#" @click.prevent="edit(row)" class="text-blue-500 mr-2">Edit</a>
                            |
                            <a href="#" @click.prevent="hapus(index, row.id)" class="text-red-500 ml-2">Hapus</a>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    `,

    data() {
        return {
            barang: [],
            kategori: [],
            supplier: [],
            formData: { id: null, kode_barang: '', nama_barang: '', id_kategori: '', id_supplier: '', stok: 0, satuan: '', harga_beli: 0, harga_jual: 0 },
            showForm: false,
            formTitle: 'Tambah Data'
        };
    },

    mounted() {
        this.loadData();
        this.loadKategori();
        this.loadSupplier();
    },

    methods: {
        loadData() {
            axios.get(apiUrl + '/api/barang')
                .then(res => { this.barang = res.data.data; }) // bukan res.data.data
                .catch(err => { console.log(err); });
        },
        loadKategori() {
            axios.get(apiUrl + '/api/kategori')
                .then(res => { this.kategori = res.data.data; }) // bukan res.data.data
                .catch(err => { console.log(err); });
        },
        loadSupplier() {
            axios.get(apiUrl + '/api/supplier')
                .then(res => { this.supplier = res.data.data; }) // bukan res.data.data
                .catch(err => { console.log(err); });
        },
        tambah() {
            this.showForm = true;
            this.formTitle = 'Tambah Data';
            this.formData = { id: null, kode_barang: '', nama_barang: '', id_kategori: '', id_supplier: '', stok: 0, satuan: '', harga_beli: 0, harga_jual: 0 };
        },
        edit(data) {
            this.showForm = true;
            this.formTitle = 'Ubah Data';
            this.formData = {
                id: data.id,
                kode_barang: data.kode_barang,
                nama_barang: data.nama_barang,
                id_kategori: data.id_kategori,
                id_supplier: data.id_supplier,
                stok: data.stok,
                satuan: data.satuan,
                harga_beli: data.harga_beli,
                harga_jual: data.harga_jual
            };
        },
        hapus(index, id) {
            if (confirm('Yakin menghapus data?')) {
                axios.delete(apiUrl + '/api/barang/' + id)
                    .then(() => { this.barang.splice(index, 1); })
                    .catch(err => { console.log(err); });
            }
        },
        saveData() {
            if (this.formData.id) {
                axios.put(apiUrl + '/api/barang/' + this.formData.id, this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            } else {
                axios.post(apiUrl + '/api/barang', this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            }
            this.formData = { id: null, kode_barang: '', nama_barang: '', id_kategori: '', id_supplier: '', stok: 0, satuan: '', harga_beli: 0, harga_jual: 0 };
            this.showForm = false;
        }
    }
};
