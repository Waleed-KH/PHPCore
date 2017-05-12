<?php

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

define ('DS', DIRECTORY_SEPARATOR);
define ('MAIN_DIR', __DIR__ . DS);
define('DIR', __DIR__ . DS);
define('CORE_DIR', DIR . 'Core' . DS);
define('WMS_DIR', DIR . 'WMS' . DS);
define('WMS_VIEWER_DIR', WMS_DIR . 'Viewer' . DS);

require_once (DIR . 'autoload.php');

WMS\WMS::Initialize();
