function toggleSidebar() { document.getElementById('sidebar').classList.toggle('sidebar-active'); }
        
        const html = document.documentElement;
        const themeIcon = document.getElementById('theme-icon');
        
        if (localStorage.getItem('theme') === 'dark') {
            html.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        }
        
        function toggleDarkMode() {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        }

        function toggleModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                if(modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    setTimeout(() => { modal.classList.remove('opacity-0'); modal.firstElementChild.classList.remove('scale-95'); }, 20);
                } else {
                    modal.classList.add('opacity-0');
                    modal.firstElementChild.classList.add('scale-95');
                    setTimeout(() => modal.classList.add('hidden'), 300);
                }
            }
        }

        function bukaModalEdit(id, judul, kategori, isi) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-judul').value = decodeURIComponent(judul.replace(/\+/g, ' '));
            document.getElementById('edit-kategori').value = decodeURIComponent(kategori.replace(/\+/g, ' '));
            document.getElementById('edit-isi').value = decodeURIComponent(isi.replace(/\+/g, ' '));
            toggleModal('modal-edit');
        }
