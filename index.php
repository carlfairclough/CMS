<? 
$time = microtime(); 
$time = explode(' ', $time); 
$time = $time[1] + $time[0]; 
$start = $time; 
?> 

<?php 

include_once('backend/index.php')

?>

<? 
$time = microtime(); 
$time = explode(' ', $time); 
$time = $time[1] + $time[0]; 
$finish = $time; 
$total_time = round(($finish - $start), 4); 
echo '<p>Page generated in '.$total_time.' seconds.</p>'."\n"; 
?> 