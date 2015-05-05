<?php
$library = "..";

require_once "$library/org.hailong.service/service.php";
require_once "$library/org.hailong.ui/ui.php";
require_once "$library/org.hailong.account/account.php";

require_once "configs/config.php";

require_once "cook.php";

session_start();
	

Shell::staticRun(config(), new SessionViewStateAdapter("cook/admin/foodedit"),"views/food_edit.html", "FoodEditController");


?>