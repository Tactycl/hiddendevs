<!DOCTYPE HTML>
<html>
    <head>
        <meta name="description" content="Roblox Dash World" />
        <meta name="keywords" content="Roblox, Tarylem, Game-Maker Studio, Games, Roblox Games, Racing Games, Dash World">
        <meta name="author" content="Tarylem, Tactical">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tarylem</title>
        <link rel="icon" type="image/x-icon" href="../img/favicon.ico"/>
        <link rel="stylesheet" href="../styles/gamepage.css">
        <link rel="stylesheet" href="../styles/fonts.css">
        <link rel="stylesheet" href="../styles/all.min.css">
    </head>
    <body>
        <!-- IGNORE -->
        <?php
            include "includes/checkban.php";

            $gameid = 16867952270;

            $gameuniverse = json_decode(file_get_contents("https://apis.roblox.com/universes/v1/places/$gameid/universe"), true)["universeId"];
            $GameData = json_decode(file_get_contents("https://games.roblox.com/v1/games?universeIds=$gameuniverse"));
            $GameVotes = json_decode(file_get_contents("https://games.roblox.com/v1/games/votes?universeIds=$gameuniverse"));

            $upVotes = (int)$GameVotes->data[0]->upVotes;
            $downVotes = (int)$GameVotes->data[0]->downVotes;

            function mathsign($n) {
                return ($n > 0) - ($n < 0);
            }

            function abbreviateNumber($num) {
                $sign = mathsign($num);
                $num = abs($num);

                $format = "";
                $suffix = "";
                $prefix = "";

                if ($sign < 0) {
                    $prefix = "-";
                }

                if($num >= 0 && $num < 1000){
                    $format = floor($num);
                    $suffix = "";
                } elseif($num >= 1000 && $num < 1000000) {
                    $format = floor($num / 10) / 100;
                    $suffix = "K";
                } elseif($num >= 1000000 && $num < 1000000000) {
                    $format = floor($num / 10000) / 100;
                    $suffix = "M";
                } elseif($num >= 1000000000 && $num < 1000000000000) {
                    $format = floor($num / 10000000) / 100;
                    $suffix = "B";
                }

                return !empty("$prefix$format$suffix") ? "$prefix$format$suffix" : 0;
            }

            $likes = abbreviateNumber($upVotes);
            $playing = abbreviateNumber((int)$GameData->data[0]->playing);
            $visits = abbreviateNumber((int)$GameData->data[0]->visits);
            $favorites = abbreviateNumber((int)$GameData->data[0]->favoritedCount);
        ?>

        <!-- game container, basically like steam. It has the thumbnail in the middle and a blurred version in the background and the game's icon on the left, see at https://www.tarylem.com/games/dashworld -->
        <div class="game-container">
            <div class="game-logo"><img src="../img/dashworldlogo.png"></div>
            <div class="blurred-thumbnail"><div class="blurred-thumbnail-img"></div></div>
            <div class="thumbnail"><img src="../img/dashworldthumbnail.png"></div>
        </div>

        <!-- game-view, it's just all the stats like playing, likes and visits and a PLAY button in green like steam. -->
        <div class="game-view">
            <div class="top-bar">
                <div class="top-bar-top">
                    <a href="https://www.roblox.com/games/16867952270" class="play-btn">Play</a>
                </div>
                <div class="top-bar-bottom">
                    <?php
                        echo "<div class='top-bar-item'>
                        <p class='top-bar-item-title'>Currently Playing:</p>
                        <p class='top-bar-item-description'>$playing</p></div>";

                        echo "<div class='top-bar-item'>
                        <p class='top-bar-item-title'>Likes:</p>
                        <p class='top-bar-item-description'>$likes</p></div>";

                        echo "<div class='top-bar-item'>
                        <p class='top-bar-item-title'>Visits:</p>
                        <p class='top-bar-item-description'>$visits</p></div>";
                    ?>
                </div>
            </div>

            <!-- community stuff like Owner, Moderators and my demon list (hardest levels in the game) -->
            <div class="community">
                <p class="title">Community</p>
                <div class="community-items">
                    <div class="list">
                        <p class="title">Demon List</p>
                        <!-- IGNORE -->
                        <?php
                            $demonlistItems = explode(":", file_get_contents("https://api.tarylem.com/v1/web/getList.php"));
                            foreach($demonlistItems as $iteration => $row){
                                $rowItems = explode(";", $row);
                                $id = $rowItems[1];
                                $name = $rowItems[2];
                                $creator = $rowItems[3];

                                if (count($rowItems) > 1 && strlen($rank) <= 3) {
                                    echo "<div class='list-item'>
                                        <p class='item-title'>#" . ($iteration + 1) . " - $name</p>
                                        <p class='item-description'>id - $id</p>
                                        <p class='item-context'>creator - $creator</p></div>";
                                }
                            }
                        ?>
                    </div>
                    <!-- all moderators in a list (FLEX list with divs) -->
                    <div class="list">
                        <p class="title">Moderators</p>
                        <div class="list-item">
                            <p class="item-title">Staff</p>
                            <p class="item-description">Elder Moderator</p>
                            <p class="item-context">Community Manager in Discord</p>
                        </div>
                        <div class="list-item">
                            <p class="item-title">Tyjohn</p>
                            <p class="item-description">Elder Moderator</p>
                        </div>
                        <div class="list-item">
                            <p class="item-title">beni</p>
                            <p class="item-description">Elder Moderator</p>
                        </div>
                        <div class="list-item">
                            <p class="item-title">Zlo</p>
                            <p class="item-description">Moderator</p>
                        </div>
                    </div>

                    <!-- Creators of the game ( me :p ) -->
                    <div class="list">
                        <p class="title">Creators</p>
                        <div class="list-item">
                            <p class="item-title">Tactycl</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
