<?php
include("guid.php");
date_default_timezone_set('America/Montreal');
$string = file_get_contents("setup.conf");
$setup = json_decode($string, true);
if (!$setup) die("setup.conf JSON error");
if ($setup['features']['user'] && !$setup['features']['db'])
    die("setup.conf error 'user' needs 'db'");
if ($setup['features']['admin'] && (!$setup['features']['db'] || !$setup['features']['user']))
    die("setup.conf error 'admin' needs 'db' and 'user'");
$title = "** Setup of ".$setup['title']." **";

// default values
$pageURL = ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
$pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER['SERVER_NAME'];
$pageURL .= "/" . strtolower($setup['title']);

if ($setup['features']['db']) {
    $sql_host = "localhost";
    $sql_login = "root";
    $sql_pw = "";
    $sql_db = "db".$setup['title'];   
}
if ($setup['features']['user']) {
    $user_table = "user";
}
if ($setup['features']['admin']) {
    $admin_pw = "1234";
    $admin_uuid=guid();
    $admin_email = "admin@somewhere.com";
}
if ($setup['features']['mail']) {
    $smtp_host="";
    $smtp_port="465";
    $smtp_login="";
    $smtp_pw="";
    $smtp_auth="";
    $smtp_secure="ssl";
    $smtp_email="noreply@somewhere.com";
}

$secretHash = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36),0,8);
$curPath = @realpath (@getcwd()."/..");
$confPath = $curPath;

if(strncmp(PHP_OS, 'WIN', 3) === 0) {    
    /* On Windows */
    $confPath = strtolower(substr($curPath,0,3)).strtolower($setup['title']).".conf";    
} else {
    /* On UNIX */
    $confPath = '/var/'.strtolower($setup['title']).".conf";    
}
$hashPath = "$confPath.hash";

/* Here we readback conf file if exist */
$isConfAlreadyExist=false;
if (@file_exists($confPath) && @file_exists($hashPath)) {    
	$SecretHashBack = @file_get_contents ($hashPath);	
	if ($SecretHashBack) {
        $encryptedMessage = @file_get_contents ($confPath);	
        if ($encryptedMessage) {
            $iv = @substr($encryptedMessage, 0, 16);           
            $encryptionMethod = 'aes128';            
            $encryptedMessage = @substr($encryptedMessage, 16);
            $readBack = @openssl_decrypt($encryptedMessage, $encryptionMethod, $SecretHashBack, 0, $iv);
            if (!empty($readBack)) {
                $DATA_BACK = @json_decode($readBack, true);	
                $isConfAlreadyExist=true;
                $secretHash = $SecretHashBack;
                $pageURL = $DATA_BACK['base_url'];
                if ($setup['features']['db']) {
                    $sql_host = $DATA_BACK['sql_host'];
                    $sql_login = $DATA_BACK['sql_login'];
                    $sql_pw = $DATA_BACK['sql_pw'];
                    $sql_db = $DATA_BACK['sql_db'];
                }
                if ($setup['features']['user']) {
                    $user_table = $DATA_BACK['user_table'];
                }
                if ($setup['features']['admin']) {
                    $admin_pw = $DATA_BACK['admin_pw'];
                    $admin_uuid = $DATA_BACK['admin_uuid'];
                    $admin_email = $DATA_BACK['admin_email'];
                }
                if ($setup['features']['mail']) {
                    $smtp_host = $DATA_BACK['smtp_host'];
                    $smtp_port = $DATA_BACK['smtp_port'];
                    $smtp_login = $DATA_BACK['smtp_login'];
                    $smtp_pw = $DATA_BACK['smtp_pw'];
                    $smtp_auth = $DATA_BACK['smtp_auth'];
                    $smtp_secure = $DATA_BACK['smtp_secure'];
                    $smtp_email = $DATA_BACK['smtp_email'];
                }
            }
        }
    }    
}
?>


