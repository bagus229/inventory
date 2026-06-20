const Supplier = {
    template: `
    <div>
        <Navbar></Navbar>
        <div class="mx-auto mt-10 px-6">

            <h2 class="text-2xl font-bold mb-4">Manajemen Data Supplier</h2>

            <button @click="tambah" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
                Tambah Data
            </button>

            <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow w-96">
                    <h3 class="text-xl font-bold mb-4">{{ formTitle }}</h3>
                    <input type="text" v-model="formData.nama_supplier" placeholder="Nama Supplier" class="border p-2 w-full mb-3">
                    <input type="text" v-model="formData.alamat" placeholder="Alamat" class="border p-2 w-full mb-3">
                    <input type="text" v-model="formData.telepon" placeholder="Telepon" class="border p-2 w-full mb-3">
                    <input type="email" v-model="formData.email" placeholder="Email" class="border p-2 w-full mb-3">
                    <div class="flex gap-2">
                        <button @click="saveData" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                        <button @click="showForm = false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-left">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="border border-gray-300 p-3">ID</th>
                            <th class="border border-gray-300 p-3">Nama Supplier</th>
                            <th class="border border-gray-300 p-3">Alamat</th>
                            <th class="border border-gray-300 p-3">Telepon</th>
                            <th class="border border-gray-300 p-3">Email</th>
                            <th class="border border-gray-300 p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in supplier" :key="row.id" class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-3">{{ row.id }}</td>
                            <td class="border border-gray-300 p-3">{{ row.nama_supplier }}</td>
                            <td class="border border-gray-300 p-3">{{ row.alamat }}</td>
                            <td class="border border-gray-300 p-3">{{ row.telepon }}</td>
                            <td class="border border-gray-300 p-3">{{ row.email }}</td>
                            <td class="border border-gray-300 p-3 text-center">
                                <a href="#" @click.prevent="edit(row)" class="text-blue-500 mr-2">Edit</a>
                                |
                                <a href="#" @click.prevent="hapus(index, row.id)" class="text-red-500 ml-2">Hapus</a>
                            </td>
                        </tr>
                        <tr v-if="supplier.length === 0">
                            <td colspan="6" class="border border-gray-300 p-4 text-center text-gray-400">Belum ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    `,

    data() {
        return {
            supplier: [],
            formData: { id: null, nama_supplier: '', alamat: '', telepon: '', email: '' },
            showForm: false,
            formTitle: 'Tambah Data'
        };
    },

    mounted() {
        this.loadData();
    },

    methods: {
        loadData() {
            axios.get(apiUrl + '/api/supplier')
                .then(res => { this.supplier = res.data; })
                .catch(err => { console.log(err); });
        },
        tambah() {
            this.showForm = true;
            this.formTitle = 'Tambah Data';
            this.formData = { id: null, nama_supplier: '', alamat: '', telepon: '', email: '' };
        },
        edit(data) {
            this.showForm = true;
            this.formTitle = 'Ubah Data';
            this.formData = { id: data.id, nama_supplier: data.nama_supplier, alamat: data.alamat, telepon: data.telepon, email: data.email };
        },
        hapus(index, id) {
            if (confirm('Yakin menghapus data?')) {
                axios.delete(apiUrl + '/api/supplier/' + id)
                    .then(() => { this.supplier.splice(index, 1); })
                    .catch(err => { console.log(err); });
            }
        },
        saveData() {
            if (this.formData.id) {
                axios.put(apiUrl + '/api/supplier/' + this.formData.id, this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            } else {
                axios.post(apiUrl + '/api/supplier', this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            }
            this.formData = { id: null, nama_supplier: '', alamat: '', telepon: '', email: '' };
            this.showForm = false;
        }
    }
};