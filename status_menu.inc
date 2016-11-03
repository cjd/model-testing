<?php

$pages = Array(
	'name' => "Model Testing",
	'type' => "status",
	'page' => "model-testing.php",
	);

?>

<li><a href="<?php echo preg_replace('/.*\/plugins\/(.*)/', 'plugin.php?plugin=$1&page=model-testing.php', dirname(__FILE__)); ?>">Model Testing</a></li>
