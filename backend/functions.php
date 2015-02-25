<?php

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







	// URL mapping


	function readFolder ( $path ) {
	
	  	// Open the folder
	  	$rootLength = strlen(ROOT.'/pages');
	  	$filenames = array();
		$files = glob($path.'/*');
		$directories = array();
		$validUrls = array();


		foreach ($files as $file) {
			if (is_dir($file)) {
				$directories[] = $file;
			}
		}

		foreach($directories as $directory) {
			$url = substr($directory, $rootLength);
			$urlParts = array();
			$urlParts = explode('/', $url);
			$newUrl = array();
			foreach ($urlParts as $urlPart) {
				if (is_numeric(substr($urlPart, 0, 2)) && $urlPart[2] = '_') {
					$newUrl[] = substr($urlPart,3);
				} else {
					$newUrl[] = $urlPart;
				}
				if ( implode ('/', $newUrl) == REQUEST ) {
					$page_lines = file($directory.'/page.txt');
					$page_lines_string = implode ('*/1!', $page_lines);
					define("PAGE_LINES_STRING", $page_lines_string);
					define("CURRENT_DIRECTORY", $directory);
					$pageFound = true;
					break;
				}

				if($pageFound) {
					break;
				}
			}
			if($pageFound) {
				break;
			}
			readFolder($directory);
		}
		return $pageFound;
	}

	$folderPath = ROOT.'/pages';

	if (empty(REQUEST)) {
		$page_lines = file(ROOT.'/pages/home/page.txt');
		$page_lines_string = implode ('*/1!', $page_lines);
		define("PAGE_LINES_STRING", $page_lines_string);
	} else {
		readFolder($folderPath);
		// by calling the function we load the vars
		if (readFolder($folderPath) == false) {
			
			$folderPath = $folderpath.'/';

			if (readFolder($folderPath) == false) {
				$page_lines = file(ROOT.'/pages/404/page.txt');
				$page_lines_string = implode ('*/1!', $page_lines);
				define("PAGE_LINES_STRING", $page_lines_string);
			}
		}
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




	function subMenu() {

		$subPages = glob(CURRENT_DIRECTORY .'/*');
		

		foreach ($subPages as $subPage) {
			if (is_dir($subPage)) {
				$folder = basename($subPage);

				if ( is_numeric(substr($folder, 0, 2)) && $folder[2] == '_') {
					$new_folder = $folder;
					$page_name = substr($new_folder, 3);
					$url = '/'.REQUEST.'/'.$page_name;
				} else {
					unset($new_folder);
					unset($page_name);
					break;
				}

				$page_vis_name = str_replace('-', ' ', str_replace('_', ' ', $page_name));
				$link_wrap = stripslashes('<li class="sub-nav-wrap-'.$page_name.'"><a class="sub-nav-link-"'.$page_name.' href="http://'.DOMAIN.$url.'">'.$page_vis_name.'</a></li>');
				$link_wrap = stripslashes($link_wrap);
				$links[] = $link_wrap;
			}

		}

		if (!empty($links)) {
			echo '<ul class="sub-nav-wrap">'.stripslashes(str_replace('","', '', rtrim(ltrim(json_encode($links),'["'), '""]'))).'</ul>';
	
		}
	}





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






	// alternate url reading





	



	


	

	// BUILD THE PAGES
	// include header first
	include_once(theme_server_loc().'/header.php');

	// include page next
	include_once(theme_server_loc().'/template-'.get_content('Template').'.php');

	// now include the footer
	include_once(theme_server_loc().'/footer.php');
	
?>