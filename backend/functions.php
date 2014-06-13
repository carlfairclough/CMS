<?php





	// FIND OUT THE SERVER ROOT PATH AND DOMAIN
	$root = $_SERVER['DOCUMENT_ROOT'];
	$domain = strtolower($_SERVER[HTTP_HOST]);





	// FIND OUT WHAT THE CURRENT URL REQ IS
	$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
	define (request, $request);





	$themeDirectory = '/themes/';








	// READ THE SETUP FILE AND GET ALL VARIABLES AT ONCE
	$setupFile = $root.'/setup.txt';
	$setup_lines = file($setupFile);
	$setup_lines_string = implode (',', $setup_lines);
	// define it to call it in the function later
	define(setup_lines_string, $setup_lines_string);

	function get_settings($term) {
		// convert string back to array
		$setup_lines_array = explode(',' , setup_lines_string);

		// create blank array for the output
		$setup_multi_lines = array();

		// Find out the first line
		foreach($setup_lines_array as $line_num => $line) {

			if ( 0 === strpos(strtolower($line), strtolower($term)) ) {
				$start_line_num = (int)$line_num + 1;
				$term_line_num = (int)$line_num + 1;
				break;
			}
		};

		// Read the relevant lines and put into array
		foreach($setup_lines_array as $line_num => $line) {

			if ( (int)$line_num >= (int)$start_line_num) {

				// break at the end
				if ( 0 === strpos($line, '----') ) {
					break;
				}
				$setup_multi_lines[] = $line;
			}
		}

		// array to string & strip whitespace
		return trim(implode($setup_multi_lines, ''));
	}





	// Quick access vars
	$theme_name = get_settings('Theme');
	$site_name = get_settings('Site Name');
	$site_description = get_settings('Site Description');
	$your_name = get_settings('Your Name');
	$your_city = get_settings('Your City');
	$your_country = get_settings('Your Country');
	$your_email = get_settings('Your Email');
	$your_twitter = get_settings('Your Twitter');






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
	define(request, $request);






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

		// create blank array for the output
		$multi_lines = array();

		// Find out the first line
		foreach($page_lines_array as $line_num => $line) {

			if ( 0 === strpos(strtolower($line), strtolower($term)) ) {
				$start_line_num = (int)$line_num + 1;
				$term_line_num = (int)$line_num + 1;
				break;
			}
		};

		// Read the relevant lines and put into array
		foreach($page_lines_array as $line_num => $line) {

			if ( (int)$line_num >= (int)$start_line_num) {

				// break at the end
				if ( 0 === strpos($line, '----') ) {
					break;
				}
				$multi_lines[] = $line;
			}
		}

		// array to string & strip whitespace
		return trim(implode($multi_lines, ''));
	}


	// is_home
	function is_home() {
		$return = false;
		if (empty(request)) {
			$return = 'true';
		} else {
			$return = 'false';
		};

		return $return;
	};



	// body classes
	function insert_body_classes() {
		$classes=' ';
		if (is_home()) {
			$classes = 'homepage home index template-'.get_content('Template');
		} else {
			$classes = request.' '.get_content('Template');
		}
		echo $classes;
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