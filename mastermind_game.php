<?php
    session_start();

    require_once "ressources/objets.php";
    require_once "ressources/mastermind-fonctions.php";

    function check_login(): bool{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_users = $bdd->prepare("SELECT * FROM users");
        $select_users->execute();
        $users = $select_users->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user){
            if ($user['username'] === $_SESSION['user']['username'] && password_verify($_SESSION['user']['password'], $user['password'])){
                return true;
            }
        }

        return false;
    }

    function redirect(string $location): void{
        header('Location: '.$location);
    }

    // Vérification de la connexion
    if (isset($_SESSION['user'])){
        $check = check_login();
    } else {
        $check = false;
    }
    if (!$check) {redirect('index.php');}

    // Vérification de la partie
    if (!isset($_GET['id'])){
        redirect('mastermind.php');
    } else if (!game_exists($_GET['id'])) {
        redirect('mastermind.php');
    }

    // Vérification du logout et back
    if (isset($_GET['logout'])){
        session_destroy();
        redirect('index.php');
    } else if (isset($_GET['back'])){
        if (isset($_SESSION['pattern'])){
            unset($_SESSION['pattern']);
        }
        redirect('mastermind.php');
    }

    // Gestion du pattern local
    $state = get_state($_GET['id']);
    if (!isset($_SESSION['pattern'])){
        $_SESSION['pattern'] = [];
    }
    if (isset($_POST['color']) && count($_SESSION['pattern']) < count(get_pattern($_GET['id']))){
        $_SESSION['pattern'][] = $_POST['color'];
    }
    if (isset($_POST['back']) && $_SESSION['pattern'] != []){
        array_pop($_SESSION['pattern']);
    }
    if (isset($_POST['undo'])){
        $_SESSION['pattern'] = [];
    }
    if ($state === 1 && isset($_POST['valider']) && count($_SESSION['pattern']) === count(get_pattern($_GET['id']))){
        if ((int) $_SESSION['user']['ID'] != get_turn($_GET['id'])){
            $error = true;
        } else {
            $result = write_logs($_GET['id'], $_SESSION['user']['ID'], $_SESSION['pattern']);
            change_turn($_GET['id']);
            $_SESSION['pattern'] = [];
            change_state($_GET['id'], $result === false);
        }
    }

    // Gestion des messages de résultat
    if ($state === 0){
        if ($_SESSION['user']['ID'] == get_winner($_GET['id'])){
            $titre = 'Victoire !';
            $message = 'Vous avez remporté la partie.';
        } else {
            $titre = "Défaite";
            $message = 'Vous avez perdu la partie.';
        }
        change_state($_GET['id'], 0);
    } else {
        $titre = '';
        $message = '';
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mastermind</title>
        <link rel="stylesheet" href="styles/style-mastermind_game.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    </head>
    <body>
        <div id="overlay" onclick="hide('overlay'); hide('error_window'); hide('end_game')"></div>
        <div id="error_window">
            <div class="close" onclick="hide('overlay'); hide('error_window')"><span class="material-symbols-outlined">close</span></div>
            <h1>Erreur</h1>
            <p>Ce n'est pas votre tour !</p>
        </div>
        <div id="end_game">
            <div class="close" onclick="hide('overlay'); hide('end_game')"><span class="material-symbols-outlined">close</span></div>
            <h1><?= $titre ?></h1>
            <p><?= $message ?></p>
        </div>

        <header>
            <form action="mastermind_game" method="get">
                <button name="back"><span class="material-symbols-outlined">arrow_back</span> Retour</button>
                <button name="logout"><span class="material-symbols-outlined icon-logout">logout</span> Se déconnecter</button>
            </form>
        </header>
        
        <div class="game">
            <div class="logs">
                <div class="resultats">
                    <?php generate_colors($_SESSION['pattern']); ?>
                </div>
                <?php generate_logs((int) $_GET['id']) ?>
            </div>
            <div class="playground">
                <h1>Couleurs</h1>
                <form action="mastermind_game.php?id=<?= $_GET['id'] ?>" method="post">
                    <div class="ligne1 ligne">
                        <button class="couleur bleu" name="color" value="bleu"></button>
                        <button class="couleur rouge" name="color" value="rouge"></button>
                        <button class="couleur jaune" name="color" value="jaune"></button>
                    </div>
                    <div class="ligne2 ligne">
                        <button class="couleur violet" name="color" value="violet"></button>
                        <button class="couleur vert" name="color" value="vert"></button>
                        <button class="couleur orange" name="color" value="orange"></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bas">
            <form action="mastermind_game.php?id=<?= $_GET['id'] ?>" method="post">
                <button class="back" name="back"><span class="material-symbols-outlined">arrow_back</span></button>
                <button class="valider" name="valider">Valider</button>
                <button class="undo" name="undo"><span class="material-symbols-outlined">undo</span></button>
            </form>
        </div>

        <script src="scripts/script-mastermind.js"></script>
        <script>
            <?php if (isset($error) && $error === true){?>
                show('overlay');
                show('error_window');
                hide('end_game');
            <?php } else if ($titre != '') { ?>
                show('overlay');
                show('end_game');
                hide('error_window');
            <?php } else {?>
                hide('overlay');
                hide('error_window');
                hide('end_game');
            <?php }?>
        </script>
    </body>
</html>