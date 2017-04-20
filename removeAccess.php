<?PHP
$confPath = urldecode($_GET['conf']);
@unlink("$confPath.hash");
@unlink("index.php");
@unlink("generate.php");
@unlink("setup.sql");
@rename("tmp.htaccess", ".htaccess");
@unlink("WARNING.txt");
@unlink("removeAccess.php");
echo("done!");
?>