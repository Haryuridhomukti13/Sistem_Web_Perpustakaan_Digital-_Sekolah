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
