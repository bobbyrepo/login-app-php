<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

require '../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    
    // Validate data
    if (empty($username) || empty($email) || empty($password)) {
        echo "Please fill in all the fields.";
    } else {
            // Access the environment variables
            $dbHost = $_ENV['DB_HOST'];
            $dbUsername = $_ENV['DB_USERNAME'];
            $dbPassword = $_ENV['DB_PASSWORD'];
            $dbName = $_ENV['DB_NAME'];

            // Use the variables in your database connection code
            $connect = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
            
            if ($connect->connect_error) {
                die("Connection failed: " . $connect->connect_error);
            } else {
                // Check if the user with the specified ID exists in MySQL
                $checkTable = "SHOW TABLES LIKE 'registeration'";
                $tableCheckResult = $connect->query($checkTable);

                if ($tableCheckResult->num_rows == 0) {
                    // The 'registeration' table does not exist; create it
                    $createTableSQL = "CREATE TABLE registeration (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        password VARCHAR(255) NOT NULL
                        )";

                    if ($connect->query($createTableSQL)) {
                        echo "The 'registeration' table has been created.";
                    } else {
                        echo "Failed to create the 'registeration' table: " . $connect->error;
                    }
                } 
                    // Check if the email already exists
           $checkEmail = "SELECT * FROM registeration WHERE email = ?";
           $stmt = $connect->prepare($checkEmail);
           $stmt->bind_param("s", $email);
           $stmt->execute();
           $result = $stmt->get_result();
           
           if ($result->num_rows > 0) { 
                $response = array(
                    'message' => "User exists",
                );
            
                echo json_encode($response);
                } else {

                    // Email doesn't exist, proceed with registration
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $connect->prepare("INSERT INTO registeration (username, email, password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $email, $hashedPassword);

                    if ($stmt->execute()) {
                        // Get the ID of the newly inserted record
                        $insertedId = $connect->insert_id;

                        $response = array(
                            'message' => 'Registeration successful',
                            'id' => $insertedId,
                        );

                        echo json_encode($response);
                    } else {
                        echo "Registration failed: " . $stmt->error;
                    }
                }

            // Close the connection
            $stmt->close();
            $connect->close();
        }

    }
} else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Retrieve the request body data
    $putData = file_get_contents("php://input");
    $data = json_decode($putData, true);
 
    $id = intval($data['id']);
    $updatedUsername = $data['username']; // Corrected variable name

    // Connect to your MySQL database
    $host = "localhost";
    $name = "root";
    $pass = "";
    $database = "guvi"; 
 
    $connect = new mysqli($host, $name, $pass, $database);

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    } else {
        // Check if the user with the specified ID exists in MySQL
        $checkId = "SELECT * FROM registeration WHERE id = ?";
        $stmt = $connect->prepare($checkId);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User with the specified ID exists, update the username
            $updateUsername = "UPDATE registeration SET username = ? WHERE id = ?";
            $stmt = $connect->prepare($updateUsername);
            $stmt->bind_param("si", $updatedUsername, $id);
            if ($stmt->execute()) {
                echo "Username updated successfully.";
            } else {
                echo "Failed to update username: " . $stmt->error;
            }
        } else {
            echo "User with the provided ID does not exist in the database.";
        }

        // Close the MySQL connection
        $stmt->close();
        $connect->close();
    }
} else {
    echo "Invalid request";
}

?>
