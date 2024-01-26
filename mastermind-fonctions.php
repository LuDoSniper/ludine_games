<?php
    require_once 'objets.php';

    function get_games_by_userID(int $id): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_games = $bdd->prepare("SELECT * FROM mastermind WHERE player2_ID = ? OR player1_ID = ?");
        $select_games->execute([$id, $id]);
        $games = $select_games->fetchAll(PDO::FETCH_ASSOC);

        return $games;
    }

    function get_colors(): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_colors = $bdd->prepare("SELECT `name` FROM colors");
        $select_colors->execute();
        $colors = $select_colors->fetchAll(PDO::FETCH_ASSOC);

        $colors_format = [];
        foreach ($colors as $color){
            $colors_format[] = $color['name'];
        }

        return $colors_format;
    }

    function generate_pattern(int $longueur): array{
        $colors = get_colors();
        $pattern = [];

        for ($i = 0; $i < $longueur; $i++){
            $pattern[] = $colors[array_rand($colors)];
        }

        return $pattern;
    }

    function format_pattern(array $pattern){
        $format = '';
        foreach ($pattern as $color){
            $format.= $color.',';
        }

        return substr($format, 0, -1);
    }

    function create_game(int $userID1, int $userID2, int $nb = 4): bool{
        $pattern = generate_pattern($nb);
        $pattern = format_pattern($pattern);
        $turn = rand(1, 2);
        
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $insert_game = $bdd->prepare("INSERT INTO mastermind (player1_ID, player2_ID, pattern, turn) VALUES (?, ?, ?, ?)");
        $insert_game->execute([$userID1, $userID2, $pattern, $turn]);

        return true;
    }

    function display_games(): string{
        $games = get_games_by_userID($_SESSION['user']['ID']);

        $divs = '';
        foreach ($games as $game){
            echo '
            <div class="game game_id-'.$game['ID'].'" onclick="show(\'game_id-'.$game['ID'].'\'); show(\'overlay\')">
                <table>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>Adversaire : </td>
                        <td>'.get_user_by_ID($game['player1_ID'])['username'].'</td>
                    </tr>
                    <tr>
                        <td>Tour : </td>
                        <td>'.get_user_by_ID($game['turn'])['username'].'</td>
                    </tr>
                </table>
            </div>
            ';

            $divs .= '
            <div class="game-options" id="game_id-'.$game['ID'].'">
            <span class="material-symbols-outlined icon-close close" onclick="hide(\'form-create\'); hide(\'overlay\'); hide(\'game_id-'.$game['ID'].'\')">close</span>
                <table>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>Adversaire : </td>
                        <td>'.get_user_by_ID($game['player1_ID'])['username'].'</td>
                    </tr>
                    <tr>
                        <td>Tour : </td>
                        <td>'.get_user_by_ID($game['turn'])['username'].'</td>
                    </tr>
                </table>
                <div>
                    <button name="delete" value="'.$game['ID'].'"><i class="uil uil-trash-alt"></i></button>
                    <button name="play" value="'.$game['ID'].'"><span class="material-symbols-outlined icon-play">sports_esports</span> Jouer</button>
                </div>
            </div>
            ';
        }

        return $divs;
    }

    function get_user_by_ID(int $id){
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_user = $bdd->prepare("SELECT * FROM users WHERE ID = ?");
        $select_user->execute([$id]);
        $user = $select_user->fetch();

        return $user;
    }

    function get_users_except_one(int $id){
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();
        $select_users = $bdd->prepare("SELECT ID, username FROM users WHERE ID != ?");
        $select_users->execute([$id]);
        $users = $select_users->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    function generate_users_html_except_one(int $id): void{
        $users = get_users_except_one($id);

        foreach ($users as $user){
            echo '<option value="'.$user['ID'].'">'.$user['username'].'</option>';
        }
    }

    function get_games_ID(): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_games = $bdd->prepare("SELECT ID FROM mastermind");
        $select_games->execute();
        $games = $select_games->fetchAll(PDO::FETCH_ASSOC);

        return $games;
    }

    function delete_logs(int $game_id): void{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $delete_logs = $bdd->prepare("DELETE FROM mastermind_log WHERE game_ID = ?");
        $delete_logs->execute([$game_id]);
    }

    function delete_game_by_ID(int $id): void{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $delete_game = $bdd->prepare("DELETE FROM mastermind WHERE ID = ?");
        $delete_game->execute([$id]);
    }

    function game_exists(int $id): bool{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_game = $bdd->prepare("SELECT * FROM mastermind WHERE ID = ?");
        $select_game->execute([$id]);
        $game = $select_game->fetch();

        return $game != false;
    }

    function get_logs(int $id): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_logs = $bdd->prepare("SELECT * FROM mastermind_log WHERE game_ID = ? ORDER BY `date`");
        $select_logs->execute([$id]);
        $logs = $select_logs->fetchAll(PDO::FETCH_ASSOC);

        return $logs;
    }

    function generate_logs(int $id): void{
        $logs = get_logs($id);
        
        $tableau = '';
        foreach ($logs as $log){
            $tableau .= '<tr><td>'.get_user_by_ID($log['player_ID'])['username'].' :</td><td class="colonne2">'.$log['pattern'].'</td><td class="colonne3">'.$log['resultat'].'</td></tr>';
        }

        echo '<table><tr><th>Joueur</th><th class="colonne2">Essai</th><th>Résultat</th></tr>'.$tableau.'</table>';
    }

    function generate_colors(array $colors): void{
        foreach ($colors as $color){
            echo '<div class="couleur '.$color.'"></div>';
        }
    }

    function get_pattern(int $id): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_pattern = $bdd->prepare("SELECT pattern FROM mastermind WHERE ID = ?");
        $select_pattern->execute([$id]);
        $pattern = $select_pattern->fetch(PDO::FETCH_ASSOC);

        return explode(',', $pattern['pattern']);
    }

    function check_pattern(int $id, string $pattern): array{
        $colors = explode(',', $pattern);
        $ref = get_pattern($id);

        $check = true;
        $resultat = '';
        for ($i = 0; $i < count($ref); $i++){
            if ($i != 0){
                $resultat .= ',';
            }

            if (in_array($colors[$i], $ref)){
                if ($colors[$i] === $ref[$i]){
                    $resultat .= '2';
                } else {
                    $resultat .= '1';
                    $check = false;
                }
            } else {
                $resultat .= '0';
                $check = false;
            }
        }

        return [$resultat, $check];
    }

    function write_logs(int $game_id, int $user_id, array $pattern): bool{
        $pattern = implode(',', $pattern);
        $tmp = check_pattern($game_id, $pattern);
        $resultat = $tmp[0];
        $check = $tmp[1];

        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $insert_log = $bdd->prepare("INSERT INTO mastermind_log (game_ID, player_ID, pattern, resultat) VALUES (?, ?, ?, ?)");
        $insert_log->execute([$game_id, $user_id, $pattern, $resultat]);

        return $check;
    }

    function get_turn(int $game_id): int{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $get_turn = $bdd->prepare("SELECT turn FROM mastermind WHERE ID = ?");
        $get_turn->execute([$game_id]);
        $turn = $get_turn->fetch(PDO::FETCH_ASSOC)['turn'];

        return $turn;
    }

    function change_turn(int $game_id): void{
        $turn = get_turn($game_id);
        $turn = 3 - $turn;

        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $set_turn = $bdd->prepare("UPDATE mastermind SET turn = ? WHERE ID = ?");
        $set_turn->execute([$turn, $game_id]);
    }

    function get_winner(int $game_id){
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_winner = $bdd->prepare("SELECT player_ID FROM mastermind_log WHERE game_ID = ? ORDER BY `date` DESC LIMIT 1");
        $select_winner->execute([$game_id]);
        $winner = $select_winner->fetch(PDO::FETCH_ASSOC)['player_ID'];

        return $winner;
    }