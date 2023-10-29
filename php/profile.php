<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

require '../vendor/autoload.php';
require '../../mongoDriver/vendor/autoload.php'; // Include the MongoDB library
use Dotenv\Dotenv as Dotenv;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ .'/../');
$dotenv->load();
$mongoClient = new MongoDB\Client($_ENV["MONGO_URL"]);

// Select a database and a collection
$db = $mongoClient->selectDatabase('guvi');
$collection = $db->selectCollection('profiles');



if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Retrieve user profile data from the registration form
    $id = $_POST['id'];
    $username = $_POST['username'];

    // Example: Insert the user's profile data into the collection
    $userProfile = [
        'id' => $id,
        'username' => $username,
        'contact' => "",
        'age' => null, 
        'dob' => null,
    ];

    $collection->insertOne($userProfile);

    {// add in redis
        $myObject = [
            'username' => $username,
            'contact' => "",
            'age' => null, 
            'dob' => null,
        ];

        $serializedObject = json_encode($myObject);

        $redis = new Predis\Client();

        $key = $id;
        $serializedObject = json_encode($myObject);

        $redis->set($key, $serializedObject);

    }

} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    $email = $_GET['email'];
    $password = $_GET['password'];

    $redis = new Predis\Client();
    $redisKey = $id;

    $cachedData = $redis->get($redisKey);

    if ($cachedData !== null) {
  
        
        echo $cachedData;
    } else {

    // Connect to your MySQL database
    $host = "localhost";
    $name = "root";
    $pass = "";
    $database = "guvi"; 

    $connect = new mysqli($host, $name, $pass, $database);

    if ($connect -> connect_error) {
        die("Connection failed: " . $connect -> connect_error);
    } else{
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
            }else {
                //check password
                $user = $result->fetch_assoc();
                $storedPassword = $user['password'];
                
                if ($password== $storedPassword) {
                    if ($id) {
                        $filter = ['id' => $id]; 
                        
                        $projection = ['_id' => 0];
                        
                        $userProfile = $collection->findOne($filter, ['projection' => $projection]);
                        
                        echo json_encode($userProfile);
                    } else {
                        echo "Invalid request: Missing ID parameter";
                        
                    }
                }else {
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
    } else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
   // Retrieve the request body data
   $putData = file_get_contents("php://input");
   $data = json_decode($putData, true);

       $id = $data['id'];
       $username = $data['username'];
       $age = $data['age'];
       $dob = $data['dob'];
       $contact = $data['contact'];

       if ($id) {
        $filter = ['id' => $id]; // You should adjust the field name based on your MongoDB collection structure
        $updateData = [
            '$set' => [
                'username' => $username,
                'age' => $age,
                'dob' => $dob,
                'contact' => $contact,
            ]
        ];

        // Update the user's profile data in MongoDB
        $updateResult = $collection->updateOne($filter, $updateData);

        if ($updateResult->getModifiedCount() > 0) {
            // Profile data was updated successfully

            $myObject = [
                'username' => $username,
                'contact' => $contact,
                'age' => $age, 
                'dob' => $dob,
            ];

            $serializedObject = json_encode($myObject);

            $redis = new Predis\Client();

            $key = $id;
            $serializedObject = json_encode($myObject);

            $redis->set($key, $serializedObject);

            // Construct an array of age, dob, and contact
            $response = [
                'message'=> 'updated successfully',
                'username' => $username,
                'age' => $age,
                'dob' => $dob,
                'contact' => $contact,
            ];

            // Return the array as a JSON response
            echo json_encode($response);
        } else {
            echo "Failed to update profile data";
        }
    } else {
        echo "Invalid request: Missing ID parameter";
    }

} else {
    echo "Invalid request";
}


// Close MongoDB connection
$mongoClient = null;


?>
