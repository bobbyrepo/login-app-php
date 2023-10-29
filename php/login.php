<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

       // Validate data
       if (empty($email) || empty($password)) {
            echo "Please fill in all the fields.";
        } else {
            // Connect to your MySQL database
            $host = "localhost";
            $name = "root";
            $pass = "";
            $database = "guvi"; 

            $connect = new mysqli($host, $name, $pass, $database);

            if ($connect -> connect_error) {
                die("Connection failed: " . $connect -> connect_error);
            } else {

            // Check if the email already exists
                $checkEmail = "SELECT * FROM registeration WHERE email = ?";
                $stmt = $connect->prepare($checkEmail);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) { 
                    $response = array(
                        'message' => "user does not exists",
                 
                    );

                    echo json_encode($response);

                    } else {
                        //check password
                        $user = $result->fetch_assoc();
                        $storedPassword = $user['password'];


                        if (password_verify($password, $storedPassword)) {

                            $response = array(
                                'message' => 'Login successful',
                                'user' => $user,
                            );
                            
                            echo json_encode($response);
                        } else {
                            $response = array(
                                'message' => "Incorrect password",
                            );
                            
                            echo json_encode($response);
                        }
                    }

                // Close the connection
                $stmt->close();
                $connect->close();
                }
        }
    } else {
        echo "Invalid request";
    }

?>