<?php
session_start();
include "auth.php";
include("style/nav_bar.php");

// Form preprocessing
if ($_SERVER["REQUEST_METHOD"]=== "POST") {
$Name = trim($_POST[ "name"]);
$category = (int)$_POST[ "category_id"];
$subcategory = !empty($_POST['subcategory']) ? $_POST['subcategory'] : null;
$fitting = (int)$_POST["fit_id"];
$price = (float)$_POST["price"];
$size_id = (int)$_POST['size_id'];
$quantity =(int)$_POST["quantity"];
}

  // basic validation
  $errors = [];
  if ($item_name === '') $errors[] = 'Name required';
  if ($category_id <= 0) $errors[] = 'Category required';
  if ($size_id <= 0) $errors[] = 'Size required';
  if ($quantity < 0) $errors[] = 'Quantity invalid';
  if ($price < 0) $errors[] = 'Price invalid';


//Checking for the repeted product in inventory
$stmt = $conn-> prepare("SELECT id,quantity FROM inventory WHERE product_id= ? AND size_id= ? limit= 1  ");
