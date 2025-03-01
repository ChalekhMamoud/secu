<?php
// Dossier où les fichiers seront stockés
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Créer le dossier si nécessaire
}

// Vérifier si la requête est une méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer les données envoyées par le formulaire
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

    // Vérifier que les informations essentielles sont présentes
    if (empty($type) || empty($latitude) || empty($longitude)) {
        echo json_encode(['status' => 'error', 'message' => 'Les informations de l\'incident sont manquantes.']);
        exit;
    }

    // Vérifier si un fichier a été envoyé
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        
        // Récupérer les informations du fichier envoyé
        $fileTmpPath = $_FILES['media']['tmp_name'];
        $fileName = $_FILES['media']['name'];
        $fileSize = $_FILES['media']['size'];
        $fileType = $_FILES['media']['type'];

        // Définir le chemin où le fichier sera enregistré
        $filePath = $uploadDir . basename($fileName);

        // Déplacer le fichier du répertoire temporaire vers le répertoire de stockage
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            // Préparer l'email à envoyer aux autorités
            $to = 'mamoud.chalekh@laposte.net'; // Adresse email des autorités
            $subject = 'Signalement d\'incident';
            $message = "
                Un incident a été signalé par un citoyen.
                
                Type d'incident: $type
                Latitude: $latitude
                Longitude: $longitude
                
                Lien vers le fichier multimédia: $filePath
                
                Veuillez traiter cette alerte dans les plus brefs délais.
            ";
            $headers = 'From: noreply@votreapp.com' . "\r\n" .
                'Reply-To: noreply@votreapp.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            // Envoi de l'email
            if (mail($to, $subject, $message, $headers)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Incident reçu et fichier téléchargé avec succès. L\'email a été envoyé aux autorités.',
                    'file_path' => $filePath,
                    'type' => $type,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi de l\'email.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors du téléchargement du fichier.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aucun fichier ou erreur de téléchargement.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
