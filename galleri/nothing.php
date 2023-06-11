<?PHP

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
    } 

$time_start = getmicrotime();
    
for ($i=0; $i < 1000; $i++){
    //do nothing, 1000 times
    }

$time_end = getmicrotime();
$time = $time_end - $time_start;
echo $time_start."<br>\n";
echo $time_end."<br>\n";
echo "Did nothing in $time seconds";
?>