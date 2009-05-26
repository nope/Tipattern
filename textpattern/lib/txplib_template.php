<?php

class template {
    function template() {
	global $prefs;
        global $TEMPLATES;

        $this->_config = $TEMPLATES;

    /*
        PRIVATE CONFIG
        ------------------------------------------------------
    */
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
        $missing_dir_text   = "The template directory `<strong>%1\$s</strong>` does not exist, and could not be automatically created.  Would you mind creating it yourself by running something like</p><pre><code>    mkdir %1\$s\n    chmod 777 %1\$s</code></pre><p>That should fix the issue.  You could also adjust the plugin's directory by modifying <code>\$templates['base_dir']</code> in the plugin's code.";
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

    function checkdir($dir = '', $type = TEMPLATES_EXPORT) {
        /*
            If $type == _EXPORT, then:
                1.  Check to see that /base/path/$dir exists, and is
                    writable.  If not, create it.
                2.  Check to see that /base/path/$dir/subdir_* exist,
                    and are writable.  If not, create them.

            If $type == _IMPORT, then:
                1.  Check to see that /base/path/$dir exists, and is readable.
                2.  Check to see that /base/path/$dir/subdir_* exist, and are readable.
        */
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
                case TEMPLATES_IMPORT:
                    if (!is_dir($curDir)) {
                        echo sprintf($this->_config['error_missing_dir'], $curDir);
                        return false;
                    }
                    if (!is_readable($curDir)) {
                        echo sprintf($this->_config['error_cant_read'], $curDir);
                        return false;
                    }
                    break;

                case TEMPLATES_EXPORT:
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

    /*
        EXPORT FUNCTIONS
        ----------------------------------------------------------
    */
    function export($dir = '') {
        if (!$this->checkdir($dir, TEMPLATES_EXPORT)) {
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

    /*
        IMPORT FUNCTIONS
        ----------------------------------------------------------
    */
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
        if (!$this->checkdir($dir, TEMPLATES_IMPORT)) {
            return;
        }

        /*
            Auto export into `preimport-data`
        */
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

?>