<?php
    session_start();

    require_once 'objets.php';

    function check_login(BDD $bdd): bool{
        $select_users = $bdd->get_bdd()->prepare("SELECT * FROM users");
        $select_users->execute();
        $users = $select_users->fetchALL(PDO::FETCH_ASSOC);

        $username = $_POST['username'];
        $password = $_POST['password'];

        foreach ($users as $user){
            if ($user['username'] === $username && password_verify($password, $user['password'])){
                return true;
            }
        }

        return false;
    }

    $bdd = new BDD();

    if (isset($_POST['username']) && isset($_POST['password'])){
        $check = check_login($bdd);
        $try_login = true;
    } else {
        $try_login = false;
    }

    // Personnalisation du message à la connexion
    if ($try_login && $check){
        $message = 'Connexion réussie.';
        $class = 'success';
    } else if ($try_login && !$check) {
        $message = 'Identifiant ou mot de passe incorrect.';
        $class = 'failure';
    } else {
        $message = 'Identifiant ou mot de passe incorrect.';
        $class = 'hidden';
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ludine Games</title>
        <link rel="stylesheet" href="style-index.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <h1 class="main_title">Ludine Games</h1>
        <div id="overlay" onclick="hide()"></div>
        <button id="login" onclick="show()"><span class="material-symbols-outlined icon-user_circle">account_circle</span> Se connecter</button>
            <form action="index.php" method="post">
                <div class="login_container" id="login_container">
                    <h2>Connexion</h2>
                    <button class="close" onclick="hide()" type="button"><span class="material-symbols-outlined">close</span></button>
                    <div class="test">
                        <label for="username">Identifiant :</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div>
                        <label for="password">Mot de passe :</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <p class="message <?php echo $class; ?>"><?php echo $message; ?></p>
                    <button>Connexion</button>
                </div>
            </form>
            <script src="fichier.js"></script>
    </body>
</html>