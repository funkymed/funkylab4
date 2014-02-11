<?php

	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";

  // images directory
  $images_dir = isset($_POST['path']) ? $offset.$_POST['path'] : $offset."data/";
  // return response
  function jsonResponse($success, $message)  {
    echo "{success: '$success', message: '$message'}";
    exit(0);
  }
  // verify images directory exists
  if (!is_dir("$images_dir")) {
  	jsonResponse("false", "$images_dir does not exist");
  }
  // verify name of image is posted
  if (!isset($_POST["image"])) {
    jsonResponse("false", "No file to delete");
  }
  // get name of image file to delete
  $file_name = $_POST["image"];
  // verify file exists
  if (!file_exists($images_dir.$file_name)) {
    jsonResponse("false", "$file_name does not exist");
  }
  // delete the file
  unlink($images_dir.$file_name);
  // report success
  jsonResponse("true", "$file_name deleted successfully");
?>
