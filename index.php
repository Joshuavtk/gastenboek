<?php
$dbc = new PDO('mysql:host=localhost;dbname=22288_gastenboek', 'root', '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gastenboek</title>
</head>
<body>

<form method="post" action="index.php">
    <label for="gastboek-naam">Uw naam</label>
    <input type="text" name="gastboek_name" id="gastboek-naam"><br>
    <label for="gastboek-body">Uw bericht</label>
    <textarea name="gastboek_body" id="gastboek-body"></textarea><br>
    <p>Geweigerde woorden: shit, fuck, poep, kut, knakworst, dumbass, donald trump</p>
    <input type="submit" name="gastboek_submit" value="SEND">
</form>
<?php
if (isset($_POST['gastboek_submit'])) {

        if (strlen($_POST['gastboek_name']) < 50) {
            $name = $_POST['gastboek_name'];
        } else {
            die("generic error message 1");
        }
        if (strlen($_POST['gastboek_body']) < 500) {
            $body = $_POST['gastboek_body'];
            $bad_words = ['shit', 'fuck', 'poep', 'kut', 'knakworst', 'dumbass', 'donald trump'];
            foreach ($bad_words as $bad_word) {
                if (preg_match('%' . $bad_word . '%', $body)) {
                    $body = preg_replace('%' . $bad_word . '%', '*****' , $body);
                }
            }
        } else {
            die("generic error message 2");
        }

        $stmt = $dbc->prepare("INSERT INTO berichten VALUES (0,?,?,0)");
    
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $body);
    
        $stmt->execute() or die('Error querying after PDO');
    
    }
if (isset($_POST['gastenboek_set_safe'])) {
    
    $id = $_POST['bericht_id'];

    $stmt = $dbc->prepare("UPDATE berichten SET is_safe=1 WHERE id=$id");
    
    $stmt->execute() or die('Error querying after PDO');

}
if (isset($_POST['gastenboek_delete'])) {
    
    $id = $_POST['bericht_id'];

    $stmt = $dbc->prepare("DELETE FROM berichten WHERE id=$id");
    
    $stmt->execute() or die('Error querying after PDO');

}
?>
    
<h1>De berichten van andere mensen</h1>

<?php

$stmt = $dbc->prepare("SELECT * FROM berichten WHERE is_safe = 1");

$stmt->execute();

while ($row = $stmt->fetch()) {
    echo "<h3>Naam " . $row['name'] . "</h3>";
    echo "Bericht: " . $row['body'];
    echo "<br>";
}
?>

<h1>Onveilige berichten</h1>

<?php

$stmt = $dbc->prepare("SELECT * FROM berichten WHERE is_safe = 0");

$stmt->execute();

while ($row = $stmt->fetch()) {
    echo "<h3>Naam " . $row['name'] . "</h3>";
    echo "<p>Bericht: " . $row['body'] . "</p>";
    echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "' >";
    echo "<input type='hidden' value='" . $row['id'] ."' name='bericht_id'>";
    echo "<input type='submit' value='Zet bericht als veilig' name='gastenboek_set_safe' >";
    echo "<input type='submit' value='Verwijder bericht' name='gastenboek_delete' >";
    echo "</form>";
    echo "<br>";
}

?>

<?php
$dbc = null;
$stmt = null;
?>

</body>
</html>