<?php
// CONFIGURATION
$output = false;

// Create this file yourself
$file = "/path/to/cron/cron.db.txt";

// These are file names for your scripts with the php extension removed. 
// Just make sure these files exist (with file extensions) in the same folder this file is in.
$cronFiles = array(
    "fileName1"   => array("minutes"=>"1","hours"=>"0","days"=>"0"),
    "fileName2"   => array("minutes"=>"0","hours"=>"0","days"=>"1")
);

/////////////////////////////////////////
// PROCESSING - NO SETTINGS BELOW HERE //
/////////////////////////////////////////
if($output) print "<table>";
$f = @fopen($file, 'c+');
if (!$f) {
    return false;
} else {
    $content = file_get_contents($file);
    $fileContents = json_decode($content);
    $fileContents = (array)$fileContents;

    foreach ($cronFiles as $task => $freq){
        if($output) print "<tr><td>Waiting Until: ".strtotime("+".$freq['days']." days ".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])."</td></tr>";
        if(!array_key_exists($task,$fileContents)) {
            $fileContents[$task] = strtotime("now");
        }
        if($freq["days"] == 0){
            if($freq["hours"] == 0){
                if($freq["minutes"] == 0){
                    include $task.".php";
                    $fileContents[$task] = strtotime("now");
                    if($output) print "<tr><td>Running ".$task."</td></tr>";
                } else if(strtotime("now") >= strtotime("+".$freq['minutes']." minutes", $fileContents[$task])){
                    include $task.".php";
                    $fileContents[$task] = strtotime("now");
                    if($output) print "<tr><td>Running ".$task."</td></tr>";
                }
            } else if(strtotime("now") >= strtotime("+".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])){
                include $task.".php";
                $fileContents[$task] = strtotime("now");
                if($output) print "<tr><td>Running ".$task."</td></tr>";
            }
        } else if(strtotime("now") >= strtotime("+".$freq['days']." days ".$freq['hours']." hours ".$freq['minutes']." minutes", $fileContents[$task])){
            include $task.".php";
            $fileContents[$task] = strtotime("now");
            if($output) print "<tr><td>Running ".$task."</td></tr>";
        }
    }
    if($output) print "<tr><td>Writing: ".json_encode($fileContents)."</td></tr>";
    if($output) print "<tr><td>Time: ".strtotime("now")."</td></tr>";
    fwrite($f, json_encode($fileContents));
    fclose($f);

    if($output) print "<tr><td>Recursive Dump</td></tr><tr><td>";
    if($output) print_r($fileContents);
    if($output) print "</td></tr>";
}
if($output) print "</table>";
?>