<html>
<title><?php echo $title; ?></title>
<style>
    body     {font-family: monospace; }
    label    {display:block; margin-top:10px; width:100%;}
    span     {color:#C00; display:block;}
    input    {margin-bottom:10px; height:30px; width:100%; color: blue; padding:5px; font-size:18;} 
    fieldset {margin:20px; background-color:#eee; text-align: left;}
    form     {width:50%; text-align: center;} 
    #button  {margin:10px; height:50px; width:200px; background-color:#0A0; color: white; padding:5px; } 
</style>

<body>
<h1><?php echo $title; ?></h1>


<script>
function copyTextToClipboard(text) {
  var textArea = document.createElement("textarea");
  textArea.style.position = 'fixed';
  textArea.style.top = 0;
  textArea.style.left = 0;
  textArea.style.width = '2em';
  textArea.style.height = '2em';
  textArea.style.padding = 0;
  textArea.style.border = 'none';
  textArea.style.outline = 'none';
  textArea.style.boxShadow = 'none';
  textArea.style.background = 'transparent';
  textArea.value = text;
  document.body.appendChild(textArea);
  textArea.select();
  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Copying text command was ' + msg);
  } catch (err) {
    console.log('Oops, unable to copy');
  }
  document.body.removeChild(textArea);
}

function copyFormToClipboard(evt) {
   formsDatas={};
   var nodes = document.querySelectorAll("input, select");
   for (var i=0; i<nodes.length; i++) {
       if (nodes[i].name!="") formsDatas[nodes[i].name] = nodes[i].value;
   }
   txt = JSON.stringify(formsDatas, null, 2); 
   console.log(txt);
   /* copy (if we can) */
   copyTextToClipboard(txt);
}

function readJsonFile(evt) {
   if (evt.target.files.length!=1) return;   
   var files = evt.target.files;
   var file = files[0];           
   var reader = new FileReader();
   reader.onload = function() { try {
        formsDatas= JSON.parse(this.result);
        txt = JSON.stringify(formsDatas, null, 2);         
        /* fill form */
        var nodes = document.querySelectorAll("input, select");
        for (var i=0; i<nodes.length; i++) {
           if(formsDatas.hasOwnProperty(nodes[i].name)) { 
               nodes[i].value = "";           
               console.log(nodes[i].name + "='" + formsDatas[nodes[i].name]+"'");
               nodes[i].value = formsDatas[nodes[i].name];
           }
        }   
        evt.target.value=null;
   } catch (e) { console.error("File error:", e);}}
   reader.readAsText(file);
}
</script> 


<!-- Read Button -->
<input type="file" id="rfile" enctype="multipart/form-data" style="position: absolute; visibility: hidden;">
<input id="button" type="button" value="Fill From Json File" onclick="document.getElementById('rfile').click()"/>
<script>document.getElementById('rfile').addEventListener('change', readJsonFile, false);</script>


<h2>Step 1 : User settings</h2>
<form method="post" action="generate.php">

<?php if ($isConfAlreadyExist) echo "<span>Note: configuration file '$confPath' detected (read back values)</span>"; ?>

<fieldset>
    <legend>Configuration file:</legend>
    <label for="conf_path">Were to save it ?</label>
    <span>Warning: for security reason please avoid public folders</span>
    <span>Note the current dir is <?php echo getcwd(); ?></span>
    <input type="text" name="conf_path" placeholder="<?php echo $confPath ?>" value="<?php echo $confPath ?>"/>
    <label for="conf_hash">Random hash</label>
    <input type="text" name="conf_hash" placeholder="<?php echo $secretHash ?>" value="<?php echo $secretHash ?>"/>    
</fieldset>

<fieldset>
    <legend>Hosting:</legend>
    <label for="base_url">Webpage</label>
    <span>Note: Should already exist</span>
    <input type="text" name="base_url" placeholder="<?php echo $pageURL ?>" value="<?php echo $pageURL ?>"/>
</fieldset>

<?php if ($setup['features']['db']) { ?>
<fieldset>
    <legend>SQL Database configuration:</legend>
    <label for="sql_host">Server host[:port]</label>
    <input type="text" name="sql_host" placeholder="localhost" value="<?php echo $sql_host ?>"/>
    <label for="sql_login">Login</label>
    <input type="text" name="sql_login" placeholder="root" value="<?php echo $sql_login ?>"/>
    <label for="sql_pw">Password <i>(leave empty if none)</i></label>
    <input type="text" name="sql_pw" placeholder="" value="<?php echo $sql_pw ?>"/>
    <label for="sql_db">Database Name</label>
    <span>Note: Should already exist</span>
    <input type="text" name="sql_db" placeholder="db<?php echo $setup['title']; ?>" value="<?php echo $sql_db ?>"/>
</fieldset>
<?php } ?>

<?php if ($setup['features']['user']) { ?>
<fieldset>
    <legend>USER Table configuration:</legend>
    <label for="user_table">Table Name</label>
    <input type="text" name="user_table" placeholder="user_table" value="<?php echo $user_table ?>"/>
</fieldset>
<?php } ?>

<?php if ($setup['features']['admin']) { ?>
<fieldset>
    <legend>Admin User:</legend>
    <label for="admin_email">email</label>
    <input type="email" name="admin_email" placeholder="admin@somewhere.com" value="<?php echo $admin_email; ?>">
    <label for="admin_uuid">UUID</label>
    <input type="text" name="admin_uuid" placeholder="<?php echo $admin_uuid;?>" value="<?php echo $admin_uuid;?>">
    <label for="admin_pw">Password</label>
    <input type="text" name="admin_pw" placeholder="1234" value="<?php echo $admin_pw ?>">
</fieldset>
<?php } ?>

<?php if ($setup['features']['mail']) { ?>
<fieldset>
    <legend>SMTP Mail configuration:</legend>
    <label for="smtp_host">Server address</label>
    <input type="text" name="smtp_host" placeholder="somewhere.smtp.net" value="<?php echo $smtp_host ?>" />
    <label for="smtp_port">Server port</label>
    <input type="number" name="smtp_port" placeholder="465" value="<?php echo $smtp_port ?>"/>   
    <label for="smtp_login">Login</label>
    <input type="text" name="smtp_login" placeholder="root" value="<?php echo $smtp_login ?>"/>
    <label for="smtp_pw">Password <i>(leave empty if none)</i></label>
    <input type="text" name="smtp_pw" placeholder="" value="<?php echo $smtp_pw ?>"/>
    <label for="smtp_secure">Security</label>
    <select name="smtp_secure">
      <option value="none">none</option>
      <option value="ssl" <?php if (0==strcmp($smtp_secure, "ssl"))  echo "selected"; ?>>ssl</option>      
      <option value="tsl" <?php if (0==strcmp($smtp_secure, "tsl"))  echo "selected"; ?>>tsl</option> 
    </select></p>
    <label for="smtp_email">Email</label>
    <input type="email" name="smtp_email" placeholder="noreply@somewhere.com" value="<?php echo $smtp_email; ?>"/>
</fieldset>
<?php } ?>

<input id="button" type="submit" value="Generate" />

<input id="button" type="button" value="Copy to clipboard" onclick="copyFormToClipboard()"/>

</form>
<body>
</html>