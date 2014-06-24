<?php

// Get new instance of PDO object
$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo', 'brett', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";

//CREATED NEW TABLE 
// $query = 'CREATE TABLE todo_items (
//     id INT UNSIGNED NOT NULL AUTO_INCREMENT,
//     item VARCHAR(240) NOT NULL,
//     PRIMARY KEY (id)
// )';

// // Run query, if there are errors they will be thrown as PDOExceptions
// $dbc->exec($query);

$errorMessage = '';
  
if (!empty($_GET)) {
    $page = $_GET['page'];
}else {
    $page = 1;
}

$pageNext = $page + 1;
$pagePrev = $page - 1;

try {
    if (isset($_POST['todoitem'])) {
        $stringLength = strlen(($_POST['todoitem']));
        if ($stringLength == 0) {
            throw new InvalidInputException('You must enter an item, YO!');
        }
        if ($stringLength >= 240) {
            throw new InvalidInputException('Item cannot be longer than 240 characters');
        } else {
        //WRITE NEW ITEMS TO THE DATABASE!!!
			$stmt = $dbc->prepare('INSERT INTO todo_items (item) VALUES (:todoitem)');
			$stmt->bindValue(':todoitem', $_POST['todoitem'], PDO::PARAM_STR);
			$stmt->execute();
        }
    }
    if (isset($_POST['remove'])) {
    		$idToRemove = $_POST['remove'];
    		$stmt = $dbc->prepare('DELETE FROM todo_items WHERE id = ?');
    		$stmt->execute(array($idToRemove));

    }
} catch(InvalidInputException $e) {
    $msg = $e->getMessage() . PHP_EOL;
}
$limit = 10;
$offset = (($limit * $page) - $limit); 
$rowCount = $dbc->query('SELECT * FROM todo_items');
$stmt = $dbc->prepare("SELECT * FROM todo_items LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$newitems = $stmt->fetchAll(PDO::FETCH_ASSOC);

//include jquery cdn in scrpit tags in the view 
?>
<!DOCTYPE html>
<html>

<head>
    <title>Todo PHP</title>
    <link rel="stylesheet" href="/css/todo.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>
<div class="container">
<body>
<? if (isset($msg)) : ?>  
    <?= "<p>{$msg}</p>";?>
<? endif; ?>
<h1>Todo List</h1>

<? if (!empty($errorMessage)) : ?>  
    <?= "<p>{$errorMessage}</p>";?>
<? endif; ?>

<table class = "table table-striped">
<? foreach ($newitems as $index => $items) :?>
	<tr>
    <td><?= $items['item'];?></td>
    <td><button class="btn btn-danger btn-sm pull-right btn-remove" data-todo="<?= $items['id']; ?>">Remove</button></td>
	</tr>
<? endforeach; ?>
</table>

<ul class="pager">
<? if ($pagePrev > 0) : ?> 
<li class="previous"><?= "<a href='?page=$pagePrev'>Previous</a>";?></li>
<? endif ?> 
<? if ($rowCount->rowCount() > ($offset + $limit)) : ?> 
 <li class="next"><?= "<a href='?page=$pageNext'>Next</a>";?></li>
<? endif ?>
</ul>
<h1>Add an item to do the todo list:</h1>
<form method="POST" action="/todo_list_mysql.php">
    <p>
        <label for="todoitem">Add Todo Item</label>
        <input id="todoitem" name="todoitem" type="text" placeholder="Enter Your Item">
    </p>
   		
        <button class="btn btn-primary" type="submit">Submit</button>
    </p>
</form>
<form id="remove-form" action="/todo_list_mysql.php" method="post">
    <input id="remove-id" type="hidden" name="remove" value="">
</form>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script>
$('.btn-remove').click(function () {
    var todoId = $(this).data('todo');
    if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
        $('#remove-id').val(todoId);
        $('#remove-form').submit();
    }
});
</script>
</body>
</html>