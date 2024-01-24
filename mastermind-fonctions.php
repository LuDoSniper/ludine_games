<?php
    require_once 'objets.php';

    function get_games_by_userID(int $id): array{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $select_games = $bdd->prepare("SELECT * FROM mastermind WHERE player2_ID = ?");
        $select_games->execute([$id]);
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

    function generate_patern(int $longueur): array{
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
        $pattern = generate_patern($nb);
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

    function delete_game_by_ID(int $id): void{
        $bdd = new BDD();
        $bdd = $bdd->get_bdd();

        $delete_game = $bdd->prepare("DELETE FROM mastermind WHERE ID = ?");
        $delete_game->execute([$id]);
    }