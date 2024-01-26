<?php
    session_start();

    require_once 'ressources/objets.php';

    function redirect(): void{
        header('Location: index.php');
    }

    function check_login(BDD $bdd): bool{
        $bdd = $bdd->get_bdd();
        $select_users = $bdd->prepare("SELECT * FROM users WHERE ID = ?");
        $select_users->execute([$_SESSION['user']['ID']]);
        $users = $select_users->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user){
            if ($user['username'] === $_SESSION['user']['username'] && password_verify($_SESSION['user']['password'], $user['password'])){
                return true;
            }
        }

        return false;
    }

    $bdd = new BDD();

    if (isset($_GET['logout'])){
        session_destroy();
        header('Location: index.php');
    }

    if (isset($_SESSION['user'])){
        $check = check_login($bdd);
    } else {
        $check = false;
    }

    if (!$check){
        redirect();
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="styles/style-home.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    </head>
    <body>
        <div id="overlay" onclick="hide('mastermind'); hide('overlay')"></div>

        <header>
            <h1>Ludine Games</h1>
            <form action="home.php" method="get">
                <button id="logout" name="logout"><span class="material-symbols-outlined">logout</span> Se déconnecter</button>
            </form>
        </header>

        <!-- Bouttons -->
        <div class="games">
            <div class="mastermind_button" onclick="show('mastermind'); show('overlay')">
                <p>Mastermind</p>
            </div>
        </div>
        <!-- Cartes -->
        <div class="mastermind" id="mastermind">
            <div class="close"><span class="material-symbols-outlined icon-close" onclick="hide('mastermind'); hide('overlay')">close</span></div>
            <h2>Mastermind</h2>
            <p>BlaBla description du jeu en lui même, il faut que je fasse un texte un avec un minimum de longueur pour voir le comportement</p>
            <form action="mastermind.php" method="post"><button><span class="material-symbols-outlined icon-game">sports_esports</span> Jouer</button></form>
        </div>
        <script src="scripts/script-home.js"></script>
        <script>
            hide('mastermind');
            hide('overlay');
        </script>
    </body>
</html>