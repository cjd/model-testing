<?php
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
		$my_models[$array[0]] = $array[0];
	}
}

?>

<script type="text/javascript">
function buttonClicked(cell, model) {
	var bgcolor=$(cell).css("backgroundColor");
	var rgb = bgcolor.replace(/\s/g,'').match(/^rgba?\((\d+),(\d+),(\d+)/i);
	if (parseInt(rgb[1]) == 255) {
		$(cell).animate({'backgroundColor': "#888888"},100);
		ModelOff(model);
	} else {
		$(cell).animate({'backgroundColor': "#ffff00"},100);
		ModelOn(model);
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
<div id="start" class="settings">
<fieldset>
<legend>Model testing</legend>

<table border=1 width='100%' height='90%' bgcolor='#000000'>
<tr>
<?php

$buttonCount = 0;

foreach ($my_models as $model) {
	$buttonCount++;
	if (($buttonCount > 1) && (($buttonCount % 2) == 1))
		echo "</tr><tr>\n";

	printf( "<td width='50%%' bgcolor='#888888' align='center' onClick='buttonClicked(this, \"%s\");'><b><font size='10px'>%s</font></b></td>\n", $model, $model);
}
if (($buttonCount % 2) == 0)
	echo "<td bgcolor='black'>&nbsp;</td>\n";
?>
</tr>
</table>

<form enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="60000" />
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="modelfile" type="file" />
    <input type="submit" value="Upload xlights models export (csv)" />
</form>

</fieldset>
</div>

<br />
