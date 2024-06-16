<?php
// Include the database connection
include_once "database.php";

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data
    $postData = file_get_contents('php://input');

    // Decode the JSON data
    $enrolledSubjects = json_decode($postData, true);

    // Validate the data (you may want to add more validation)
    if (!empty($enrolledSubjects) && is_array($enrolledSubjects)) {
        // Prepare a statement to insert the enrolled subjects into the database
        $stmt = $mysqli->prepare("INSERT INTO enrolled_subjects (subject_id) VALUES (?)");

        // Bind parameter
        $stmt->bind_param("s", $subjectID);

        // Insert each enrolled subject into the database
        foreach ($enrolledSubjects as $subjectID) {
            // Assign value to parameter
            $subjectID = $subjectID;

            // Execute the statement
            $stmt->execute();
        }

        // Close the statement
        $stmt->close();

        // Send a response back
        http_response_code(200);
        echo json_encode(array("success" => true));
    } else {
        // Send a response back indicating a bad request
        http_response_code(400);
        echo json_encode(array("message" => "Bad request"));
    }
} else {
    // Send a response back indicating a method not allowed
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}

// Close the database connection
$mysqli->close();
?>
