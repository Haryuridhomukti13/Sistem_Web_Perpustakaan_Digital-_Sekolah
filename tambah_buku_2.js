// Preview Sampul/Cover
        const sampulInput = document.getElementById('sampul-input');
        const imgPreview = document.getElementById('image-preview');
        const placeholderIcon = document.getElementById('placeholder-icon');
        const placeholderText = document.getElementById('placeholder-text');

        sampulInput.onchange = evt => {
            const [file] = sampulInput.files;
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.classList.remove('hidden');
                placeholderIcon.classList.add('hidden');
                placeholderText.classList.add('hidden');
            }
        }
