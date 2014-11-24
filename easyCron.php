<?php
// CONFIGURATION
$debug = false;

// These are file names with the extension removed. 
// Just make sure these files exist in the same folder this file is in.
$cronFiles = array(
    "fileName1"   => array("minutes"=>"1","hours"=>"0","days"=>"0"),
    "fileName2"   => array("minutes"=>"0","hours"=>"0","days"=>"1")
);

$file = "/path/to/cron/cron.db.txt";

////////////////
// PROCESSING //
////////////////
if($debug) print "<table>";
$f = @fopen($file, 'c+');
if (!$f) {
    return false;
} else {
    $content = file_get_contents($file);
    $fileContents = json_decode($content);
    $fileContents = (array)$fileContents;
    if($debug) print "<tr><td>Recursive Dump</td></tr><tr><td>";
    if($debug) print_r($fileContents);
	if($debug) print "</td></tr>";
    foreach ($cronFiles as $task => $freq){
		if($debug) print "<tr><td>Waiting Until: ".strtotime("+".$freq['days']." days ".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])."</td></tr>";
        if(!array_key_exists($task,$fileContents)) {
            $fileContents[$task] = strtotime("now");
        }
        if($freq["days"] == 0){
            if($freq["hours"] == 0){
                if($freq["minutes"] == 0){
                    include $task.".php";
                    $fileContents[$task] = strtotime("now");
                    if($debug) print "<tr><td>Running ".$task."</td></tr>";
                } else if(strtotime("now") >= strtotime("+".$freq['minutes']." minutes", $fileContents[$task])){
                    include $task.".php";
                    $fileContents[$task] = strtotime("now");
                    if($debug) print "<tr><td>Running ".$task."</td></tr>";
                }
            } else if(strtotime("now") >= strtotime("+".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])){
                include $task.".php";
                $fileContents[$task] = strtotime("now");
                if($debug) print "<tr><td>Running ".$task."</td></tr>";
            }
        } else if(strtotime("now") >= strtotime("+".$freq['days']." days ".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])){
            include $task.".php";
            $fileContents[$task] = strtotime("now");
            if($debug) print "<tr><td>Running ".$task."</td></tr>";
        }
    }
    if($debug) print "<tr><td>Writing: ".json_encode($fileContents)."</td></tr>";
    if($debug) print "<tr><td>Time: ".strtotime("now")."</td></tr>";
    fwrite($f, json_encode($fileContents));
    fclose($f);
}
if($debug) print "</table>";
?>
