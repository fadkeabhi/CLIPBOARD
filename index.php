<?php include 'db.php';
$msg = " ";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = $_POST['data'];
    if (empty($data)) {
        $msg = "Name is empty";
    } else {
        $data = htmlspecialchars($data);

        $sql = "INSERT INTO clips (clip) VALUES ('$data')";

        if (mysqli_query($conn, $sql)) {
            $msg = "Clip added successfully";
        } else {
            $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// limit of number of clips displayed
$limit = isset($_GET['show-limit']) ? $_GET['show-limit'] : 5;
?>

<!doctype html>
<html lang="en">
    <head>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>
            MY CLIPBOARD
        </title>

        <script>
// to make text copy function
            </script>
  <link rel="stylesheet" href="styles.css">
 
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

        <form action="" name="filter" method="GET">
            <label for="show-limit">Clips to show :</label>

            <select name="show-limit" id="show-limit">
                <option value="5" <?= $limit === '5' ? 'selected' : ''?>>5</option>
                <option value="10" <?= $limit === '10' ? 'selected' : ''?>>10</option>
                <option value="20" <?= $limit === '20' ? 'selected' : ''?>>20</option>
                <option value="50" <?= $limit === '50' ? 'selected' : ''?>>50</option>
                <option value="100" <?= $limit === '100' ? 'selected' : ''?>>100</option>
                <option value="all" <?= $limit === 'all' ? 'selected' : ''?>>All</option>
            </select>

            <button type="submit">Show</button>
        </form>

        <h4>
            <?php echo $msg;
            ?>
        </h4>


        <?php
        # showing recent clips
        if ($limit === 'all') {
            $sql = "SELECT clip FROM clips ORDER BY id DESC";
        } else {
            $sql = "SELECT clip FROM clips ORDER BY id DESC LIMIT $limit";
        }
        $result = mysqli_query($conn, $sql);
        $i = 1;

        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="clip"><br>';
                echo '<p id="clip' . $i . '">' . $row["clip"] . '</p><br></div><br>';
                //     echo '<a href="#" onclick="CopyToClipboard(#clip' . $i . ');return false;">📄</a><br>' ;
                ++$i;



    }
  } else {
    echo "0 results";
  }


?>
<br>


    </body>
</html>