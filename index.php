<? 
$time = microtime(); 
$time = explode(' ', $time); 
$time = $time[1] + $time[0]; 
$start = $time; 







$root = $_SERVER['DOCUMENT_ROOT'];
$domain = strtolower($_SERVER[HTTP_HOST]);
$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
define ("REQUEST", $request);
define ("ROOT", $root);
define("DOMAIN", $domain);

$request_depth = count(explode('/', REQUEST)) - 1;



if (REQUEST == '/editor') {
	include_once('editor/index.php');
} else {
	include_once('backend/index.php');
}















$time = microtime(); 
$time = explode(' ', $time); 
$time = $time[1] + $time[0]; 
$finish = $time; 
$total_time = round(($finish - $start), 4); 
echo '<p>Page generated in '.$total_time.' seconds.</p>'."\n"; 
?> 