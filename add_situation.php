<?php
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';
$dbname = 'rent_shop';
$username = 'saw24';
$password = 'saw24';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion à la base de données.']);
    exit;
}

// Fonction pour générer un code unique
function generateUniqueCode($prefix) {
    $date = new DateTime();
    return $prefix . $date->format('YmdHis') . sprintf('%03d', rand(0, 999));
}

// Récupération des données POST
$situation_nom = $_POST['situation_nom'] ?? null;
$description = $_POST['description'] ?? null;

if (!$situation_nom || !$description) {
    echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}

// Génération du code
$code = generateUniqueCode('SIT-');

// Insertion dans la base de données
try {
    $query = "INSERT INTO t_situations (code, situation_nom, description) VALUES (:code, :situation_nom, :description)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':situation_nom', $situation_nom);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Situation ajoutée avec succès.', 'code' => $code]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout : ' . $e->getMessage()]);
}
?>
