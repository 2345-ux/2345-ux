<?php
// get_shops.php
header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host=localhost;dbname=rent_shop;charset=utf8",
        "saw24",
        "saw24",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Requête pour récupérer les boutiques avec les détails du marché
    $stmt = $pdo->prepare("
        SELECT 
            t_shop.shop_code,
            t_shop.shop_number,
            t_shop.shop_name, 
            t_shop.tenant_name, 
            t_shop.tenant_phone, 
            t_shop.shop_rent, 
            t_shop.shop_location, 
            t_marche.nom_marche 
        FROM t_shop
        INNER JOIN t_marche ON t_shop.id_marche = t_marche.id
    ");
    $stmt->execute();

    $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérification des résultats
    if ($shops && count($shops) > 0) {
        $shops = array_map(function ($shop) {
            return array_map(function ($value) {
                return $value ?? ''; // Remplacer les valeurs nulles par des chaînes vides
            }, $shop);
        }, $shops);

        echo json_encode([
            'status' => 'success',
            'message' => 'Liste des boutiques récupérée avec succès !',
            'donnees' => $shops
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Aucune boutique trouvée.',
            'donnees' => []
        ]);
    }
} catch (PDOException $e) {
    // Gestion des erreurs PDO
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur lors de la récupération des boutiques. Veuillez réessayer plus tard.',
        'details' => $e->getMessage() // À retirer en production
    ]);
}
