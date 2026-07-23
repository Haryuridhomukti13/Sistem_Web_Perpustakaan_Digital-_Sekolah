const html = document.documentElement;
		const themeIcon = document.getElementById('theme-icon');

		if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
			html.classList.add('dark');
			themeIcon.classList.replace('fa-moon', 'fa-sun');
		} else {
			html.classList.remove('dark');
			themeIcon.classList.replace('fa-sun', 'fa-moon');
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

		function toggleSidebar() {
			const sidebar = document.getElementById('sidebar');
			sidebar.classList.toggle('sidebar-active');
		}
