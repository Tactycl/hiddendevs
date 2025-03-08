<?php

// used to remove GDPR data when user deletes their roblox account.

header("Content-Type: application/json");
$secret = "...";
$APIKey = "...";
$db = null;

// executes an SQL I send with the first ? being set as an integer with the $userId
function executeSQL($sql, $userId)
{
    global $db;
    if ($db === null) {
        echo json_encode(["status" => "error", "message" => "No database."]);
        exit();
    }

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

// deletes the datastore entry from roblox with a request set up with CURL.
function deleteDatastoreEntry($datastoreName, $scope, $universe, $key)
{
    global $APIKey;
    $url = "...";
    $ch = curl_init();

    $queryParams = http_build_query([
        "datastoreName" => $datastoreName,
        "entryKey" => $key,
        "scope" => $scope
    ]);

    curl_setopt($ch, CURLOPT_URL, "$url?$queryParams");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "x-api-key: $APIKey"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        echo "Response: $response";
    }

    curl_close($ch);
}

// Here it takes the $userId and $gameId, goes through them in a switch statement and depending on which it is, it deletes their user data from the datastores and my database
function deleteGDPR($userId, $gameId)
{
    global $db;
    
    switch ($gameId) {
        case 16867952270:
            // Dash World
            require "../../v2/dw/database.php";
            $universe = 5795666792;

            $sql_comments = "DELETE FROM comments WHERE UserId = ?";
            $sql_levels = "DELETE FROM levels WHERE UserId = ?";
            $sql_leaderboards = "DELETE FROM leaderboards WHERE UserId = ?";
            $sql_playerlevels = "DELETE FROM playerlevels WHERE UserId = ?";
            $sql_playerdata = "DELETE FROM playerdata WHERE UserId = ?";

            executeSQL($sql_comments, $userId);
            executeSQL($sql_levels, $userId);
            executeSQL($sql_leaderboards, $userId);
            executeSQL($sql_playerlevels, $userId);
            executeSQL($sql_playerdata, $userId);

            $db->close();

            deleteDatastoreEntry("Cloud", "PlayerData", $universe, $userId);
            break;

        case 15701159480:
            // Geometry Jump
            $universe = 5424305514;

            deleteDatastoreEntry("Cloud#1", "PlayerInformation", $universe, $userId);
            break;

        case 16764997873:
            // Grabpack System
            $universe = 5761825643;
            deleteDatastoreEntry("Cloud", "PlayerInfo", $universe, $userId);
            break;

        default:
            http_response_code(501);
            echo json_encode(["status" => "error", "message" => "Game Id not implemented."]);
    }
}

// Checks if the $header of the HTTP request is a VALID ROBLOX header (with secret)
function checkHeader($header)
{
    global $secret;
    return str_contains($header, "v1=$secret");
}

// Gets ALL HTTP headers and returns them
function getHeaders() {
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$headerName] = $value;
        }
    }
    return $headers;
}

// if it's a post (used by roblox) it gets all headers, if a roblox-signature header is set, it checks that header, then it gets the input from roblox and goes through all game ids and deletes the userid from all game ids sent. Else it gives some errors.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $headers = getHeaders();
    if (isset($headers["roblox-signature"])) {
        if (checkHeader($headers["roblox-signature"]) === true) {
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Invalid JSON format."]);
                exit();
            }

            if (
                isset($data["NotificationId"]) &&
                isset($data["EventType"]) &&
                isset($data["EventPayload"]["UserId"]) &&
                $data["EventType"] === "RightToErasureRequest" &&
                isset($data["EventPayload"]["GameIds"])
            ) {
                $notificationId = $data["NotificationId"];
                $eventType = $data["EventType"];
                $eventTime = $data["EventTime"];
                $userId = $data["EventPayload"]["UserId"];
                $gameIds = $data["EventPayload"]["GameIds"];

                foreach ($gameIds as $gameId) {
                    deleteGDPR($userId, $gameId);
                }

                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Request received and processed."
                ]);

            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Invalid request payload."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid header."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No signature header."]);
    }

} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
