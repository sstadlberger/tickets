<?
require_once('include/config.inc.php');

lang();

function lang () {
	$lang = DEFAULT_LANG;
	if (isset($_GET['lang'])) {
		if (strlen($_GET['lang']) == 2 && preg_match('/^[a-z][a-z]/', $_GET['lang'])) {
			if (in_array($_GET['lang'], $GLOBALS['INSTALLED_LANG'])) {
				$lang = $_GET['lang'];
				setcookie('lang', $_GET['lang'], time()+60*60*24*365*10);
			}
		}
	} elseif (isset($_COOKIE['lang'])) {
		if (strlen($_COOKIE['lang']) == 2 && preg_match('/^[a-z][a-z]/', $_COOKIE['lang'])) {
			if (in_array($_COOKIE['lang'], $GLOBALS['INSTALLED_LANG'])) {
				$lang = $_COOKIE['lang'];
			}
		}
	} else {
		$hal = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		if (preg_match('/^[a-z][a-z]/', $hal)) {
			if (in_array($hal, $GLOBALS['INSTALLED_LANG'])) {
				$lang = $hal;
				setcookie('lang', $hal, time()+60*60*24*365*10);
			}
		} else {
			setcookie('lang', $lang, time()+60*60*24*365*10);
		}
	}
	include('include/lang.'.$lang.'.php');
}
$hid = 5;
if (isset($_GET['id']))	{
	if (is_numeric($_GET['id'])) {
		if (round($_GET['id']) >= 0 && round($_GET['id']) < 5) {
			$hid = round($_GET['id']);
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?> <?=$GLOBALS['lang']['help_t']?>
		</title>
		<style type="text/css" media="screen">@import "c/main.css";</style>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="de-de" />
		<meta name="ROBOTS" content="ALL" />
		<meta name="Copyright" content="Copyright (c) 2005 yadur studios" />
		<meta http-equiv="imagetoolbar" content="no" />
		<meta name="MSSmartTagsPreventParsing" content="true" />
		<link rel="Shortcut Icon" href="/favicon.ico" type="image/x-icon" />
		<meta name="description" content="Heimkino Ticket Designer" />
		<meta name="keywords" content="Heimkino Ticket Designer" />
		<meta name="Rating" content="General" />
		<meta name="revisit-after" content="30 Days" />
		<meta name="doc-class" content="Living Document" />
	</head>
	<body>
	<?=$GLOBALS['lang']['help'][$hid]?>
	</body>
</html>