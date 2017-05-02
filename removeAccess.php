<?PHP
$confPath = urldecode($_GET['conf']);
@unlink("$confPath.hash");
@unlink("../setup.conf");
@unlink("../setup.sql");
@unlink("../users.sql");

/* Recursively delete a Folder */
function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        return;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    @rmdir($dirPath);
}

deleteDir("../setup");
echo("done!");
?>