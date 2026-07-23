// Sinkronisasi class dark mode otomatis berdasarkan localStorage dashboard milikmu
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }

        // Jalankan stream kamera belakang smartphone
        function inisialisasiKamera() {
            const html5QrCode = new Html5Qrcode("reader");
            const konfigurasi = { 
                fps: 15, 
                qrbox: { width: 280, height: 160 } // Dioptimalkan berbentuk persegi panjang mengikuti model barcode buku fisik
            };

            // "environment" memerintahkan HP membuka kamera belakang (bukan kamera selfie depan)
            html5QrCode.start({ facingMode: "environment" }, konfigurasi, scanBerhasil)
            .catch((err) => {
                console.error("Izin Kamera Ditolak:", err);
                document.getElementById('reader').innerHTML = `
                    <div class="p-6 text-center text-sm text-rose-400">
                        <i class="fa fa-exclamation-triangle text-2xl mb-2 block"></i>
                        Gagal memuat kamera HP.<br>Pastikan izin akses kamera aktif dan web diakses menggunakan protokol HTTPS aman.
                    </div>`;
            });
        }

        // Fungsi pemicu saat barcode buku berhasil dibaca kamera
        function scanBerhasil(decodedText, decodedResult) {
            const pilihanAnggota = document.getElementById('id_pengguna').value;
            
            // Validasi mencegah admin melakukan scan buku sebelum memilih identitas peminjam
            if (!pilihanAnggota) {
                alert("Peringatan: Silakan pilih ANGGOTA PEMINJAM terlebih dahulu sebelum melakukan scan barcode buku!");
                return;
            }

            // Membunyikan efek suara bip layaknya mesin kasir swalayan
            let audioBip = new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav');
            audioBip.play();

            // Memasukkan hasil pembacaan string ke input tersembunyi, lalu lakukan submit form otomatis
            document.getElementById('barcode_data').value = decodedText;
            document.getElementById('form-scan').submit();
        }

        // Jalankan kamera setelah halaman selesai dimuat utuh
        window.addEventListener('DOMContentLoaded', inisialisasiKamera);
