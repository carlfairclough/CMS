<?php





	// FIND OUT THE SERVER ROOT PATH AND DOMAIN
	$root = $_SERVER['DOCUMENT_ROOT'];
	$domain = strtolower($_SERVER[HTTP_HOST]);





	// FIND OUT WHAT THE CURRENT URL REQ IS
	$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
	





	$themeDirectory = '/themes/';




	// READ THE SETUP FILE AND GET ALL VARIABLES AT ONCE
	$setupFile = $root.'/setup.txt';
	$setup_lines = file($setupFile);

	foreach($setup_lines as $line_num => $line) {
		if ( strstr ( $line, 'Theme' ) ) {
			$theme_line_num = (int)$line_num + 1;
		} else if ( $line_num == $theme_line_num && $line_num != 0 ) { 
			$theme_name = trim($line);
		} else if ( strstr ( $line, 'Site Name' ) ) {
			$sitename_line_num = (int)$line_num + 1;
		} else if ( $line_num == $sitename_line_num && $line_num != 0 ) { 
			$site_name = trim($line);
		} else if ( strstr ( $line, 'Site Description' ) ) {
			$sitedesc_line_num = (int)$line_num + 1;
		} else if ( $line_num == $sitedesc_line_num && $line_num != 0 ) { 
			$site_description = trim($line);
		} else if ( strstr ( $line, 'Your Name' ) ) {
			$yourname_line_num = (int)$line_num + 1;
		} else if ( $line_num == $yourname_line_num && $line_num != 0 ) { 
			$your_name = trim($line);
		} else if ( strstr ( $line, 'Your City' ) ) {
			$yourcity_line_num = (int)$line_num + 1;
		} else if ( $line_num == $yourcity_line_num && $line_num != 0 ) { 
			$your_city = trim($line);
		} else if ( strstr ( $line, 'Your Country' ) ) {
			$yourcountry_line_num = (int)$line_num + 1;
		} else if ( $line_num == $yourcountry_line_num && $line_num != 0 ) { 
			$your_country = trim($line);
		} else if ( strstr ( $line, 'Your Email' ) ) {
			$youremail_line_num = (int)$line_num + 1;
		} else if ( $line_num == $youremail_line_num && $line_num != 0 ) { 
			$your_email = trim($line);
		} else if ( strstr ( $line, 'Your Twitter' ) ) {
			$yourtwitter_line_num = (int)$line_num + 1;
		} else if ( $line_num == $yourtwitter_line_num && $line_num != 0 ) { 
			$your_twitter = trim($line);
		} else if ( strstr ( $line, 'Header' ) ) {
			$header_line_num = (int)$line_num + 1;
		} else if ( $line_num == $header_line_num && $line_num != 0 ) { 
			$header = trim($line);
		} else if ( strstr ( $line, 'Footer' ) ) {
			$footer_line_num = (int)$line_num + 1;
		} else if ( $line_num == $footer_line_num && $line_num != 0 ) { 
			$footer = trim($line);
			break;
		}
	}





	// KEY VARIABLES;
	// $theme_name
	// $site_name
	// $site_description
	// $your_name
	// $your_city
	// $your_email
	// $your_twitter
	// $site_name
	// $header
	// $footer





	// Define some vars
	define(theme_name, $theme_name);
	define(theme_directory, '/themes/');
	define(root, $root);
	define(domain, $domain);





	// Theme location on the server
	function theme_server_loc () {
		define(theme_server_loc, root.theme_directory.theme_name);
		return theme_server_loc;
	}






	// Theme location url
	function theme_url_loc () {
		define(theme_url_loc, domain.theme_directory.theme_name);
		return 'http://'.theme_url_loc;
	}






	// Get variables for page requested before including the template
	$root_pages = glob(root.'/pages/*');
	$request =  trim($request, '/');


	// Main navigation links
	$links = array();

	foreach ($root_pages as $root_link) {

		$folder = basename($root_link);

		if ( is_numeric(substr($folder, 0, 2)) && $folder[2] == '_') {
			$new_folder = $folder;
			$page_name = substr($new_folder, 3);
			unset ($folder);
		}

		$page_vis_name = str_replace('-', ' ', str_replace('_', ' ', $page_name));
		$link_wrap = stripslashes('<li class="main-nav-wrap-'.$page_name.'"><a class="main-nav-link-"'.$page_name.' href="http://'.domain.'/'.$page_name.'">'.$page_vis_name.'</a></li>');
		$link_wrap = stripslashes($link_wrap);
		$links[] = $link_wrap;
	}

	$get_main_nav = '<ul class="main-nav-wrap">'.stripslashes(str_replace('","', '', rtrim(ltrim(json_encode($links),'["'), '""]'))).'</ul>';



	foreach ($root_pages as $root_page) {
		
		$folder = basename($root_page);

		if (empty($request)) {
			$page_lines = file(root.'/pages/home/page.txt');
			break;
		// 
		} else if ( is_numeric(substr($folder, 0, 2)) && $folder[2] == '_' && $request == substr($folder, 3)) {
			$page_lines = file(root.'/pages/'.$folder.'/page.txt');
			break;
		// Nope, so does it exist at all?
		} else if ( $folder == $request ){
			$page_lines = file(root.'/pages/'.$folder.'/page.txt');
			break;
		// Nope – is it the homepage?
		} else if (empty($request)) {
			echo 'homepage';
			$page_lines = file(root.'/pages/home/page.txt');
			break;
		// Must be a 404 page then
		} else {
			$page_lines = file(root.'/pages/404/page.txt');
			break;
		}
	}





	
	// convert page txt file to a string
	$page_lines_string = implode (',', $page_lines);
	// define it to call it in the function later
	define(page_lines_string, $page_lines_string);

	function get_content($term) {
		// convert string back to array
		$page_lines_array = explode(',' , page_lines_string);

		foreach($page_lines_array as $line_num => $line) {
			if ( strstr ( $line, $term ) ) {
				$term_line_num = (int)$line_num + 1;
			} else if ( $line_num == $term_line_num && $line_num != 0 ) { 
				$return_term = trim($line);
				return $return_term;
			}
		};
		
	}

	




	// Find out page title

	// $template_name
	// $title
	// $content




	// BUILD THE PAGES
	// include header first
	include_once(theme_server_loc().'/header.php');

	// include page next
	include_once(theme_server_loc().'/template-'.get_content('Template').'.php');

	// now include the footer
	include_once(theme_server_loc().'/footer.php');





	

