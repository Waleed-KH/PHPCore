<?php

spl_autoload_register(function ($class) {
	//$prefix = 'WMS\\';

	//$len = strlen($prefix);

	//if (strncmp($prefix, $class, $len) !== 0) {
	//    return;
	//}

	$relativeClass = $class;//substr($class, $len);

	$fileName = str_replace('\\', DS, $relativeClass) . '.php';

	if (file_exists(WMS_DIR . $fileName)) {
		require WMS_DIR . $fileName;
	}

	// TODO: Initialize static class
});

?>