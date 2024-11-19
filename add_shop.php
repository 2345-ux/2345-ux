<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "rent_shop";
$username = "saw24";
$password = "saw24";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les données du formulaire
$shop_code = $_POST['shop_code'] ?? '';
$shop_number = $_POST['shop_number'] ?? '';
$shop_name = $_POST['shop_name'] ?? '';
$tenant_name = $_POST['tenant_name'] ?? '';
$tenant_phone = $_POST['tenant_phone'] ?? null;
$shop_rent = $_POST['shop_rent'] ?? 0.00;
$shop_localisation = $_POST['shop_localisation'] ?? null;
$id_marche = $_POST['id_marche'] ?? '';

try {
    // Préparer la requête SQL
    $sql = "INSERT INTO t_shop (shop_code, shop_number, shop_name, tenant_name, tenant_phone, shop_rent, shop_localisation, id_marche)
            VALUES (:shop_code, :shop_number, :shop_name, :tenant_name, :tenant_phone, :shop_rent, :shop_localisation, :id_marche)";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête
    $stmt->execute([
        ':shop_code' => $shop_code,
        ':shop_number' => $shop_number,
        ':shop_name' => $shop_name,
        ':tenant_name' => $tenant_name,
        ':tenant_phone' => $tenant_phone,
        ':shop_rent' => $shop_rent,
        ':shop_localisation' => $shop_localisation,
        ':id_marche' => $id_marche
    ]);

    echo "Boutique ajoutée avec succès.";
} catch (PDOException $e) {
    echo "Erreur lors de l'ajout : " . $e->getMessage();
}
