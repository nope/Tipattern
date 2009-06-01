<?php
// I stole plenty of code from zem. Don't tell him! ;)
	
if (!defined('txpinterface')) die('txpinterface is undefined.');		
	
	if ($event == 'cache') {

		require_privs('cache');

		$available_steps = array(
			'lista',
			'clean'
		);

		if(!$step or !in_array($step, $available_steps)){
			$step = 'lista';
		}
		$step();
	}
	
	function	lista($message = '')
	{
		global $prefs;

		extract($prefs);

		pagetop("Cache Cleaner");
		
		echo "<div align=\"center\" style=\"margin-top:3em\">";
		echo form(
			tag("Cache-Cleaner", "h3").
			graf("Usually you don't need to do that. Cache is <b>automatically</b> cleared <br />1)
				  after a certain amount of time <br />2) when a comment is posted, edited or moderated
			      <br />3) after a page-template or form-tag is modified.<br />4) after template import.<br />5) after article update.<br /><br />".
				fInput("hidden", "txp_token", md5($lastmod)).
				fInput("submit", "clean_cache", "Clean all cached Files", "smallerbox").
				eInput("cache").sInput("clean")
			," style=\"text-align:center\"")
		);
		echo tag("Cache Statistics","h3");
		global $path_to_site;$count = array('size'=>0, 'num'=>0);
		$txp_cache_dir = txpath."/cache";
		if (!empty($txp_cache_dir) and $fp = opendir($txp_cache_dir)) {
			while (false !== ($file = readdir($fp))) {
				if ($file{0} != ".") {
					$count['size'] += filesize("$txp_cache_dir/$file");
					++$count['num'];
				}
			}
			closedir($fp);
			printf("There are %d cache files with a total size of %d kb.", $count['num'], floor($count['size']/1000));
		} else { echo "Cache is empty.";}
		include $path_to_site .'/textpattern/lib/txp_cache/cache-config.php';
		
			echo "</div>";

	}
	
	function	clean($message = '')
	{
		global $prefs;

		extract($prefs);
		
		pagetop("Cache Cleaner", ( (ps("txp_token") === (md5($lastmod)))
				? "Successful"
				: "Token expired. Please try again."));
		if (ps("txp_token") === (md5($lastmod)))
		{
			echo "<div align=\"center\" style=\"margin-top:3em\">";
			printf("Deleted %s files. Cache is clean.",''.txp_flushdir(true));
			echo "</div>";
		}
		
		echo "<div align=\"center\" style=\"margin-top:3em\">";
		echo form(
			tag("Cache-Cleaner", "h3").
			graf("Usually you don't need to do that. Cache is <b>automatically</b> cleared <br />1)
				  after a certain amount of time <br />2) when a comment is posted, edited or moderated
			      <br />3) after a page-template or form-tag is modified.<br />4) after template import.<br />5) after article update.<br /><br />".
				fInput("hidden", "txp_token", md5($lastmod)).
				fInput("submit", "clean_cache", "Clean all cached Files", "smallerbox").
				eInput("cache").sInput("clean")
			," style=\"text-align:center\"")
		);
		echo tag("Cache Statistics","h3");
		global $path_to_site;$count = array('size'=>0, 'num'=>0);
		$txp_cache_dir = txpath."/cache";
		if (!empty($txp_cache_dir) and $fp = opendir($txp_cache_dir)) {
			while (false !== ($file = readdir($fp))) {
				if ($file{0} != ".") {
					$count['size'] += filesize("$txp_cache_dir/$file");
					++$count['num'];
				}
			}
			closedir($fp);
			printf("There are %d cache files with a total size of %d kb.", $count['num'], floor($count['size']/1000));
		} else { echo "Cache is empty.";}
		include $path_to_site .'/textpattern/lib/txp_cache/cache-config.php';
		
			echo "</div>";
	}
	
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

?>