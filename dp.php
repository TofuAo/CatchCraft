<?php
$conn = mysqli_connect("localhost", "root", "", "master_fisher");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
