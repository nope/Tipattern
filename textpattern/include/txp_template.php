<?php
/* 
  txp_template.php
  web
  
  Created by luca sabato on 2009-05-25.
  Copyright 2009 http://sabatia.it. All rights reserved.

*/

if (!defined('txpinterface')) die('txpinterface is undefined.');

if ($event == 'template') {
	
	require_privs('template');
	
	include_once txpath.'/lib/txplib_template.php';

	$available_steps = array(
		'lista',
		'import',
		'export'
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
	
	$template = new template();
	
	pagetop(gTxt('template'), $message);
	print "
    <style type='text/css'>
        .success { color: #009900; }
        .failure { color: #FF0000; }
    </style>
			
    <table cellpadding='0' cellspacing='0' border='0' id='list' align='center'>
        <tr>
            <td>
    ";

	$importlist = $template->getTemplateList();

    print "
        <h1>Import Templates</h1>
    ".form(
          graf('Which template set would you like to import?'.
            selectInput('import_dir', $importlist, '', 1).
            fInput('submit', 'go', 'Go', 'smallerbox').
            eInput('template').sInput('import')
        )
  );

  print "
        <h1>Export Templates</h1>
    ".form(
          graf('Name this export:'.
            fInput('text', 'export_dir', '').
            fInput('submit', 'go', 'Go', 'smallerbox').
            eInput('template').sInput('export')
        )
  );
	
	print "
          </td>
      </tr>
  </table>
  ";
	
}

function	import($message = '')
{
	global $prefs;

	extract($prefs);
	
	$template = new template();
	
	pagetop(gTxt('template'), $message);
	print "
    <style type='text/css'>
        .success { color: #009900; }
        .failure { color: #FF0000; }
    </style>
			
    <table cellpadding='0' cellspacing='0' border='0' id='list' align='center'>
        <tr>
            <td>
    ";

	$template->import(ps('import_dir'));
	
	print "
          </td>
      </tr>
  </table>
  ";
}

function	export($message = '')
{
	global $prefs;

	extract($prefs);
	
	$template = new template();
	
	pagetop(gTxt('template'), $message);
	print "
    <style type='text/css'>
        .success { color: #009900; }
        .failure { color: #FF0000; }
    </style>
			
    <table cellpadding='0' cellspacing='0' border='0' id='list' align='center'>
        <tr>
            <td>
    ";

	$dir = ps('export_dir');

  $dir =  str_replace(
              array(" "),
              array("-"),
              $dir
            );
  $template->export($dir);

	print "
          </td>
      </tr>
  </table>
  ";
}

?>