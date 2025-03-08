<?php

// simple roblox proxy, allows CORS so my JS in tarylem.com can send requests to it.

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$allowed_origins = ["https://www.tarylem.com", "https://tarylem.com"];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}

// preflight options, exits and returns the header for preflight.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// gets your secret and checks if it's correct, if it is, it converts my link to a valid ROBLOX API link and requests it, it sends it back to the server which requested this proxy. There's also a method "getGamepasses" for a donation game I was wanting to make. It gets all gamepasses of a user by user id and returns them with ID;ID;ID;...
$requiredSecret = ".....";
$secret = $_GET["Secret"];

if ($secret == $requiredSecret) {
    $url = $_SERVER['REQUEST_URI'];
    $url = str_replace("/v1/proxy/roblox.php/", "", $url);
    $url = explode("?", $url)[0];

    $explodedurl = explode("/", $url);

    if ($explodedurl[0] == "getGamepasses") {
        // Get All Gamepasses

        $string = "";

        $userid = (int) $explodedurl[1];
        $games = json_decode(file_get_contents("https://games.roblox.com/v2/users/$userid/games?limit=25", false), true);
        $multigeturl = "https://games.roblox.com/v1/games/multiget-place-details?";

        foreach ($games["data"] as $placeKEY => $placeDATA) {
            $placeid = $placeDATA["rootPlace"]["id"];
            $multigeturl = $multigeturl . "placeIds=$placeid&";
            $universeid = json_decode(file_get_contents("https://apis.roblox.com/universes/v1/places/$placeid/universe"), true);
            $gamepassinfo = json_decode(file_get_contents("https://games.roblox.com/v1/games/" . $universeid["universeId"] . "/game-passes?limit=100&sortOrder=Asc"), true);

            foreach ($gamepassinfo["data"] as $gamepassKEY => $gamepassDATA) {
                $string = $string . $gamepassDATA["id"] . ";";
            }
        }

        echo $string;

    } else {
        // Standard Procedure
        $newurl = "https://";

        for ($i = 0; $i < count($explodedurl); $i++) {
            $newurl .= $explodedurl[$i];
            if ($i == 0)
                $newurl .= ".roblox.com/";

            elseif ($i < count($explodedurl) - 1)
                $newurl .= "/";
        }

        $newurl .= "?";
        foreach ($_REQUEST as $key => $value) {
            if ($key === "Secret")
                continue;
            $newurl .= "$key=$value&";
        }

        $headers = ['Content-Type: application/json']; 

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $newurl );
        curl_setopt( $ch,CURLOPT_HTTPGET, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $response = curl_exec( $ch );
        curl_close( $ch );

        echo $response;
    }
}
