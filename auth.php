<?php
session_start();
// Tu by si za normálnych okolností načítal knižnicu HybridAuth cez Composer
// require 'vendor/autoload.php';

$config = [
    'callback' => 'http://localhost/smartydev/auth.php',
    'providers' => [
        'Google' => ['enabled' => true, 'keys' => ['id' => 'TVOJ_GOOGLE_CLIENT_ID', 'secret' => 'TVOJ_SECRET']],
        'GitHub' => ['enabled' => true, 'keys' => ['id' => 'TVOJ_GITHUB_ID', 'secret' => 'TVOJ_SECRET']],
    ],
];

try {
    // Toto je simulácia toho, čo sa stane po úspešnom overení cez Google
    if (isset($_GET['login_success'])) {
        
        $db = new mysqli("localhost", "root", "", "smartydev_db");

        // Dáta, ktoré vráti Google
        $oauth_id = "123456789"; 
        $email = "uzivatel@gmail.com";
        $name = "Meno Používateľa";

        // Skontrolujeme, či už user v DB existuje
        $check = $db->query("SELECT id FROM users WHERE oauth_id = '$oauth_id'");
        
        if ($check->num_rows == 0) {
            // Ak nie, zaregistrujeme ho (VÁŽNE FUNGUJÚCI REGISTER)
            $db->query("INSERT INTO users (oauth_id, email, name, oauth_provider) VALUES ('$oauth_id', '$email', '$name', 'google')");
            $user_id = $db->insert_id;
        } else {
            $user = $check->fetch_assoc();
            $user_id = $user['id'];
        }

        // Prihlásime ho do session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;

        header("Location: serverpanel.php");
        exit();
    }
} catch (Exception $e) {
    echo "Chyba prihlásenia: " . $e->getMessage();
}
?>