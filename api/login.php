<?php
session_start();

include_once "../config/Database.php";
include_once "../model/User.php";
include_once "../helpers/jwt_helper.php";

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $user->email = $email;
        $userData = $user->login();

        if ($userData && password_verify($password, $userData['password'])) {
            $payload = [
                "id" => $userData['id'],
                "username" => $userData['username'],
                "email" => $userData['email']
            ];
            $token = JWTHandler::generateToken($payload);

            header("Location: ../home.php?success=Login successful&token=" . urlencode($token));
            exit;
        } else {
            header("Location: ../login.php?error=Invalid email or password");
            exit;
        }
    } else {
        header("Location: ../login.php?error=Incomplete form data");
        exit;
    }
} else {
    header("Location: ../login.php?error=Invalid request");
    exit;
}

if ($userData && password_verify($password, $userData['password'])) {
    $payload = [
        "id" => $userData['id'],
        "username" => $userData['username'],
        "email" => $userData['email']
    ];
    $token = JWTHandler::generateToken($payload);

    //  تسجيل الحدث
    $user->id = $userData['id'];
    $user->logFeatureUsage("login");

    header("Location: ../home.php?success=Login successful&token=" . urlencode($token));
    exit;
}

?>



/*header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../config/Database.php";
include_once "../model/User.php";
include_once "../helpers/jwt_helper.php";

$database = new Database();
$db = $database->connect();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $user->email = $data->email;
    $userData = $user->login();

    if ($userData && password_verify($data->password, $userData['password']))
    {
        $payload = ["id" => $userData['id'], "username" => $userData['username'], "email" => $userData['email']];
        $token = JWTHandler::generateToken($payload);

        echo json_encode(["message" => "Login successful.", "token" => $token]);
    } else {
        echo json_encode(["message" => "Invalid email or password."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
?>*/
