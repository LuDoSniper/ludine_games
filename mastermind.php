<?php
    session_start();

    require_once 'objets.php';
    require_once 'mastermind-fonctions.php';

    function redirect(string $location): void{
        header('Location: '.$location);
    }

    function check_login(BDD $bdd): bool{
        $bdd = $bdd->get_bdd();
        $select_user = $bdd->prepare("SELECT * FROM users WHERE ID = ?");
        $select_user->execute([$_SESSION['user']['ID']]);
        $user = $select_user->fetch();
        
        if ($user['username'] === $_SESSION['user']['username'] && password_verify($_SESSION['user']['password'], $user['password'])){
            return true;
        }

        return false;
    }

    // Vérification de connexion
    if (isset($_GET['logout'])){
        session_destroy();
        redirect('index.php');
    }
    if (isset($_GET['home'])){
        redirect('home.php');
    }

    $bdd = new BDD();

    if (isset($_SESSION['user'])){
        $check = check_login($bdd);
    } else {
        $check = false;
    }
    if (!$check) {redirect('index.php');}

    // Début du code
    if (isset($_POST['create'])){
        create_game($_POST['user'], $_SESSION['user']['ID'], $_POST['pattern']);
    }

    // Agir sur les games
    if (isset($_POST['delete'])){
        delete_game_by_ID($_POST['delete']);
    }

    // Récupérer les games
    $divs_json = str_replace('"',"'" , json_encode(get_games_ID()));
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mastermind</title>
        <link rel="stylesheet" href="style-mastermind.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    </head>
    <body>
        <header>
            <form action="home.php" method="get"><button name="home"><i class="uil uil-estate icon-home"></i></button></form>
            <h1>Ludine Games</h1>
            <form action="mastermind.php" method="get"><button name="logout"><span class="material-symbols-outlined icon-logout">logout</span> Se déconnecter</button></form>
        </header>

        <div class="games">
            <div class="games_row1"><?php $divs = display_games() ?></div>
            <button class="create" onclick="show('form-create'); show('overlay')"><span class="material-symbols-outlined icon-create">add</span> Créer</button>
        </div>

        <!-- hidden -->
        <div id="overlay" onclick="hide('overlay'); hide('form-create'); hidedivs(<?= $divs_json ?>);"></div>
        <form action="mastermind.php" method="post" id="form-play">
            <?= $divs ?>
        </form>
        <form action="mastermind.php" method="post" id="form-create">
            <span class="material-symbols-outlined icon-close close" onclick="hide('form-create'); hide('overlay')">close</span>
            <label for="user">Adversaire :</label>
            <select name="user" id="user">
                <?php generate_users_html_except_one($_SESSION['user']['ID']) ?>
            </select>
            <label for="pattern">Longueur du pattern :</label>
            <select name="pattern" id="pattern">
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            <button name="create">Créer</button>
        </form>
        <script src="script-mastermind.js"></script>
        <script>
            hide('overlay');
            hide('form-create');
            hidedivs(<?= $divs_json ?>);
        </script>
    </body>
</html>