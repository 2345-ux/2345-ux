<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Configuration de la connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=rent_shop;charset=utf8",
        "saw24",
        "saw24",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]));
}

// Génération du code
$rental_code = generateUniqueCode('MKT');

// Vérification que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $rental_code = $_POST['rental_code'] ?? null;
    $shop_number = $_POST['shop_number'] ?? null;
    $tenant_name = $_POST['tenant_name'] ?? null;
    $montant_verse = $_POST['montant_verse'] ?? null;
    $payment_date = $_POST['payment_date'] ?? null;
    $nom_marche = $_POST['nom_marche'] ?? null;
    $marche_id = !empty($_POST['marche_id']) ? $_POST['marche_id'] : null; // NULL si vide
    $situation_name = $_POST['situation_name'] ?? null;

    // Validation des champs requis
    if (empty($shop_number) || empty($tenant_name) || empty($montant_verse) || empty($payment_date) || empty($situation_name)) {
        echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs requis.']);
        exit;
    }

    try {
        // Préparation de la requête SQL
        $stmt = $pdo->prepare("
            INSERT INTO t_payments (rental_code, shop_number, tenant_name, montant_verse, payment_date, nom_marche, marche_id, situation_name)
            VALUES (:rental_code, :shop_number, :tenant_name, :montant_verse, :payment_date, :nom_marche, :marche_id, :situation_name)
        ");

        // Exécution de la requête avec les paramètres
        $stmt->execute([
            ':rental_code' => $rental_code,
            ':shop_number' => $shop_number,
            ':tenant_name' => $tenant_name,
            ':montant_verse' => $montant_verse,
            ':payment_date' => $payment_date,
            ':nom_marche' => $nom_marche,
            ':marche_id' => $marche_id, // Peut être NULL
            ':situation_name' => $situation_name
        ]);

        // Réponse en cas de succès
        echo json_encode(['success' => true, 'message' => 'Paiement ajouté avec succès.']);
    } catch (PDOException $e) {
        // Réponse en cas d'erreur SQL
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du paiement : ' . $e->getMessage()]);
    }
} else {
    // Réponse si la méthode n'est pas POST
    echo json_encode(['success' => false, 'message' => 'Requête non valide.']);
}
function generateUniqueCode($prefix) {
    $date = new DateTime();
    return $prefix . $date->format('YmdHis') . sprintf('%03d', rand(0, 999));
}