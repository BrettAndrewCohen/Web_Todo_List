<!DOCTYPE html>
<html>

<head>
    <title>Todo PHP</title>
</head>
<body>
    <h1>Todo List</h1>
    <?php

function savefile($savefilepath, $array) {
    $filename = $savefilepath;
        if (is_writable($filename)) {
            $handle = fopen($filename, 'w');
                foreach($array as $items) {
                    fwrite($handle, PHP_EOL . $items);
                }
            fclose($handle); 
        }   
}


function getfile($filename) {
        $contents = [];
        if (is_readable($filename) && filesize($filename) > 0){
            $handle = fopen($filename, 'r');
            $bytes = filesize($filename);
            $contents = trim(fread($handle, $bytes));
            fclose($handle);
            $contents = explode("\n", $contents);

        }
        return $contents;
} 
    $filename = 'list.txt';
    $newitems = getfile($filename);
    // var_dump($_FILES);

    // array (size=1)
    //   'file1' => 
    //     array (size=5)
    //       'name' => string 'spurs.jpg' (length=9)
    //       'type' => string 'image/jpeg' (length=10)
    //       'tmp_name' => string '/tmp/phpdQ9DWq' (length=14)
    //       'error' => int 0
    //       'size' => int 191967


    // Verify there were uploaded files and no errors
    if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0 && $_FILES['file1']['type'] == 'text/plain') {
        // Set the destination directory for uploads
        $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
        // Grab the filename from the uploaded file by using basename
        $filename = basename($_FILES['file1']['name']);
        // Create the saved filename using the file's original name and our upload directory
        $saved_filename = $upload_dir . $filename;
        // Move the file from the temp location to our uploads directory
        move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    } 
    if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0 && $_FILES['file1']['type'] !== 'text/plain') {
        echo "Not a valid file. Please use only a plain text file";
    }

    // Check if we saved a file
    // if (isset($saved_filename)) {
    //     $textfile = $saved_filename;
    //     $newfile = getfile($textfile);
    //     var_dump($newfile);
    //     array_merge($newfile, $newitems);
    // }
  
    if (!empty($_GET)) {
        //THIS IS THE SAME
        $removeindex = $_GET['removeitem'];
        unset($newitems[$removeindex]);
        // AS BELOW
        // unset($newitems[$_GET['removeitem']]);
    }
    if (!empty($_POST['todoitem'])) {
        array_push($newitems, $_POST['todoitem']);
    }
    if (isset($saved_filename)) {
        $textfile = $saved_filename;
        $newfile = getfile($textfile);
        var_dump($newfile);
        $newitems = array_merge($newfile, $newitems);
    }
     foreach ($newitems as $index => $items) {
        echo "<li>$items <a href='?removeitem=$index'>Mark Complete</a></li>";
    }
    // echo "<br>";
    // echo "This is my GET array";
    // echo "<br>";
    // var_dump($_GET);
    // echo "<br>";
    // echo "*******************";
    // echo "<br>";
    // echo "This is my POST array";
    // echo "<br>";
    // var_dump($_POST);
    if (isset($saved_filename)) {
        $textfile = $saved_filename;
        $newfile = getfile($textfile);
        $newarray = array_merge($newfile, $newitems);
    }
  
    $savefilepath = 'list.txt';
    savefile($savefilepath, $newitems);

    ?>

<h1>Please add an item to do the todo list!</h1>
    <form method="POST" action="/todo_list.php">
            <p>
                <label for="todoitem">Add Todo Item</label>
                <input id="todoitem" name="todoitem" type="text" placeholder="Enter Your Item">
            </p>
                <input type="submit" value="Submit">
            </p>
    </form>

<h1>Upload File</h1>

<form method="POST" enctype="multipart/form-data">
    <p>
        <label for="file1">File to upload: </label>
        <input type="file" id="file1" name="file1">
    </p>
    <p>
        <input type="submit" value="Upload">
    </p>
</form>
</body>
</html>