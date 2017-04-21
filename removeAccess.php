<?PHP
$confPath = urldecode($_GET['conf']);
@unlink("$confPath.hash");
@unlink("index.php");
@unlink("generate.php");
@rename("tmp.htaccess", ".htaccess");
@unlink("WARNING.txt");
@unlink("removeAccess.php");

if (file_exists ("setup.conf")) @unlink("setup.conf");
if (file_exists ("setup.sql"))  @unlink("setup.sql");
if (file_exists ("users.sql"))  @unlink("users.sql");
if (file_exists ("../setup.conf")) @unlink("../setup.conf");
if (file_exists ("../setup.sql"))  @unlink("../setup.sql");
if (file_exists ("../users.sql"))  @unlink("../users.sql");

echo("done!");
?>