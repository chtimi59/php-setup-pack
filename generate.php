<?php include('header.php'); ?>

<html>
<head>
<style>
    body     {font-family: monospace; }
    tr       {  }
    td       { padding:10px; }
    .error   {background-color:red; color:white; padding:10px;}
    .success {background-color:green; color:white; padding:10px;}
</style>
</head>
<body>

<script>
function goBack() { window.history.back(); }
var elems = document.getElementsByClassName('confirmation');
var confirmIt = function (e) {
    if (!confirm('Are you sure?')) e.preventDefault();
};
for (var i = 0, l = elems.length; i < l; i++) {
    elems[i].addEventListener('click', confirmIt, false);
}
</script>

<h1><?php echo $title; ?></h1>
<h2>Step 2 : Generate configuration file</h2>


<?php
$SUCESS_SETUP = false;
//error_reporting(0);
function printOK($str) { echo "<tr><td>$str</td><td class='success'>OK</td>\n"; }
function printKO($str) { echo "<tr><td>$str</td><td class='error'>KO</td>\n"; }
echo "<table>\n";
$DATA = array();
if(true){try{do
{   
    if (!isset ($_POST['conf_path'])) { printKO("conf_path missing!"); break; } 
    if (empty($_POST['conf_path'])) { printKO("conf_path empty!"); break; } 
    $confPath = $_POST['conf_path'];
    $hashPath = "$confPath.hash";
    if (!isset ($_POST['conf_hash'])) { printKO("conf_hash missing!"); break; } 
    if (empty($_POST['conf_hash'])) { printKO("conf_hash empty!"); break; } 
    $secretHash = $_POST['conf_hash'];

    if (!isset ($_POST['base_url'])) { printKO("base_url missing!"); break; } 
    if (empty($_POST['base_url'])) { printKO("base_url empty!"); break; } 
    $DATA['base_url'] = $_POST['base_url'];
    
    if ($setup['features']['db']) {
        if (!isset ($_POST['sql_host'])) { printKO("sql_host missing!"); break; } 
        if (empty($_POST['sql_host'])) { printKO("sql_host empty!"); break; } 
        $DATA['sql_host'] = $_POST['sql_host'];
        if (!isset ($_POST['sql_login'])) { printKO("sql_login missing!"); break; } 
        if (empty($_POST['sql_login'])) { printKO("sql_login empty!"); break; } 
        $DATA['sql_login'] = $_POST['sql_login'];
        if (!isset ($_POST['sql_pw'])) { printKO("sql_pw missing!"); break; } 
        $DATA['sql_pw'] = $_POST['sql_pw'];
        $DATA['sql_isPW'] = empty($_POST['sql_pw'])?false:true;
        if (!isset ($_POST['sql_db'])) { printKO("sql_db missing!"); break; }
        if (empty($_POST['sql_db'])) { printKO("sql_db empty!"); break; }  
        $DATA['sql_db'] = $_POST['sql_db'];
    }
    
    if ($setup['features']['user']) {
        if (!isset ($_POST['user_table'])) { printKO("user_table missing!"); break; } 
        if (empty($_POST['user_table'])) { printKO("user_table empty!"); break; } 
        $DATA['user_table'] = $_POST['user_table'];
    }
    
    if ($setup['features']['admin']) {
        if (!isset ($_POST['admin_email'])) { printKO("admin_email missing!"); break; } 
        if (empty($_POST['admin_email'])) { printKO("admin_email empty!"); break; }  
        $DATA['admin_email'] = $_POST['admin_email'];
        if (!isset ($_POST['admin_uuid'])) { printKO("admin_uuid missing!"); break; } 
        if (empty($_POST['admin_uuid'])) { printKO("admin_uuid empty!"); break; }  
        $DATA['admin_uuid'] = $_POST['admin_uuid'];
        if (!isset ($_POST['admin_pw'])) { printKO("admin_pw missing!"); break; } 
        if (empty($_POST['admin_pw'])) { printKO("admin_pw empty!"); break; }  
        $DATA['admin_pw'] = $_POST['admin_pw'];
    }
    
    if ($setup['features']['mail']) {
        if (!isset ($_POST['smtp_host'])) { printKO("smtp_host missing!"); break; } 
        if (empty($_POST['smtp_host'])) { printKO("smtp_host empty!"); break; }  
        $DATA['smtp_host'] = $_POST['smtp_host'];
        if (!isset ($_POST['smtp_port'])) { printKO("smtp_port missing!"); break; } 
        if (empty($_POST['smtp_port'])) { printKO("smtp_port empty!"); break; }  
        $DATA['smtp_port'] = $_POST['smtp_port'];
        if (!isset ($_POST['smtp_login'])) { printKO("smtp_login missing!"); break; } 
        if (empty($_POST['smtp_login'])) { printKO("smtp_login empty!"); break; }  
        $DATA['smtp_login'] = $_POST['smtp_login'];
        if (!isset ($_POST['smtp_pw'])) { printKO("smtp_pw missing!"); break; } 
        $DATA['smtp_pw'] = $_POST['smtp_pw'];
        $DATA['smtp_isPW'] = empty($_POST['smtp_pw'])?false:true;
        if (!isset ($_POST['smtp_secure'])) { printKO("smtp_secure missing!"); break; } 
        if (empty($_POST['smtp_secure'])) { printKO("smtp_secure empty!"); break; }  
        $DATA['smtp_auth'] = ($_POST['smtp_secure']=='none')?false:true;
        $DATA['smtp_secure'] = $_POST['smtp_secure'];
        if (!isset ($_POST['smtp_email'])) { printKO("smtp_email missing!"); break; } 
        if (empty($_POST['smtp_email'])) { printKO("smtp_email empty!"); break; }  
        $DATA['smtp_email'] = $_POST['smtp_email'];
    }
	

// -------------------------------------------------------------------

	/* 0. add debug/production info */
	if (($_SERVER['SERVER_ADDR']=='localhost' ) || $_SERVER["SERVER_ADDR"]=="127.0.0.1" || ($_SERVER["SERVER_ADDR"]=="::1")) {
        $DATA['debug'] = true;
        printOK("Debug Mode");
    } else {
        $DATA['debug'] = false;
        printOK("Production Mode");
    }
	
	/* 1. Generate configuration file */
	$string = @json_encode($DATA);
	$encryptionMethod = 'aes128';
	$iv = @mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
	if (!@file_put_contents ($confPath, $iv.openssl_encrypt ($string, $encryptionMethod, $secretHash, NULL, $iv))) {
		printKO("Failed to write to '$confPath'"); break; 
	} else {
        printOK("Write to '$confPath'");
        // bonus to allow read back
        @file_put_contents($hashPath, $secretHash);
    }
    
    /* 2. Read back for test */
	$encryptedMessage = @file_get_contents ($confPath);	
	if (!$encryptedMessage) {
		printKO("Failed to read back '$confPath'"); break; 		
	} else {
        printOK("Read back '$confPath'");
    }
	$iv = @substr($encryptedMessage, 0, 16);
	$encryptedMessage = @substr($encryptedMessage, 16);
	$readBack = @openssl_decrypt($encryptedMessage, $encryptionMethod, $secretHash, 0, $iv);
	$DATA_BACK = @json_decode($readBack, true);	
    $foundError = false;
	foreach ($DATA as $key => $value) {
		if($DATA[$key]!=$DATA_BACK[$key]) {
			printKO("$key: $value");
            $foundError = true;
		} else {
            //printOK("$key: $value");
        }
	}
    if ($foundError) break;
    
    /* 3. Write conf.php file accordingly */
    $confPhpFile = @realpath (@getcwd()."/..");
    $confPhpFile .= DIRECTORY_SEPARATOR . 'conf.php';    
    $file = fopen($confPhpFile, "w");
    $nl='';
    /* $nl="\n"; */
    if (!$file) { printKO("Failed to write to '$confPhpFile'"); break; }
    fwrite($file, '<?PHP {'.$nl);
    fwrite($file, '$e=@file_get_contents(\''.$confPath.'\');'.$nl);
    fwrite($file, 'if(!$e){echo("error 1");die();}'.$nl);
    fwrite($file, '$i=@substr($e,0,16);'.$nl);       
    fwrite($file, '$e=@substr($e,16);'.$nl);       
    fwrite($file, '$m="aes128";'.$nl);           
    fwrite($file, '$s=@openssl_decrypt($e,$m,\''.$secretHash.'\',0,$i);'.$nl);               
    fwrite($file, 'if(!isset($s)||trim($s)===\'\'){echo("error 2");die();}'.$nl);                       
    fwrite($file, '$GLOBALS[\'CONFIG\']=@json_decode($s,true);'.$nl);                           
    fwrite($file, '} ?>'.$nl);
    /* fwrite($file, '<?PHP foreach ($GLOBALS[\'CONFIG\'] as $key => $value) { echo ("$key: $value<br>"); } ?>'); */
    fclose($file);
    printOK("write to '$confPhpFile'</br>Configuration Done!");
    
	/* 4. let's include conf.php */
	if (!file_exists ('../conf.php')) { printKO("conf.php not correctly generated"); break; }
    include('../conf.php');
	printOK("inclusion of conf.php OK");
    
    /* 5. test mysql */
    if ($setup['features']['db']) {
        if ($GLOBALS['CONFIG']['sql_isPW']) {
            $db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login'], $GLOBALS['CONFIG']['sql_pw']); 
        } else {
            $db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login']); 
        }
        if ($db) {
            printOK('Connection to \''.$GLOBALS['CONFIG']['sql_host'].'\'');
        } else {
            printKO('Connection to \''.$GLOBALS['CONFIG']['sql_host'].'\''); break;
        }    
        if(@mysql_select_db($GLOBALS['CONFIG']['sql_db'],$db)) {
            printOK('Connection to database \''.$GLOBALS['CONFIG']['sql_db'].'\'');
        } else {
            printKO('Connection to database \''.$GLOBALS['CONFIG']['sql_db'].'\'');
            break;
        }    

        /* 6. apply setup.sql */
        $ret = applySqlFile($setup_sql);
        if ($ret) { printKO($ret); break; }
        printOK("Apply 'setup.sql'<br>Database Configured!");
    }
    
    /* 7. add user table */
    if ($setup['features']['user']) {
        $replace_arr=array('%USER_TABLE_NAME%' => $GLOBALS['CONFIG']['user_table']);
        $ret = applySqlFile($users_sql,$replace_arr);
        if ($ret) { printKO($ret); break; }
        printOK("Apply 'users.sql'<br>Users table Added!");
    }
    
    /* 8. insert admin */
    if ($setup['features']['admin']) {
        $req =  "INSERT INTO `".$GLOBALS['CONFIG']['user_table']."` ";
        $req .= "(`UUID`, `EMAIL`, `PASSWORD`, `PRIVILEGE`, `CREATION_DATE`) VALUES (";    
        $req .= "'".$DATA['admin_uuid']."',";
        $req .= "'".$DATA['admin_email']."',";
        $req .= "'".md5($DATA['admin_pw'])."',";
        $req .= "1,";
        $req .= "now())";
        if (!@mysql_query($req)){
            printKO("Failed to add admin user<br>".mysql_error()); break; 		
        } else {
            printOK("Add admin user");
        }    
    }
    
    if ($setup['features']['db']) @mysql_close($db);
        
    /* 8. test email */
    if ($setup['features']['mail']) {
        require '../libs/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug  = 4;
        $mail->Host       = $GLOBALS['CONFIG']['smtp_host'];	
        $mail->Port       = $GLOBALS['CONFIG']['smtp_port'];
        $mail->Username   = $GLOBALS['CONFIG']['smtp_login'];	
        $mail->Password   = $GLOBALS['CONFIG']['smtp_pw'];											   
        $mail->SMTPAuth   = $GLOBALS['CONFIG']['smtp_auth'];
        $mail->SMTPSecure = $GLOBALS['CONFIG']['smtp_secure'];	
        $mail->IsHTML(true);
        $mail->SetFrom($GLOBALS['CONFIG']['smtp_email']);
        $mail->addReplyTo($GLOBALS['CONFIG']['smtp_email']);	
        $mail->addAddress($GLOBALS['CONFIG']['smtp_email']);
        $mail->Subject = "Test from ".$setup['title'];	            
        $mail->Body = "<h1>If you can read this that mean your setup is correct!</h1>"; 
        print("<tr><td><textarea style='width:100%;'>");
        
        if($mail->Send()) {
            print("</textarea><br>");
            print("Send an email<br>&nbsp&nbspfrom: ".$GLOBALS['CONFIG']['smtp_email']."<br>&nbsp&nbspto:&nbsp&nbsp ".$DATA['admin_email']);
            print("</td>");
            print("<td class='success'>OK</td>");
        } else {   
            print("</textarea><br>");
            print("Failed to send an email<br>&nbsp&nbspfrom: ".$GLOBALS['CONFIG']['smtp_email']."<br>&nbsp&nbspto:&nbsp&nbsp ".$DATA['admin_email']." ".$mail->ErrorInfo);
            print("</td>");
            print("<td class='error'>KO</td>");
            break; 		
        }    
    }
    $SUCESS_SETUP = true;
    
} while(0);}catch(Exception $e){echo'Exception: ',$e->getMessage(), "\n";}} else {
    
    $SUCESS_SETUP = true;
    $DATA['base_url'] = "http://google.com";
    printOK("un");
    printOK("deux");
    printOK("trois");
    
}
echo "</table>\n";

if(!$SUCESS_SETUP) {
?>
    <h2>Sorry you have errors</h2>
    <p>go back and check your inputs</p></br>
    <button onclick="goBack()">Go Back</button>
<?php } else { ?>
<h1>Congratulation!<br>
<h2>Everthing seems ok, almost done.</h2><br>
<p>Last Steps:
<p>1- Go to this link <a href="<?php echo($DATA['base_url'])?>" target="_blank"><?php echo($DATA['base_url'])?></a> and test it</p>
<p>2- If you want to redo the configuration just <a href="#" onclick="javascript:goBack();">go back</a> to adjust your setting.</p>
<p>3- Happy with the result? Remove the public access of the page by clicking
<a href="removeAccess.php?conf=<?php echo urlencode($confPath); ?>" onclick = "if (! confirm('Are you\'re done?\nThis page won\'t be accessible')) { return false; }">here</a>
</p>
<?PHP } ?>
</body>
</html>