/////////////////////////////
////// GET THEME DIRECTORY //
/////////////////////////////
	


	
//////////////////////////////
////// PORTFOLIO STUFF HERE //
//////////////////////////////
	$dir = "./pages/work/*";
	$thumb = "main-thumb.png";
	$thumbcount = 3;
	$cols = 12/$thumbcount;
	$projects = glob($dir);
		

	// FIND PROJECT NAME
	function projectName ($file) {
		$projectDetails = $file.'/project.md';
		$ID = 'Name: ';

		foreach (new SplFileObject($projectDetails) as $lineNumber => $lineContent) {
		    if (FALSE !== strpos($lineContent, $ID)) {
		    	$content = preg_replace('/^' . preg_quote($ID, '/') . '/', '', $lineContent);
		    	break;
		    }
		}
		echo $content;
	}

	function projectURL ($file) {
		$projectDetails = $file.'/project.md';
		$ID = 'Name: ';

		foreach (new SplFileObject($projectDetails) as $lineNumber => $lineContent) {
		    if (FALSE !== strpos($lineContent, $ID)) {
		    	$content = preg_replace('/^' . preg_quote($ID, '/') . '/', '', $lineContent);
		    	break;
		    }
		}

		$content = strtolower('/work/'.str_replace(' ', '_', $content));
		echo $content;
	}

	// FIND LINK TO PROJECT
	function projectRole ($file) {
		$projectDetails = $file.'/project.md';
		$ID = 'Role: ';

		foreach (new SplFileObject($projectDetails) as $lineNumber => $lineContent) {
		    if (FALSE !== strpos($lineContent, $ID)) {
		    	$content = preg_replace('/^' . preg_quote($ID, '/') . '/', '', $lineContent);
		    	break;
		    }
		}
		echo $content;
	}

	// FIND LINK TO PROJECT
	function linkToProject ($file) {
		$projectDetails = $file.'/project.md';
		$ID = 'URL: ';

		foreach (new SplFileObject($projectDetails) as $lineNumber => $lineContent) {
		    if (FALSE !== strpos($lineContent, $ID)) {
		    	$content = preg_replace('/^' . preg_quote($ID, '/') . '/', '', $lineContent);
		    	break;
		    }
		}
		echo $content;
	}

	// 

	// FIND PROJECT THUMBNAIL
	function projectThumb ($file) {
		echo $file.'/main-thumb.png';
	}


	

	if (is_home()) {
		$confFileLoc = $root.'/pages/';
		$confFileName = 'home.md';
	} else {
		$confFileLoc = $root.'/pages/'.$folder;
		$confFileName = 'contents.md';
	}


	$page = $confFileLoc.'/'.$confFileName;

	function show ($page, $blockName) {
		$projectDetails = $page;
		$ID = $blockName.': ';

		foreach (new SplFileObject($projectDetails) as $lineNumber => $lineContent) {
		    if (FALSE !== strpos($lineContent, $ID)) {
		    	$content = preg_replace('/^' . preg_quote($ID, '/') . '/', '', $lineContent);
		    	break;
		    }
		}
		echo $content;
	};


///////////////////////////
///	MISC PAGE FUNCTIONS ///
///////////////////////////

	
?>