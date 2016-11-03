<?php

include_once "commonFunctions.inc.php";

$pluginName="model-testing";
$pluginVersion="1.0";

$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";

$gitURL = "https://github.com/cjd/model-testing.git";

if(isset($_POST['updatePlugin']))
{
    logEntry("updating plugin...");
    $updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

    echo $updateResult."<br/> \n";
}


if (isset($_FILES['modelfile'])) {
	if (file_exists($_FILES['modelfile']['tmp_name'])) {
		$csvData = file_get_contents($_FILES['modelfile']['tmp_name']);
		$lines = explode(PHP_EOL, $csvData);
		$array = array();
		foreach ($lines as $line) {
			$array[] = str_getcsv($line);
		}
		$mapfile = fopen ($settings['channelMemoryMapsFile'], "w");
		for ($i = 1; $i < count($array); ++$i) {
			$model = $array[$i];
			if ($model[0] == "") {
				$i=count($array);
			} else {
				$modelline = $model[0] . "," . $model[10] . "," . $model[8] . ",horizontal,TL,1,1\n";
				fwrite($mapfile, $modelline);
			}
		}
		fclose($mapfile);
		print "<center><b>Models imported - Restart FPPD to apply changes</b></center><br>";

	}
}
?>

<div id="start" class="settings">
<fieldset>
<legend>Model Import</legend>

<form enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="60000" />
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="modelfile" type="file" />
    <input type="submit" value="Upload xlights models export (csv)" />
</form>

</fieldset>

<?
 if(file_exists($pluginUpdateFile))
 {
    //echo "updating plugin included";
    include $pluginUpdateFile;
}
?>
<p>To report a bug, please file it against <?php echo $gitURL;?>

</div>

<br />
