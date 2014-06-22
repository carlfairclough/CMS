<?php

$root = $_SERVER['DOCUMENT_ROOT'];
$domain = strtolower($_SERVER[HTTP_HOST]);
$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
define ("REQUEST", $request);
define ("ROOT", $root);
define("DOMAIN", $domain);





$pageDirs = glob(ROOT.'/pages/*');

$pageDirList = array();

foreach ( $pageDirs as $pageDir ) {
	$pageDirList[] = '<li>'.$pageDir.'</li>';
}

$pageDirListPrint = '<ul class="pageDirList">'.stripslashes(str_replace('","', '', rtrim(ltrim(json_encode($pageDirList),'["'), '""]'))).'</ul>';

echo $pageDirListPrint;

?>