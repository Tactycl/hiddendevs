<?php
    require "database.php";
    require "global_functions.php";
    header("Content-Type: application/json");

    // function to post a comment, uses SQL and mysqli for safe inserting, returns a decoded JSON.
    function PostComment($id, $text, $userid) {
        global $db;
        $timestamp = time();

        $sql_insert = "INSERT INTO comments (`LevelId`, `Text`, `Likes`, `UserId`, `Pinned`, `UnixTime`) VALUES (?, ?, 0, ?, 0, ?)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bind_param("isii", $id, $text, $userid, $timestamp);
        $stmt_insert->execute();
        $stmt_insert->close();
        $db->close();
        
        echo json_encode(["status" => "success", "message" => "Successfully posted comment."]);
    }

    // Adds $increment to likes of a comment
    function LikeComment($id, $increment) {
        global $db;

        $sql_increment = "UPDATE comments SET Likes = Likes + ? WHERE CommentId = ?";
        $stmt_increment = $db->prepare($sql_increment);
        $stmt_increment->bind_param("ii", $increment, $id);
        $stmt_increment->execute();
        $stmt_increment->close();
        $db->close();

        echo json_encode(["status" => "success", "message" => "Successfully liked/disliked comment."]);
    }

    // deletes a comment by comment id
    function DeleteComment($id) {
        global $db;

        $sql_delete = "DELETE FROM comments WHERE CommentId = ? LIMIT 1";
        $stmt_delete = $db->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        $stmt_delete->close();
        $db->close();

        echo json_encode(["status" => "success", "message" => "Successfully deleted comment."]);
    }

    // sets pinned to $pinned in a comment
    function PinComment($id, $pinned) {
        global $db;

        $sql_pin = "UPDATE comments SET Pinned = ? WHERE CommentId = ? LIMIT 1";
        $stmt_pin = $db->prepare($sql_pin);
        $stmt_pin->bind_param("ii", $pinned, $id);
        $stmt_pin->execute();
        $stmt_pin->close();
        $db->close();

        echo json_encode(["status" => "success", "message" => "Successfully pinned/unpinned comment."]);
    }

    // gets the body and decodes the JSON, if no secret, request or data is sent it exits.
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    if (!array_key_exists("secret", $body) || !array_key_exists("request", $body) || !array_key_exists("data", $body))
        exit();

    // gets all elements and checks if secret is valid
    $request = $body["request"];
    $bodySecret = $body["secret"];
    $data = $body["data"];

    if ($secret !== $bodySecret)
        exit();

    // switch statement for all "REQUEST" types. No need to explain, basically all of it is SQL. No idea if that counts but whatever.
    switch ($request) {
        case "Upload":
            if (!array_key_exists("id", $data) || !array_key_exists("message", $data) || !array_key_exists("userid", $data)) exit();
            PostComment($data["id"], $data["message"], $data["userid"]);
            break;

        case "Like":
            if (!array_key_exists("commentid", $data)) exit();
            LikeComment($data["commentid"], 1);
            break;

        case "Dislike":
            if (!array_key_exists("commentid", $data)) exit();
            LikeComment($data["commentid"], -1);
            break;

        case "Delete":
            if (!array_key_exists("commentid", $data)) exit();
            DeleteComment($data["commentid"]);
            break;

        case "Pin":
            if (!array_key_exists("commentid", $data) || !array_key_exists("pinned", $data)) exit();
            PinComment($data["commentid"], $data["pinned"]);
            break;

        case "Get":
            if (!array_key_exists("commentid", $data)) exit();
            $sql = "SELECT comments.LevelId, comments.Text, comments.Likes, comments.UserId, comments.Pinned, comments.UnixTime, COALESCE(playerdata.Username, 'Unknown Dasher') AS Username
                FROM comments
                LEFT JOIN playerdata
                ON comments.UserId = playerdata.UserId
                WHERE comments.CommentId = ? LIMIT 1";
            echo json_encode(fetchData($sql, "i", [$data["commentid"]])[0]);
            break;

        case "GetUser":
            if (!array_key_exists("userid", $data)) exit();
            $sql = "SELECT comments.CommentId, comments.LevelId, comments.Text, comments.Likes, comments.Pinned, comments.UnixTime, COALESCE(playerdata.Username, 'Unknown Dasher') AS Username
                FROM comments
                LEFT JOIN playerdata
                ON comments.UserId = playerdata.UserId
                WHERE comments.UserId = ?
                ORDER BY comments.Pinned DESC, comments.CommentId DESC";
                echo json_encode(fetchData($sql, "i", [$data["userid"]]));
                break;

        case "GetComments":
            if (!array_key_exists("id", $data)) exit();
            $sql = "SELECT comments.CommentId, comments.Text, comments.Likes, comments.UserId, comments.Pinned, comments.UnixTime, COALESCE(playerdata.Username, 'Unknown Dasher') AS Username
                FROM comments
                LEFT JOIN playerdata
                ON comments.UserId = playerdata.UserId
                WHERE comments.LevelId = ?
                ORDER BY comments.Pinned DESC, comments.CommentId DESC
                LIMIT ? OFFSET ?";
            echo json_encode(fetchData($sql, "iii", [$data["id"], $chunkSize, getCursorOffset()]));
            break;

        case "GetTopComments":
            if (!array_key_exists("id", $data)) exit();
            $sql = "SELECT comments.CommentId, comments.Text, comments.Likes, comments.UserId, comments.Pinned, comments.UserId, COALESCE(playerdata.Username, 'Unknown Dasher') AS Username
                FROM comments
                LEFT JOIN playerdata
                ON comments.UserId = playerdata.UserId
                WHERE comments.LevelId = ?
                ORDER BY comments.Pinned DESC, comments.Likes DESC
                LIMIT ? OFFSET ?";
            echo json_encode(fetchData($sql, "iii", [$data["id"], $chunkSize, getCursorOffset()]));
            break;
    }
