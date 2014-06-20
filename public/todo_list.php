<?php

require_once('classes/filestore.php');
// $filename = 'list.txt';
// $newitems = getfile($filename);
// Using the class filestore here

$store = new Filestore('list.txt');
$newitems = $store->read();
var_dump($store->is_csv);
$errorMessage = '';

// Verify there were uploaded files and no errors
if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) {

    if ($_FILES['file1']['type'] == 'text/plain') {

        // Set the destination directory for uploads
        $upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
        // Grab the filename from the uploaded file by using basename
        $filename = basename($_FILES['file1']['name']);
        // Create the saved filename using the file's original name and our upload directory
        $saved_filename = $upload_dir . $filename;
        // Move the file from the temp location to our uploads directory
        move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);

        // $textfile = $saved_filename;
        // $newfile = getfile($textfile);
        // $newitems = array_merge($newfile, $newitems);
        // refactored this code to use the Filestore class 

        $upload = new Filestore($saved_filename);
        $newTextFile = $upload->read();
        $newitems = array_merge($newitems, $newTextFile);

    } else {
        $errorMessage = "Not a valid file. Please use only a plain text file";
    }
}
  
if (!empty($_GET)) {
    //THIS IS THE SAME
    $removeindex = $_GET['removeitem'];
    unset($newitems[$removeindex]);
    // AS BELOW
    // unset($newitems[$_GET['removeitem']]);
}

if (!empty($_POST['todoitem'])) {
    array_push($newitems, htmlspecialchars(strip_tags($_POST['todoitem'])));
}

// $savefilepath = 'list.txt';
// savefile($savefilepath, $newitems);
$store->write($newitems);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Todo PHP</title>
    <link rel="stylesheet" href="/css/todo.css">
</head>
<body>

<h1>Todo List</h1>

<? if (!empty($errorMessage)) : ?>  
    <?= "<p>{$errorMessage}</p>";?>
<? endif; ?>

<ul>
<? foreach ($newitems as $index => $items) :?>
    <?= "<li>$items <a href='?removeitem=$index'>Mark Complete</a></li>";?>
<? endforeach; ?>
</ul>

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