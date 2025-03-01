// script.js

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const photo = document.getElementById('photo');
const captureBtn = document.getElementById('captureBtn');
const clearBtn = document.getElementById('clearBtn');

// Demander l'accès à la caméra arrière
navigator.mediaDevices.getUserMedia({
    video: {
        facingMode: { exact: "environment" }  // Cela force l'utilisation de la caméra arrière
    }
})
    .then((stream) => {
        video.srcObject = stream;
    })
    .catch((err) => {
        console.error('Erreur lors de l\'accès à la caméra : ', err);
        alert('Impossible d\'accéder à la caméra');
    });

// Fonction pour capturer la photo
captureBtn.addEventListener('click', () => {
    // Dessiner l'image de la vidéo sur le canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Récupérer l'image du canvas et l'afficher
    const dataUrl = canvas.toDataURL('image/png');
    photo.src = dataUrl;
    photo.style.display = 'block';
    captureBtn.style.display = 'none';  // Cacher le bouton de capture après avoir pris la photo
    clearBtn.style.display = 'block';   // Afficher le bouton "Tout Supprimer"
});

// Fonction pour effacer la photo et réinitialiser l'interface
clearBtn.addEventListener('click', () => {
    photo.style.display = 'none';  // Masquer la photo
    captureBtn.style.display = 'block';  // Réafficher le bouton "Prendre une Photo"
    clearBtn.style.display = 'none';  // Masquer le bouton "Tout Supprimer"
});
