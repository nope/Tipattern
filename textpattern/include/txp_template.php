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
	

	$available_steps = array(
		'list',
		'import',
		'export'
	);

	if(!$step or !in_array($step, $available_steps)){
		$step = 'list';
	}
	$step();
}

function	list($message = '')
{
	pagetop(gTxt('template'), $message);
}



/*


    $hcg_templates = array(
        "base_dir"      =>  "_templates",

        "subdir_pages"  =>  "pages",
        "subdir_forms"  =>  "forms",
        "subdir_css"    =>  "style",

        "ext_pages"     =>  ".page",
        "ext_forms"     =>  ".form",
        "ext_css"       =>  ".css"
    );


    define('_HCG_TEMPLATES_IMPORT', 1);
    define('_HCG_TEMPLATES_EXPORT', 2);
    $GLOBALS['_HCG_TEMPLATES'] = $hcg_templates;

        function hcg_templates($event, $step) {
			$GLOBALS['prefs'] = get_prefs();
            $template = new hcg_template();

    		pagetop("Process Templates", "");
		    print "
	        <style type='text/css'>
	            .success { color: #009900; }
	            .failure { color: #FF0000; }
	        </style>

	        <table cellpadding='0' cellspacing='0' border='0' id='list' align='center'>
	            <tr>
	                <td>
	        ";

	        switch ($step) {
	            case "import":
	                $template->import(ps('import_dir'));
	                break;

	            case "export":
	                $dir = ps('export_dir');

	                $dir =  str_replace(
	                            array(" "),
	                            array("-"),
	                            $dir
                            );
	                $template->export($dir);
	                break;

	            default:
	                $importlist = $template->getTemplateList();

                    print "
                        <h1>Import Templates</h1>
                    ".form(
                          graf('Which template set would you like to import?'.
                            selectInput('import_dir', $importlist, '', 1).
                            fInput('submit', 'go', 'Go', 'smallerbox').
                            eInput('hcg_templates').sInput('import')
                        )
        	        );

        	        print "
                        <h1>Export Templates</h1>
                    ".form(
                          graf('Name this export:'.
                            fInput('text', 'export_dir', '').
                            fInput('submit', 'go', 'Go', 'smallerbox').
                            eInput('hcg_templates').sInput('export')
                        )
        	        );

	                break;
	        }
	        print "
	                </td>
	            </tr>
	        </table>
	        ";
        }

    class hcg_template {
        function hcg_template() {
			global $prefs;
            global $_HCG_TEMPLATES;

            $this->_config = $_HCG_TEMPLATES;


            $this->_config['root_path']         =   $prefs['path_to_site'];
            $this->_config['full_base_path']    =   sprintf(
                                                        "%s/%s",
                                                        $this->_config['root_path'],
                                                        $this->_config['base_dir']
                                                    );

            $this->_config['error_template']    =   "
                <h1 class='failure'>%s</h1>
                <p>%s</p>
            ";

            $missing_dir_head   = "Template Directory Missing";
            $missing_dir_text   = "The template directory `<strong>%1\$s</strong>` does not exist, and could not be automatically created.  Would you mind creating it yourself by running something like</p><pre><code>    mkdir %1\$s\n    chmod 777 %1\$s</code></pre><p>That should fix the issue.  You could also adjust the plugin's directory by modifying <code>\$hcg_templates['base_dir']</code> in the plugin's code.";
            $cant_write_head    = "Template Directory Not Writable";
            $cant_write_text    = "I can't seem to write to the template directory `<strong>%1\$s</strong>`.  Would you mind running something like</p><pre><code>    chmod 777 %1\$s</code></pre><p>to fix the problem?";
            $cant_read_head     = "Template Directory Not Readable";
            $cant_read_text     = "I can't seem to read from the template directory `<strong>%1\$s</strong>`.  Would you mind running something like</p><pre><code>    chmod 777 %%1\$s</code></pre><p>to fix the problem?";


            $this->_config['error_missing_dir'] =   sprintf(
                                                        $this->_config['error_template'],
                                                        $missing_dir_head,
                                                        $missing_dir_text
                                                    );
            $this->_config['error_cant_write']  =   sprintf(
                                                        $this->_config['error_template'],
                                                        $cant_write_head,
                                                        $cant_write_text
                                                    );
            $this->_config['error_cant_read']   =   sprintf(
                                                        $this->_config['error_template'],
                                                        $cant_read_head,
                                                        $cant_read_text
                                                    );

            $this->exportTypes = array(
                "pages" =>  array(
                                "ext"       =>  $this->_config['ext_pages'],
                                "data"      =>  "user_html",
                                "fields"    =>  "name, user_html",
                                "nice_name" =>  "Page Files",
                                "regex"     =>  "/(.+)".$this->_config['ext_pages']."/",
                                "sql"       =>  "`user_html` = '%s'",
                                "subdir"    =>  $this->_config['subdir_pages'],
                                "table"     =>  "txp_page"
                            ),
                "forms" =>  array(
                                "ext"       =>  $this->_config['ext_forms'],
                                "data"      =>  "Form",
                                "fields"    =>  "name, type, Form",
                                "nice_name" =>  "Form Files",
                                "regex"     =>  "/(.+)\.(.+)".$this->_config['ext_forms']."/",
                                "sql"       =>  "`Form` = '%s', `type` = '%s'",
                                "subdir"    =>  $this->_config['subdir_forms'],
                                "table"     =>  "txp_form"
                            ),
                "css"   =>  array(
                                "ext"       =>  $this->_config['ext_css'],
                                "data"      =>  "css",
                                "fields"    =>  "name, css",
                                "nice_name" =>  "CSS Rules",
                                "regex"     =>  "/(.+)".$this->_config['ext_css']."/",
                                "sql"       =>  "`css` = '%s'",
                                "subdir"    =>  $this->_config['subdir_css'],
                                "table"     =>  "txp_css"
                            )
            );
        }

        function checkdir($dir = '', $type = _HCG_TEMPLATES_EXPORT) {
          
            $dir =  sprintf(
                        "%s/%s",
                        $this->_config['full_base_path'],
                        $dir
                    );

            $tocheck =  array(
                            $dir,
                            $dir.'/'.$this->_config['subdir_pages'],
                            $dir.'/'.$this->_config['subdir_css'],
                            $dir.'/'.$this->_config['subdir_forms']
                        );
            foreach ($tocheck as $curDir) {
                switch ($type) {
                    case _HCG_TEMPLATES_IMPORT:
                        if (!is_dir($curDir)) {
                            echo sprintf($this->_config['error_missing_dir'], $curDir);
                            return false;
                        }
                        if (!is_readable($curDir)) {
                            echo sprintf($this->_config['error_cant_read'], $curDir);
                            return false;
                        }
                        break;

                    case _HCG_TEMPLATES_EXPORT:
                        if (!is_dir($curDir)) {
                            if (!@mkdir($curDir, 0777)) {
                                echo sprintf($this->_config['error_missing_dir'], $curDir);
                                return false;
                            }
                        }
                        if (!is_writable($curDir)) {
                            echo sprintf($this->_config['error_cant_write'], $curDir);
                            return false;
                        }
                        break;
                }
            }
            return true;
        }


        function export($dir = '') {
            if (!$this->checkdir($dir, _HCG_TEMPLATES_EXPORT)) {
                return;
            }

            foreach ($this->exportTypes as $type => $config) {
                print "
                    <h1>Exporting ".$config['nice_name']."</h1>
                    <ul class='results'>
                ";

                $rows = safe_rows($config['fields'], $config['table'], '1=1');

                foreach ($rows as $row) {
                    $filename       =   sprintf(
                                            "%s/%s/%s/%s%s",
                                            $this->_config['full_base_path'],
                                            $dir,
                                            $config['subdir'],
                                            $row['name'] . (isset($row['type'])?".".$row['type']:""),
                                            $config['ext']
                                        );
                    $nicefilename =     sprintf(
                                            ".../%s/%s/%s%s",
                                            $dir,
                                            $config['subdir'],
                                            $row['name'] . (isset($row['type'])?".".$row['type']:""),
                                            $config['ext']
                                        );

                    if (isset($row['css'])) {
                        $row['css'] = base64_decode($row['css']);
                    }

                    $f = @fopen($filename, "w+");
                    if ($f) {
                        fwrite($f,$row[$config['data']]);
                        fclose($f);
                        print "
                        <li><span class='success'>Successfully exported</span> ".$config['nice_name']." '".$row['name']."' to '".$nicefilename."'</li>
                        ";
                    } else {
                        print "
                        <li><span class='failure'>Failure exporting</span> ".$config['nice_name']." '".$row['name']."' to '".$nicefilename."'</li>
                        ";
                    }
                }
                print "
                    </ul>
                ";
            }
        }


        function getTemplateList() {
            $dir = opendir($this->_config['full_base_path']);

            $list = array();

            while(false !== ($filename = readdir($dir))) {
                if (
                    is_dir(
                        sprintf(
                            "%s/%s",
                            $this->_config['full_base_path'],
                            $filename
                        )
                    ) && $filename != '.' && $filename != '..'
                ) {
                    $list[$filename] = $filename;
                }
            }
            return $list;
        }

        function import($dir) {
            if (!$this->checkdir($dir, _HCG_TEMPLATES_IMPORT)) {
                return;
            }


            print "
                <h1>Backing up current template data</h1>
                <p>Your current template data will be available for re-import as `preimport-data`.</p>
            ";

            $this->export('preimport-data');

            $basedir =  sprintf(
                            "%s/%s",
                            $this->_config['full_base_path'],
                            $dir
                        );
            foreach ($this->exportTypes as $type => $config) {
                print "
                    <h1>Importing ".$config['nice_name']."</h1>
                    <ul class='results'>
                ";

                $exportdir =    sprintf(
                                    "%s/%s",
                                    $basedir,
                                    $config['subdir']
                                );

                $dir = opendir($exportdir);
                while (false !== ($filename = readdir($dir))) {
                    if (preg_match($config['regex'], $filename, $filedata)) {
                        $templateName = addslashes($filedata[1]);
                        $templateType = (isset($filedata[2]))?$filedata[2]:'';

                        $f =    sprintf(
                                    "%s/%s",
                                    $exportdir,
                                    $filename
                                );

		                if ($data = file($f)) {
    		                if ($type == 'css') {
    		                    $data = base64_encode(implode('', $data));
    		                } else {
    		                    $data = addslashes(implode('', $data));
    		                }
                            if (safe_field('name', $config['table'], "name='".$templateName."'")) {
                                $result = safe_update($config['table'], sprintf($config['sql'], $data, $templateType), "`name` = '".$templateName."'");
                                $success = ($result)?1:0;
                            } else {
                                $result = safe_insert($config['table'], sprintf($config['sql'], $data, $templateType).", `name` = '".$templateName."'");
                                $success = ($result)?1:0;
                            }
    		            }

    		            $success = true;
                        if ($success) {
                            print "<li><span class='success'>Successfully imported</span> file '".$filename."'</li>";
                        } else {
                            print "<li><span class='failure'>Failed importing</span> file '".$filename."'</li>";
                        }
                    }
                }

                print "
                    </ul>
                ";
            }
        }
    }
*/
?>