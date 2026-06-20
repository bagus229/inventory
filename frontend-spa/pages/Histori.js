const Histori = {
    template: `
    <div>
        <Navbar></Navbar>
        <div class="mx-auto mt-10 px-6">

            <h2 class="text-2xl font-bold mb-4">Manajemen Histori Barang</h2>

            <button @click="tambah" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
                Tambah Data
            </button>

            <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow w-96">
                    <h3 class="text-xl font-bold mb-4">{{ formTitle }}</h3>

                    <select v-model="formData.id_barang" class="border p-2 w-full mb-3">
                        <option value="">-- Pilih Barang --</option>
                        <option v-for="b in barangList" :key="b.id" :value="b.id">
                            {{ b.nama_barang }}
                        </option>
                    </select>

                    <select v-model="formData.jenis" class="border p-2 w-full mb-3">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>

                    <input type="number" v-model="formData.jumlah" placeholder="Jumlah" class="border p-2 w-full mb-3">

                    <textarea v-model="formData.keterangan" placeholder="Keterangan" class="border p-2 w-full mb-3" rows="3"></textarea>

                    <input type="date" v-model="formData.tanggal" class="border p-2 w-full mb-3">

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
                            <th class="border border-gray-300 p-3">Barang</th>
                            <th class="border border-gray-300 p-3">User</th>
                            <th class="border border-gray-300 p-3">Jenis</th>
                            <th class="border border-gray-300 p-3">Jumlah</th>
                            <th class="border border-gray-300 p-3">Keterangan</th>
                            <th class="border border-gray-300 p-3">Tanggal</th>
                            <th class="border border-gray-300 p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in histori" :key="row.id" class="hover:bg-gray-50">
                            <td class="border border-gray-300 p-3">{{ row.id }}</td>
                            <td class="border border-gray-300 p-3">{{ row.nama_barang }}</td>
                            <td class="border border-gray-300 p-3">{{ row.nama_user }}</td>
                            <td class="border border-gray-300 p-3 capitalize">{{ row.jenis }}</td>
                            <td class="border border-gray-300 p-3">{{ row.jumlah }}</td>
                            <td class="border border-gray-300 p-3">{{ row.keterangan }}</td>
                            <td class="border border-gray-300 p-3">{{ row.tanggal }}</td>
                            <td class="border border-gray-300 p-3 text-center">
                                <a href="#" @click.prevent="edit(row)" class="text-blue-500 mr-2">Edit</a>
                                |
                                <a href="#" @click.prevent="hapus(index, row.id)" class="text-red-500 ml-2">Hapus</a>
                            </td>
                        </tr>
                        <tr v-if="histori.length === 0">
                            <td colspan="8" class="border border-gray-300 p-4 text-center text-gray-400">Belum ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    `,

    data() {
        return {
            histori: [],
            barangList: [],
            formData: {
                id: null,
                id_barang: '',
                jenis: '',
                jumlah: '',
                keterangan: '',
                tanggal: ''
            },
            showForm: false,
            formTitle: 'Tambah Data'
        };
    },

    mounted() {
        this.loadData();
        this.loadBarang();
    },

    methods: {
        loadData() {
            axios.get(apiUrl + '/api/histori')
                .then(res => { this.histori = res.data; })
                .catch(err => { console.log(err); });
        },
        loadBarang() {
            axios.get(apiUrl + '/api/barang')
                .then(res => { this.barangList = res.data; })
                .catch(err => { console.log(err); });
        },
        tambah() {
            this.showForm = true;
            this.formTitle = 'Tambah Data';
            this.formData = {
                id: null,
                id_barang: '',
                jenis: '',
                jumlah: '',
                keterangan: '',
                tanggal: ''
            };
        },
        edit(data) {
            this.showForm = true;
            this.formTitle = 'Ubah Data';
            this.formData = {
                id: data.id,
                id_barang: data.id_barang,
                jenis: data.jenis,
                jumlah: data.jumlah,
                keterangan: data.keterangan,
                tanggal: data.tanggal
            };
        },
        hapus(index, id) {
            if (confirm('Yakin menghapus data?')) {
                axios.delete(apiUrl + '/api/histori/' + id)
                    .then(() => { this.histori.splice(index, 1); })
                    .catch(err => { console.log(err); });
            }
        },
        saveData() {
            if (this.formData.id) {
                axios.put(apiUrl + '/api/histori/' + this.formData.id, this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            } else {
                axios.post(apiUrl + '/api/histori', this.formData)
                    .then(() => { this.loadData(); })
                    .catch(err => { console.log(err); });
            }
            this.formData = {
                id: null,
                id_barang: '',
                jenis: '',
                jumlah: '',
                keterangan: '',
                tanggal: ''
            };
            this.showForm = false;
        }
    }
};