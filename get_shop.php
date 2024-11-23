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

    // Requête pour récupérer les boutiques
    $stmt = $pdo->prepare("
        SELECT 
            shop_code,
            shop_number,
            shop_name,
            tenant_name,
            tenant_phone,
            shop_rent,
            shop_location,
            shop_market,
            shop_latitude,
            shop_longitude
        FROM t_shop
    ");
    $stmt->execute();

    $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérification des résultats
    if ($shops && count($shops) > 0) {
        // Nettoyage des données pour s'assurer qu'aucune valeur n'est nulle
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
        'details' => $e->getMessage() // Supprimer cette ligne en production pour éviter de divulguer des détails techniques
    ]);
}
