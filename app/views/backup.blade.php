<?php
//error_reporting(0);
ini_set("max_execution_time", 300);
$dir = base_path() . "/backup";
if(!(file_exists($dir))) {
mkdir($dir, 0777);
}
$host = "localhost"; //host name
$username = "root"; //username
$password = ""; // your password
$dbname = "posv2"; // database name
$zip = new ZipArchive();
backup_tables($host, $username, $password, $dbname);

/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
$con = mysql_connect($host,$user,$pass);
mysql_select_db($name,$con);

//get all of the tables
if($tables == '*')
{
$tables = array();
$result = mysql_query('SHOW TABLES');
while($row = mysql_fetch_row($result))
{
$tables[] = $row[0];
}
}
else
{
$tables = is_array($tables) ? $tables : explode(',',$tables);
}
$return = "";

	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		//$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		for ($i = 0; $i < $num_fields; $i++)
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++)
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}



//save file
$handle = fopen(date('d-m-y')."_backup_".(md5(implode(',',$tables))).'.sql','w+');
fwrite($handle,$return);
fclose($handle);
}

if (glob("*.sql") != false)
{
$filecount = count(glob("*.sql"));
$arr_file = glob("*.sql");

for($j=0;$j<$filecount;$j++)
{
$res = $zip->open($arr_file[$j].".zip", ZipArchive::CREATE);
if ($res === TRUE)
{
$zip->addFile($arr_file[$j]);
$zip->close();
unlink($arr_file[$j]);
}
}
}

//get the array of zip files
if(glob("*.zip") != false) {
$arr_zip = glob("*.zip");
}

//copy the backup zip file to site-backup-stark folder
foreach ($arr_zip as $key => $value) {
$delete_zip[] = $value;
    copy("$value", "$dir/$value");
}
for ($i=0; $i < count($delete_zip); $i++) {
unlink($delete_zip[$i]);
}
echo "<center>Backup taken Successfully.</center>";
echo "<a href='#' onclick='javascript:history.go(-2)'>Go Back</a>"
?>

