<?php
/**
 * @var $conn mysqli
 */
include __DIR__ . '/../db.php';

$dataraw = json_decode(file_get_contents('php://input'), true);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    $data = $_POST['data'];
}
elseif($_SERVER['REQUEST_METHOD'] === 'POST' && isset($dataraw['data'])) {
    $data = $dataraw['data'];
}
else{
    echo '{"msg" : "Direct access not allowed"}';
}

// Retrieving data from the server
if (isset($data)) {
    

    // If the retrieved data is empty.
    if (empty($data)) {
        $msg = 'Clip is empty.';
    // If the retrieved data exceeds 1000 characters
    } elseif (strlen($data) > 1000) {
        $msg = 'Clip must not exceed 1000 characters.';
    // If the data satisfies the condictions,inserting data to database
    } else {
        $data = htmlspecialchars($data);

        $sql = "INSERT INTO clips (clip) VALUES ('$data')";

        // Displaying success message.
        if (mysqli_query($conn, $sql)) {
            $msg = 'Clip added Sucessfully.';
        } else {
            $msg = "Error, Unable to add Clip.";
        }
    }
    echo '{"msg" : "' . $msg . '"}';
}




?>