document.addEventListener('DOMContentLoaded', () => {
    const uploadButton = document.getElementById('uploadButton');
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const errorMessage = document.getElementById('errorMessage');

    if (!uploadButton || !fileInput || !uploadForm || !errorMessage) {
        console.error('One or more elements not found');
        return;
    }

    uploadButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            errorMessage.style.display = 'none';
            uploadForm.submit();
        } else {
            errorMessage.textContent = 'Please select an image file.';
            errorMessage.style.display = 'block';
        }
    });

    uploadButton.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadButton.style.backgroundColor = '#e8f0d9';
    });

    uploadButton.addEventListener('dragleave', () => {
        uploadButton.style.backgroundColor = '#f0f7e9';
    });

    uploadButton.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadButton.style.backgroundColor = '#f0f7e9';
        const file = event.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            errorMessage.style.display = 'none';
            fileInput.files = event.dataTransfer.files;
            uploadForm.submit();
        } else {
            errorMessage.textContent = 'Please drop an image file.';
            errorMessage.style.display = 'block';
        }
    });
});
