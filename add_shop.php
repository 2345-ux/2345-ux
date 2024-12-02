<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (prévol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
    exit();
}

// Fonction pour générer un code unique
function generateUniqueCode($prefix) {
    $date = new DateTime();
    return $prefix . $date->format('YmdHis') . sprintf('%03d', rand(0, 999));
}

try {
    // Vérification de la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée. Utilisez POST.');
    }

    // Récupération et validation des données POST
    $shop_code = trim($_POST['shopCode'] ?? 'Auto');
    $shop_number = trim($_POST['shopNumber'] ?? null);
    $shop_name = trim($_POST['shopName'] ?? null);
    $tenant_name = trim($_POST['tenantName'] ?? null);
    $tenant_phone = trim($_POST['tenantPhone'] ?? null);
    $shop_rent = filter_var($_POST['shopRent'] ?? null, FILTER_VALIDATE_FLOAT);
    $shop_location = trim($_POST['shopLocation'] ?? null);
    $id_marche = trim($_POST['id_marche'] ?? null);

    // Champs obligatoires
if (!$shop_name) {
    throw new Exception('Le champ "Nom de la boutique" est obligatoire.');
}

if (!$shop_location) {
    throw new Exception('Le champ "Localisation de la boutique" est obligatoire.');
}

if (!$id_marche) {
    throw new Exception('Le champ "Identifiant du marché" est obligatoire.');
}

    // Validation du montant du loyer
    if ($shop_rent !== null && $shop_rent < 0) {
        throw new Exception('Le montant du loyer doit être positif.');
    }

    // Générer un code unique si nécessaire
    if ($shop_code === 'Auto') {
        $shop_code = generateUniqueCode('SHOP-');
    }

    // Vérification des doublons
    $query = "SELECT COUNT(*) FROM t_shop WHERE shop_code = :shop_code OR shop_number = :shop_number";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':shop_code' => $shop_code, ':shop_number' => $shop_number]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Une boutique avec ce code ou ce numéro existe déjà.');
    }

    // Insertion dans la base de données
    $query = "INSERT INTO t_shop (
        shop_code, shop_number, shop_name, tenant_name,
        tenant_phone, shop_rent, shop_location, id_marche
    ) VALUES (
        :shop_code, :shop_number, :shop_name, :tenant_name,
        :tenant_phone, :shop_rent, :shop_location, :id_marche
    )";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':shop_code' => $shop_code,
        ':shop_number' => $shop_number,
        ':shop_name' => $shop_name,
        ':tenant_name' => $tenant_name,
        ':tenant_phone' => $tenant_phone,
        ':shop_rent' => $shop_rent,
        ':shop_location' => $shop_location,
        ':id_marche' => $id_marche
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Boutique ajoutée avec succès.',
        'shopCode' => $shop_code,
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout en base de données.']);
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
