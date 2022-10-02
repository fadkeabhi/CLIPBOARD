<?php include 'db.php';
$msg = " ";
#Retrieving data from the server
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = $_POST['data'];
    #If the retrieved data is empty.
    if (empty($data)) {
        $msg = "Clip is Empty.";
    #If the retrieved data exceeds 1000 characters
    } else if(strlen($data) > 1000) {
        $msg = "Clip must not exceed 1000 characters.";
   #If the data satisfies the condictions,inserting data to database
    } else {
        $data = htmlspecialchars($data);

        $sql = "INSERT INTO clips (clip) VALUES ('$data')";
        #Displaying success message.
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

        <script src="index.js">
// to make text copy function
            </script>
  <link rel="stylesheet" href="styles.css">
 
    </head>

    <nav>
  <div class="theme-switch-wrapper">
    <label class="theme-switch" for="checkbox">
    <input type="checkbox" id="checkbox" />
    <div class="slider round"></div>
  </label>
    <em>Switch</em>
  </div>
</nav>

    <body>
        <h1>
            CLIPBOARD
        </h1>

        <h2>
            MAKE NEW CLIP
        </h2>

        <div class="container">
        <form action="" name="submit" method="post">
            <textarea name="data" rows=5 cols=70 placeholder="Add Clip Content"></textarea>
            <input type="submit" value="Submit">
        </form>
        </div>

        <h2>
            LATEST CLIPS
        </h2>
        <div class="container">
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
        </div>
        <h4>
            <?php echo $msg;
            ?>
        </h4>

        <?php
        # showing recent clips
        if(in_array($limit, ['5', '10', '20', '50', '100'], true)) {
            $sql = "SELECT clip, created_at FROM clips ORDER BY id DESC LIMIT $limit";
        } else {
            $sql = "SELECT clip, created_at FROM clips ORDER BY id DESC";
        }
        $result = mysqli_query($conn, $sql);
        $i = 1;

        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="clip"><br>';
                echo '<p id="created_at' . $i . '"> Created at :' . $row["created_at"] . '</p>';
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
