<?php

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

define ('DS', DIRECTORY_SEPARATOR);
define ('MAIN_DIR', __DIR__ . DS);
//define ('WAF_DIR', MAIN_DIR . 'WAF' . DS);
//define ('WMS_VIEWER_DIR', WMS_LIB_DIR . 'Viewer' . DS);

require_once (WMS_DIR . 'autoload.php');

