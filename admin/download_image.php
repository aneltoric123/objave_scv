<?php
session_start();
require_once '../connection.php';
require_once '../vendor/autoload.php'; // Include PHPWord library

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

// Check if the user is logged in and has the role of an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: ../login/login.php");
    exit();
}

// Check if the post ID is specified in the query string
if (!isset($_GET['id'])) {
    header("Location: home_admin.php");
    exit();
}

$id = $_GET['id'];

// Check if the post ID is valid
$stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);
if ($row[0] < 1) {
    header("Location: home_admin.php");
    exit();
}

// Fetch post data from the database
$stmt = mysqli_prepare($link, "SELECT naslov, besedilo, datum FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$post = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Create a new instance of PhpWord
$phpWord = new PhpWord();

// Add a section to the document
$section = $phpWord->addSection();

// Add content to the section
$section->addText("{$post['naslov']}");
$section->addText("{$post['besedilo']}");
$section->addText("{$post['datum']}");

// Save the document as a .docx file
$docFilename = $post['naslov'] . ".docx";
$docxWriter = IOFactory::createWriter($phpWord, 'Word2007');
$docxWriter->save($docFilename);

// Create a ZIP file containing all images for the post and the docx file
$zip = new ZipArchive();
$zipFilename = $post['naslov'] . ".zip";
$zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$stmt = mysqli_prepare($link, "SELECT path FROM photos WHERE post_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_array($result)) {
    $image_data = $row[0];
    $image_file_name = uniqid() . ".jpg";
    $zip->addFromString($image_file_name, $image_data);
}

// Add the docx file to the ZIP file
$zip->addFile($docFilename, $docFilename);

$zip->close();

// Download the ZIP file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename=' . $zipFilename);
header('Content-Length: ' . filesize($zipFilename));
readfile($zipFilename);

// Delete the ZIP and docx files
unlink($zipFilename);
unlink($docFilename);
?>
