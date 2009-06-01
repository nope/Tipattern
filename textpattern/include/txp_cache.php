<?php
// I stole plenty of code from zem. Don't tell him! ;)
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
if (!defined('txpinterface'))
	{
		die('txpinterface is undefined.');
	}

	// Add a new tab to the Content area.
	if ($event == 'cache') {
		require_privs('cache');
		register_tab("admin", "txp_cache", "cache-cleaner");
		register_callback("txp_cachecleaner", "txp_cache");
	}

	// This is the callback-function when something in the Admin-Panel gets changed. (Wrapper)
	function txp_flush_event($event, $step) {
		if ( ($event==='article')
			 && (($step==='create') || ($step==='edit'))
			 && ((count($_POST)==0) || ($_REQUEST['view']!='')) ) return;
		elseif (count($_POST)==0) return;
		$count = txp_flushdir(true);
	}

	// This is the Callback-Function for the Admin-CP
	function txp_cachecleaner($event, $step) {
		global $lastmod,$prefs,$path_to_site;
		// ps() returns the contents of POST vars, if any;
		if (ps("step") === "clean")
		{
			pagetop("JPCache Cleaner", ( (ps("txp_token") === (md5($lastmod)))
					? "Successful"
					: "Token expired. Please try again."));
			if (ps("txp_token") === (md5($lastmod)))
			{
				echo "<div align=\"center\" style=\"margin-top:3em\">";
				printf("Deleted %s files. Cache is clean.",''.txp_flushdir(true));
				echo "</div>";
			}
		} else {
			pagetop("JPCache Cleaner");
		}
		echo "<div align=\"center\" style=\"margin-top:3em\">";
		echo form(
			tag("JPCache-Cleaner", "h3").
			graf("Usually you don't need to do that. Cache is <b>automatically</b> cleared <br />1)
				  after a certain amount of time <br />2) when a comment is posted, edited or moderated
			      <br />3) after a page-template or form-tag is is modified.<br /><br />".
				fInput("hidden", "txp_token", md5($lastmod)).
				fInput("submit", "clean_cache", "Clean all cached Files", "smallerbox").
				eInput("txp_cache").sInput("clean")
			," style=\"text-align:center\"")
		);
		echo tag("Cache Statistics","h3");
		global $path_to_site;$count = array('size'=>0, 'num'=>0);
		$txp_cache_dir = $path_to_site .'/cache/cache';
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
		include $path_to_site .'/lib/txp_cache/cache-config.php';
/*		if (@$JPCACHE_TXPLOG_DO == 1 && $prefs['logging']=='all'){
			echo tag("Read-Write-Ratio<sup>1</sup>","h3");;
			$cachehits = safe_field('COUNT( id ) as hit', 'txp_log', "page LIKE '%#cachehit'");
			$totalhits = getThing("SELECT MIN(time) FROM ".PFX."txp_log WHERE page LIKE '%#cachehit'");
			$totalhits = getThing("SELECT COUNT(id) FROM ".PFX."txp_log WHERE time > '". $totalhits."'");
			printf("There were <b>%d</b> cache-reads recorded and <b>%d</b> possible cache-writes. <br />Average number of reads per write: <b>%01.2f</b>",$cachehits, $totalhits-$cachehits, (($totalhits-$cachehits) > 0) ? ($cachehits/($totalhits-$cachehits)) : '0');
			echo "<br /><br /><sup>1</sup>This is a (low) Approximation. Initially wait a week before numbers become meaningful.";
		}
*/		echo "</div>";
	}

	// This function clears the Cache directory. Make sure cache is installed in the right directory.
	function txp_flushdir($force_clean = false) {
		global $path_to_site, $lastmod;

		$count = 0;
		$txp_cache_dir = $path_to_site .'/cache/cache';

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