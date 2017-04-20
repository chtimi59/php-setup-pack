<?php
/* Run a .sql script file
   
   Note: $replace_arr array allows to replace %VAR% token in it
   
   Example of replacement:
   
   If sql file contains:
      DROP TABLE IF EXISTS `%USER_TABLE_NAME%';
      
   And replace_arr contains:
       $replace_arr['USER_TABLE_NAME'] = 'hello'
   
   then, the actual script line will be:
       DROP TABLE IF EXISTS `hello';
   
*/
function applySqlFile($filename, $replace_arr = NULL)
{
    $sqlrequest = @file_get_contents ($filename);	
    if (!$sqlrequest) return "Failed to read 'setup.sql'";
    
    $sqlrequest_arr = @explode("\n",$sqlrequest);
    $req_lineCount = 0;
    $req="";
    $foundError = false;
        
    foreach ($sqlrequest_arr as $line)
    {
        $req_lineCount++;
        
        $line = trim($line);
        
        // comment line
        if ('--' == substr($line,0,2))
            continue;
        
        // %VAR% token replacment
        if ($replace_arr!=NULL)
            $line = str_replace(array_keys($replace_arr), array_values($replace_arr), $line);
        
        $req = $req.$line;
        
        // quite ugly, but it (quite) allows a command on multiple lines
        if (';' != substr($line, -1)) 
            continue; 
        
        // do mysql request
        if (!empty($req)) {
            if (!@mysql_query($req)){
                return("Failed to apply 'setup.sql' line $req_lineCount<br>".htmlentities($req)."<br>".mysql_error());
                break; 		
            }
        }
        $req='';
    }
    
    /* success */
    return NULL;
}
?>