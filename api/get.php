<?php
/**
 * @var $conn mysqli
 */
include __DIR__ . '/../db.php';
header('Content-Type: application/json'); 
$limit = $_GET['limit'] ?? '5';

$sql = "SELECT id, clip, created_at FROM clips ORDER BY id DESC LIMIT $limit";
$result = mysqli_query($conn, $sql);

$jsondata = '{"data":[';
if (mysqli_num_rows($result) > 0): 
    $i = false;
    while ($row = mysqli_fetch_assoc($result)):
        if($i)
        {
            $jsondata .= ",";
        }
        else{
            $i = true;
        }
        $jsondata .= '{"id":' . $row["id"] . ',"c":"' . $row["created_at"] . '","d":"' . $row["clip"] . '"}';
    endwhile;
else: 
    //<p>0 results</p>
endif;

$jsondata .= "]}";

echo preg_replace("/[\n\r]/","\\n",$jsondata);



// echo json_encode($jsondata,JSON_PRETTY_PRINT);
// echo $jsondata;
