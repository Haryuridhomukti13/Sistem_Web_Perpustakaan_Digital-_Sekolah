window.onload = function() {
            // Trigger dialog print
            window.print();
        }

        // Event listener saat jendela print ditutup
        window.onafterprint = function() {
            // Menutup tab secara otomatis
            window.close();
        }

        // Tambahan: Jika browser memblokir window.close(), 
        // pastikan kamu membuka link ini dengan target="_blank"
