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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?> <?=$GLOBALS['lang']['seating']?>
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
		<script language="JavaScript" type="text/JavaScript">
			//<!--
			subcats = new Array();
			for (i=1; i<11; i++) {
				subcats[i] = new Array();
				for (j=1; j<11; j++) {
					subcats[i][j] = 0;
				}
			}
			
			function toggle (row, seat, seatID) {
				if (subcats[row][seat] == 0) {
					subcats[row][seat] =1;
					document.getElementById(seatID).className = "chairOn";
				} else if (subcats[row][seat] == 1) {
					subcats[row][seat] = 0;
					document.getElementById(seatID).className = "chair";
				}
			}
			
			function fertig () {
				opener.document.forms[0].seats_none.disabled = true;
				opener.document.forms[0].sitze.disabled = false;
				opener.document.forms[0].radio1.checked = false;
				opener.document.forms[0].radio2.checked = true;
				finalList = '';
				for (i=1; i<11; i++) {
					for (j=1; j<11; j++) {
						if (subcats[i][j] == 1) {
							finalList += i + ',' + j + ';';
						}
					}
				}
				myLen = finalList.length;
				opener.document.forms[0].sitze.value = finalList.slice(0, myLen - 1);
				window.close();
			}
			//-->
		</script>
	</head>
	<body>
		<?
		$count = 1;
			echo '<div id="top-row">&nbsp;</div>';
			for ($j=1; $j<11; $j++) {
				echo '<a href="javascript:void();" class="seats">'.$j.'</a>
';
				$count++;
			}
			echo '<br class="myclear" />';
		for ($i=1; $i<11; $i++) {
			echo '<div class="reihe">'.$GLOBALS['lang']['row'].' '.$i.'</div>';
			for ($j=1; $j<11; $j++) {
				echo '<a href="javascript:toggle('.$i.', '.$j.', \'seat'.$count.'\');" id="seat'.$count.'" class="chair">&nbsp;</a>
';
				$count++;
			}
			echo '<br class="myclear" />
';
		}
		?>
		<form>
			<input type="button" value="<?=$GLOBALS['lang']['done']?>" id="button-done" onclick="fertig();" />
		</form>
	</body>
</html>