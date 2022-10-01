<?php include 'db.php';
$msg = " ";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST['data'];
    if (empty($data)) {
      $msg = "Name is empty";
    } else {
        $data = htmlspecialchars($data);

      $sql = "INSERT INTO clips (clip)
VALUES ('$data')";

if (mysqli_query($conn, $sql)) {
    $msg = "Clip added successfully";
  } else {
    $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
  }

  
    }
  }

?>


<html>
    <head>
        <meta charset="utf-8"/>
        <title>
            MY CLIPBOARD
        </title>

        <script>
// to make text copy function
            </script>

    <style>
        .clip{
        
        border-style: hidden;
        border-radius: 10px;
        border-width: 2px;
        background-color: aquamarine;
    min-height: 5ch;}

    p{
        position : relative;
        left : 8px;
        width : 97%;
    }

    </style>
    </head>

    <body>
        <h1>
            CLIPBOARD
        </h1>

        <h2>
    MAKE NEW CLIP
</h2>
        <form action="" name="submit" method="post">
            <textarea name="data"></textarea>
            <input type="submit" value="Submit">
        </form>

        <h2>
            LATEST CLIPS
        </h2>

        <h4>
            <?php echo $msg;
            ?>
        </h4>

        
<?php
# showing recent clips
$limit = 5;
$sql = "SELECT clip FROM clips ORDER BY id DESC LIMIT $limit";
$result = mysqli_query($conn, $sql);
$i = 1;

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      echo '<div class="clip"><br>';
      echo '<p id="clip' . $i . '">' . $row["clip"] . '</p><br></div><br>';
 //     echo '<a href="#" onclick="CopyToClipboard(#clip' . $i . ');return false;">📄</a><br>' ;
      $i = $i + 1;



    }
  } else {
    echo "0 results";
  }


?>
<br>


    </body>
</html>