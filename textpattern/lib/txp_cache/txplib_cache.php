<?php

/*
if (function_exists("register_callback")) {
	register_callback("txp_flush_event", "article", "edit");
	register_callback("txp_flush_event", "article", "create");
	register_callback("txp_flush_event", "link");
	register_callback("txp_flush_event", "page", "page_save");
	register_callback("txp_flush_event", "form", "form_save");
	register_callback("txp_flush_event", "list", "list_multi_edit");
	register_callback("txp_flush_event", "discuss");
	// We do not have a callback when comments are posted on the front_end
	// but that's ok, I hacked some magic into cache-main.php
}
*/

// This function clears the Cache directory. Make sure cache is installed in the right directory.
function txp_flushdir($force_clean = false) {
	global $path_to_site, $lastmod;

	$count = 0;
	$txp_cache_dir = txpath.'/cache';

	if (!empty($txp_cache_dir) and $fp = opendir($txp_cache_dir)) {
		$last = strtotime($lastmod);
		while (false !== ($file = readdir($fp))) {
			if ($file{0} != "." AND
				 ((filemtime("$txp_cache_dir/$file") < $last) OR $force_clean)){
				@unlink("$txp_cache_dir/$file");
				++$count;
			}
		}

		closedir($fp);
	}

	return $count;
}

function txp_flushcachedir($force_clean = false) {
	global $path_to_site, $lastmod;

	$txp_cache_dir = txpath.'/cache';

	if (!empty($txp_cache_dir) and $fp = opendir($txp_cache_dir)) {
		$last = strtotime($lastmod);
		while (false !== ($file = readdir($fp))) {
			if ($file{0} != "." AND
				 ((filemtime("$txp_cache_dir/$file") < $last) OR $force_clean)){
				@unlink("$txp_cache_dir/$file");
			}
		}

		closedir($fp);
	}
	return;
}

// This is the callback-function when something in the Admin-Panel gets changed. (Wrapper)
function txp_flush_event($event, $step) {
	if ( ($event==='article')
		 && (($step==='create') || ($step==='edit'))
		 && ((count($_POST)==0) || ($_REQUEST['view']!='')) ) return;
	elseif (count($_POST)==0) return;
	$count = txp_flushdir(true);
}


?>