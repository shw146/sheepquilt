<?php

header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'usertracking';
$username = 'shawnwang';
$password = '123456789';

$pdo = new PDO(
    "pgsql:host=$host;dbname=$dbname",
    $username,
    $password
);

$method = $_SERVER['REQUEST_METHOD'];

$id = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if ($method === 'GET') {
    try {
        // GET /api/userinformation.php?id=xxx
        if ($id !== null) {
            $stmt = $pdo->prepare(
                "SELECT *
                FROM userinformation
                WHERE id = :id"
            );

            $stmt->execute([
                ':id' => $id
            ]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                http_response_code(404);

                echo json_encode([
                    "error" => "User information not found"
                ]);
                exit;
            }

            echo json_encode($data);

        }

        // GET /api/userinformation
        else {

            $stmt = $pdo->query(
                "SELECT *
                 FROM userinformation
                 ORDER BY id"
            );

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($data);
        }

    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            "error" => $e->getMessage()
        ]);
    }

    exit;
}

if ($method === 'POST') {

    // Read JSON sent by fetch()
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!$input) {

        http_response_code(400);

        echo json_encode([
            "error" => "Invalid JSON"
        ]);

        exit;
    }

    try {

        $stmt = $pdo->prepare(
            "INSERT INTO userinformation
            (
                uuid,
                url,
                title,
                referrer,
                timestamp,
                type,
                staticInfo,
                performanceInfo,
                error,
                mouseData,
                keyPressed,
                idleInfo
            )
            VALUES
            (
                :uuid,
                :url,
                :title,
                :referrer,
                :timestamp,
                :type,
                :staticInfo,
                :performanceInfo,
                :error,
                :mouseData,
                :keyPressed,
                :idleInfo
            )
            RETURNING id"
        );

        $stmt->execute([
            ':uuid' => $input['uuid'] ?? null,
            ':url' => $input['url'] ?? null,
            ':title' => $input['title'] ?? null,
            ':referrer' => $input['referrer'] ?? null,
            ':timestamp' => $input['timestamp'] ?? null,
            ':type' => $input['type'] ?? null,
            ':staticInfo' => $input['staticInfo'] ?? null,
            ':performanceInfo' => $input['performanceInfo'] ?? null,
            ':error' => $input['error'] ?? null,
            ':mouseData' => $input['mouseData'] ?? null,
            ':keyPressed' => $input['keyPressed'] ?? null,
            ':idleInfo' => $input['idleInfo'] ?? null
        ]);

        $newId = $stmt->fetchColumn();

        http_response_code(201);

        echo json_encode([
            "message" => "Entry created successfully",
            "id" => $newId
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            "error" => "Failed to create entry"
        ]);
    }

    exit;
}

if ($method === 'PUT') {

    if ($id === null) {

        http_response_code(400);

        echo json_encode([
            "error" => "An ID is required for PUT"
        ]);

        exit;
    }

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!$input) {

        http_response_code(400);

        echo json_encode([
            "error" => "Invalid JSON"
        ]);

        exit;
    }

    try {

        $stmt = $pdo->prepare(
            "UPDATE userinformation
             SET
                uuid = :uuid,
                url = :url,
                title = :title,
                referrer = :referrer,
                timestamp = :timestamp,
                type = :type,
                staticInfo = :staticInfo,
                performanceInfo = :performanceInfo,
                error = :error,
                mouseData = :mouseData,
                keyPressed = :keyPressed,
                idleInfo = :idleInfo
             WHERE id = :id"
        );

        $stmt->execute([
            ':uuid' => $input['uuid'] ?? null,
            ':url' => $input['url'] ?? null,
            ':title' => $input['title'] ?? null,
            ':referrer' => $input['referrer'] ?? null,
            ':timestamp' => $input['timestamp'] ?? null,
            ':type' => $input['type'] ?? null,
            ':staticInfo' => $input['staticInfo'] ?? null,
            ':performanceInfo' => $input['performanceInfo'] ?? null,
            ':error' => $input['error'] ?? null,
            ':mouseData' => $input['mouseData'] ?? null,
            ':keyPressed' => $input['keyPressed'] ?? null,
            ':idleInfo' => $input['idleInfo'] ?? null,
            ':id' => $id
        ]);

        if ($stmt->rowCount() === 0) {

            http_response_code(404);

            echo json_encode([
                "error" => "Entry not found"
            ]);

            exit;
        }

        echo json_encode([
            "message" => "Entry updated successfully"
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            "error" => "Failed to update entry"
        ]);
    }

    exit;
}

if ($method === 'DELETE') {

    if ($id === null) {

        http_response_code(400);

        echo json_encode([
            "error" => "An ID is required for DELETE"
        ]);

        exit;
    }

    try {

        $stmt = $pdo->prepare(
            "DELETE FROM userinformation
             WHERE id = :id"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        if ($stmt->rowCount() === 0) {

            http_response_code(404);

            echo json_encode([
                "error" => "Entry not found"
            ]);

            exit;
        }

        echo json_encode([
            "message" => "Entry deleted successfully"
        ]);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            "error" => "Failed to delete entry"
        ]);
    }

    exit;
}

http_response_code(405);

echo json_encode([
    "error" => "Method not allowed"
]);

?>