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

$FPPMM = $settings['fppBinDir']."/fppmm";

if (isset($_REQUEST['command'])) {
	if ($_REQUEST['command'] == "on") {
		$cmd = $FPPMM . " -m \"" . $_REQUEST['model'] . "\" -o on";
		exec($cmd);
		$cmd = $FPPMM . " -m \"" . $_REQUEST['model'] . "\" -s 50";
		exec($cmd);
	} else {
		$cmd = $FPPMM . " -m \"" . $_REQUEST['model'] . "\" -s 0";
		exec($cmd);
		$cmd = $FPPMM . " -m \"" . $_REQUEST['model'] . "\" -o off";
		exec($cmd);
	}
}

$my_models = Array();
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
if (file_exists($settings['channelMemoryMapsFile'])) {
	$csvData = file_get_contents($settings['channelMemoryMapsFile']);
	$lines = explode(PHP_EOL, $csvData);
	$my_models = array();
	foreach ($lines as $line) {
		$array = str_getcsv($line);
		if ($array[0] != "") {
			$my_models[$array[0]] = $array[0];
		}
	}
}

?>

<script type="text/javascript">
function buttonClicked(cell, model) {
	var bgcolor=$(cell).css("backgroundColor");
	var rgb = bgcolor.replace(/\s/g,'').match(/^rgba?\((\d+),(\d+),(\d+)/i);
	//f (parseInt(rgb[1]) == 255) {
	if ($(cell).hasClass("modeloff")) {
		$(cell).removeClass("modeloff");
		$(cell).addClass("modelon");
		ModelOn(model);
	} else {
		$(cell).removeClass("modelon");
		$(cell).addClass("modeloff");
		ModelOff(model);
	}
}

function ModelOn(model){
	var xmlhttp=new XMLHttpRequest();
	var url = self.location.href + "&command=on&model=" + model;
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader('Content-Type', 'text/xml');
	xmlhttp.send();
}

function ModelOff(model){
	var xmlhttp=new XMLHttpRequest();
	var url = self.location.href + "&command=off&model=" + model;
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader('Content-Type', 'text/xml');
	xmlhttp.send();
}
</script>

<style>
td {
	text-align: center;
	font-size: 30px;
	text-overflow: ellipsis;
	overflow: hidden;
	max-width:1px;
}

.modelon {
	background-color: #ffff00;
}

.modeloff {
	background-color: #dddddd;
}
</style>
<div id="start" class="settings">
<fieldset>
<legend>Model testing</legend>

<table border=1 width='100%' height='90%' bgcolor='#222222'>
<colgroup>
<col width="50%"/>
<col width="50%"/>
</colgroup>
<tr>
<?php

$buttonCount = 0;

foreach ($my_models as $model) {
	$buttonCount++;
	if (($buttonCount > 1) && (($buttonCount % 2) == 1))
		echo "</tr><tr>\n";

	printf("<td onClick='buttonClicked(this, \"%s\");' class=modeloff><b>%s</b></td>\n", $model, $model);
}
?>
</tr>
</table>

<?
 if(file_exists($pluginUpdateFile))
 {
    //echo "updating plugin included";
    include $pluginUpdateFile;
}
?>
<p>To report a bug, please file it against <?php echo $gitURL;?>

</fieldset>
</div>

<br />
