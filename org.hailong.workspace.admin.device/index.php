<?php
$library = "..";

require_once "$library/org.hailong.service/service.php";
require_once "$library/org.hailong.ui/ui.php";
require_once "$library/org.hailong.account/account.php";

require_once "configs/config.php";

require_once "device.php";

session_start();
	

Shell::staticRun(config(), new SessionViewStateAdapter("workspace/admin/device/index"),"views/device.html", "DeviceSearchController");


?>