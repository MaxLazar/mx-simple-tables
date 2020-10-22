<div id="mxst_help">
<?php
  require_once PATH_THIRD.'mx_simple_tables/libraries/Parsedown.php';
  $Parsedown = new Parsedown();
	$r = file_get_contents(PATH_THIRD.'/mx_simple_tables/README.md');
  print $Parsedown->text($r);
?>
</div>

<style>
    #mxst_help pre {background-color: #333; padding:10px; border-left: 5px solid #c5f326; }
</style>