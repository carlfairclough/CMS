<?php





	// FIND OUT THE SERVER ROOT PATH AND DOMAIN
	$root = $_SERVER['DOCUMENT_ROOT'];
	$domain = strtolower($_SERVER[HTTP_HOST]);





	// FIND OUT WHAT THE CURRENT URL REQ IS
	$request =	rtrim(strtok(strtolower($_SERVER[REQUEST_URI]), '?'), '/');
	define ("REQUEST", $request);

	$request_depth = count(explode('/', $request)) - 1;




	$themeDirectory = '/themes/';








	// READ THE SETUP FILE AND GET ALL VARIABLES AT ONCE
	$setupFile = $root.'/setup.txt';
	$setup_lines = file($setupFile);
	$setup_lines_string = implode (',', $setup_lines);
	// define it to call it in the function later
	define("SETUP_LINES_STRING", $setup_lines_string);

	function get_settings($term) {
		// convert string back to array
		$setup_lines_array = explode(',' , SETUP_LINES_STRING);

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
	define("THEME_NAME", $theme_name);
	define("THEME_DIRECTORY", '/themes/');
	define("ROOT", $root);
	define("DOMAIN", $domain);





	// Theme location on the server
	function theme_server_loc () {
		define("THEME_SERVER_LOC", ROOT.THEME_DIRECTORY.THEME_NAME);
		return THEME_SERVER_LOC;
	}






	// Theme location url
	function theme_url_loc () {
		define("THEME_URL_LOC", DOMAIN.THEME_DIRECTORY.THEME_NAME);
		return 'http://'.THEME_URL_LOC;
	}






	// Get variables for page requested before including the template
	$root_pages = glob(ROOT.'/pages/*');
	$request =  trim($request, '/');
	define("REQUEST", $request);






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
		$link_wrap = stripslashes('<li class="main-nav-wrap-'.$page_name.'"><a class="main-nav-link-"'.$page_name.' href="http://'.DOMAIN.'/'.$page_name.'">'.$page_vis_name.'</a></li>');
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
			$page_lines = file(ROOT.'/pages/home/page.txt');
			$dir_exists = true;

		} else if ($page_url_count == 2) {

			foreach ($root_pages as $root_page) {
				$folder = basename($root_page);
				$desired_request = substr($folder, 3);
				// page in menu
				if ($folder[2] == '_' && is_numeric(substr($folder, 0, 2) ) && $request == $desired_request) {
					$page_lines = file(ROOT.'/pages/'.$folder.'/page.txt');
					$dir_exists = true;
					break;
				// page not in menu
				} else if ($folder == $request) {
					$page_lines = file(ROOT.'/pages/'.$folder.'/page.txt');
					$dir_exists = true;
					break;
				}
			}

		} else if ($page_url_count == 3) {

			$url_array = explode('/', REQUEST);
			$request = $url_array[1];
			$request_ii = $url_array[2];

			foreach ($root_pages as $root_page) {
				$folder = basename($root_page);
				$desired_request = substr($folder, 3);
				$next_level_dirs = glob(ROOT.'/pages/'.$folder.'/*');

				// does root folder exist?
				if ($folder[2] == '_' && is_numeric(substr($folder, 0, 2) ) && $request == $desired_request ) {
					
					foreach ($next_level_dirs as $next_level_dir) {
						$folder_ii = basename($next_level_dir);
						$desired_request_ii = substr($folder_ii, 3);
						if ( $folder_ii[2] == '_' && is_numeric(substr($folder_ii, 0, 2))  && $request_ii == $desired_request_ii ) {
							$page_lines = file(ROOT.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						} else if ($folder_ii == $request_ii){
							$page_lines = file(ROOT.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						}
					}

				} else if ($folder == $request) {

					foreach ($next_level_dirs as $next_level_dir) {
						$folder_ii = basename($next_level_dir);
						$desired_request_ii = substr($folder_ii, 3);
						if ( $folder_ii[2] == '_' && is_numeric(substr($folder_ii, 0, 2))  && $request_ii == $desired_request_ii ) {
							$page_lines = file(ROOT.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
							$dir_exists = true;
							break;
						} else if ($folder_ii == $request_ii){
							$page_lines = file(ROOT.'/pages/'.$folder.'/'.$folder_ii.'/page.txt');
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
			$page_lines = file(ROOT.'/pages/404/page.txt');
		} else if ($page_lines == 0) {
			// folder does not exist, genuine 404
			$page_lines = file(ROOT.'/pages/404/page.txt');
		}


	// convert page txt file to a string – stupid identifier to stop clashes from the
	$page_lines_string = implode ('*/1!', $page_lines);
	// define it to call it in the function later
	define("PAGE_LINES_STRING", $page_lines_string);

	function get_content($term) {
		// convert string back to array
		$page_lines_array = explode('*/1!' , PAGE_LINES_STRING);

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
		if (empty(REQUEST)) {
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
			$classes = REQUEST.' '.get_content('Template');
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




	$plugins = glob(ROOT.'/plugins/*');

	foreach ($plugins as $plugin) {
		$config = $plugin.'/config.txt';
		$function = $plugin.'/function.php';

		
		if (file_exists($plugin) && file_exists($config)) {
			// find the config file stuff – is it enabled? if so, turn it on
			$config_lines_string = implode ('*/1!', file($config));
			$config_lines_array = explode('*/1!' , $config_lines_string);

			foreach($config_lines_array as $line_num => $line) {

				if ( 0 === strpos(strtolower($line), strtolower('Status')) ) {
					$start_line_num = (int)$line_num + 1;
					$term_line_num = (int)$line_num + 1;
					break;
				}
			};

			$plugin_status = trim(rtrim(implode('', ( array_slice($config_lines_array, $term_line_num, $term_line_num))), '----'));

			if ($plugin_status == 'On') {
				include_once($function);
			} else {
				// off
			}
		}
	}



	

	// BUILD THE PAGES
	// include header first
	include_once(theme_server_loc().'/header.php');

	// include page next
	include_once(theme_server_loc().'/template-'.get_content('Template').'.php');

	// now include the footer
	include_once(theme_server_loc().'/footer.php');
	
?>