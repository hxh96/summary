<?php  
$dir=dirname(__FILE__);

if(isset($_GET["act"]) && $_GET["act"] == "del" ){   //删除测试文件
$filename=basename($_SERVER['PHP_SELF']);
@unlink($filename);
exit('Error:File Not Found!');
}

echo "当前清理路径:".$dir."&nbsp;&nbsp;<a href=\"?act=del\" >[删除测试文件]</a><BR/>";

if (!is_dir($dir))
    die("$dir not exists");
$auto = 1;  
checkdir($dir);  
function checkdir($basedir){  
if ($dh = opendir($basedir)) {  
  while (($file = readdir($dh)) !== false) {  
   if ($file != '.' && $file != '..'){  
    if (!is_dir($basedir."/".$file)) {  
     echo "filename: $basedir/$file ".checkBOM("$basedir/$file")."<BR/>\n";  
    }else{  
     $dirname = $basedir."/".$file;  
     checkdir($dirname);  
    }  
   }  
  }  
closedir($dh);  
}  
} 

function checkBOM ($filename) {  
global $auto;  
$contents = file_get_contents($filename);  
$charset[1] = substr($contents, 0, 1);  
$charset[2] = substr($contents, 1, 1);  
$charset[3] = substr($contents, 2, 1);  
if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {  
  if ($auto == 1) {  
   $rest = substr($contents, 3);  
   rewrite ($filename, $rest);  
   return ("<font color=red> BOM found, automatically removed.</font>\n");  
  } else {  
   return ("<font color=red>BOM found.</font>\n");  
  }  
}  
else return ("BOM Not Found.\n");  
}  
function rewrite ($filename, $data) {  
$filenum = fopen($filename, "w");  
flock($filenum, LOCK_EX);  
fwrite($filenum, $data);  
fclose($filenum);  
}  
?>