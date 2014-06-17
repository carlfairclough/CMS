<?php





	// FIND OUT THE SERVER ROOT PATH AND DOMAIN
	$root = $_SERVER['DOCUMENT_ROOT'];
	$domain = strtolower($_SERVER[HTTP_HOST]);





	// FIND OUT WHAT THE CURRENT URL REQ IS
	$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
	define (request, $request);

	$request_depth = count(explode('/', $request)) - 1;




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
		} else {
			unset($new_folder);
			unset($page_name);
		}

		$page_vis_name = str_replace('-', ' ', str_replace('_', ' ', $page_name));
		$link_wrap = stripslashes('<li class="main-nav-wrap-'.$page_name.'"><a class="main-nav-link-"'.$page_name.' href="http://'.domain.'/'.$page_name.'">'.$page_vis_name.'</a></li>');
		$link_wrap = stripslashes($link_wrap);
		$links[] = $link_wrap;
	}

	$get_main_nav = '<ul class="main-nav-wrap">'.stripslashes(str_replace('","', '', rtrim(ltrim(json_encode($links),'["'), '""]'))).'</ul>';




	// get URL (supports https) 
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
	 	$pageURL .= "://";
	 	if ($_SERVER["SERVER_PORT"] != "80") {
	  		$pageURL = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 	} else {
	  		$pageURL = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 	}
	 	return $pageURL;
	}

	$curPageURL = rtrim(ltrim(curPageURL(), '/'), '/');
	$page_url_array = ( explode('/', $curPageURL ) );
	$page_url_count = count($page_url_array);


		if ($page_url_count == 1) {
			// homepage
			$page_lines = file(root.'/pages/home/page.txt');
			$dir_exists = true;

		} else if ($page_url_count == 2) {

			foreach ($root_pages as $root_page) {
				$folder = basename($root_page);
				$desired_request = substr($folder, 3);
				// page in menu
				if ($folder[2] == '_' && is_numeric(substr($folder, 0, 2) ) && $request == $desired_request) {
					$page_lines = file(root.'/pages/'.$folder.'/page.txt');
					$dir_exists = true;
					break;
				// page not in menu
				} else if ($folder == $request) {
					$page_lines = file(root.'/pages/'.$folder.'/page.txt');
					$dir_exists = true;
					break;
				}
			}

		} else if ($page_url_count == 3) {

			$url_array = explode('/', request);
			$request = $url_array[1];
			$request_ii = $url_array[2];

			foreach ($root_pages as $root_page) {
				$folder = basename($root_page);
				$desired_request = substr($folder, 3);
				$next_level_dirs = glob(root.'/pages/'.$folder.'/*');

				// does root folder exist?
				if ($folder[2] == '_' && is_numeric(substr($folder, 0, 2) ) && $request == $desired_request ) {
					
					foreach ($next_level_dirs as $next_level_dir) {
						$folder_ii = basename($next_level_dir);
						$desired_request_ii = substr($folder_ii, 3);
						if ( $folder_ii[2] == '_' && is_numeric(substr($folder_ii, 0, 2))  && $request_ii == $desired_request_ii ) {
							$page_lines = file(root.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						} else if ($folder_ii == $request_ii){
							$page_lines = file(root.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						}
					}

				} else if ($folder == $request) {

					foreach ($next_level_dirs as $next_level_dir) {
						$folder_ii = basename($next_level_dir);
						$desired_request_ii = substr($folder_ii, 3);
						if ( $folder_ii[2] == '_' && is_numeric(substr($folder_ii, 0, 2))  && $request_ii == $desired_request_ii ) {
							$page_lines = file(root.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						} else if ($folder_ii == $request_ii){
							$page_lines = file(root.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						}
					}
				}
			}


		} else if ($page_url_count = '4') {

		} else if ($page_url_count = '5') {

		}

		// 404, folder doesnt exist / page.txt doesn't exist
		if ($page_lines == 0 && $dir_exists) {
			// page.txt doesn't exist
			$page_lines = file(root.'/pages/404/page.txt');
		} else if ($page_lines == 0) {
			// folder does not exist, genuine 404
			$page_lines = file(root.'/pages/404/page.txt');
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
		return trim(implode(PHP_EOL, $multi_lines));
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

	

	// Markdown support!
	include_once('parsedown.php');
	function print_markdown($term) {
		$markdown = new Parsedown();
		echo $markdown->text(get_content($term));
	}




	

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