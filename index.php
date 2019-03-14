<?
require_once('include/config.inc.php');
require_once('include/common.inc.php');
require_once('include/start.inc.php');
require_once('include/fpdf.php');

start();

function start () {
	lang();
	if (isset($_GET['delete'])) {
		delete_ticket();
	} elseif (isset($_POST['doTheMacDaddy']) && isset($_POST['tickets'])) {
		if (count($_POST['tickets']) == 13) {
			gogogo();
		} else {
			init();
			print_html();
		}
	} else {
		init();
		print_html();
	}
}

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
	$GLOBALS['orderlang'] = $lang;
}

function gogogo () {
	$post = $_POST['tickets'];
	if (checkdate($post[8], $post[7], $post[9])) {
		$date = mktime(0, 0, 0, $post[8], $post[7], $post[9]);
		$data = $post[0];
		$rows = explode(';', preg_replace('/[^0-9,;\-]*/', '', $post[4]));
		$seatnumbers = array();
		if (count($rows) == 1) {
			if (is_numeric($rows[0])) {
				$count = $rows[0];
				for ($i=0; $i<$count; $i++) {
					$seatnumbers[$i] = $GLOBALS['lang']['freeseat'];
				}
			}
		}
		if (!isset($count)) {
			$count = 0;
			for ($i=0; $i<count($rows); $i++) {
				$seats = explode(',', $rows[$i]);
				if (count($seats) == 2) {
					$numbers = explode('-', $seats[1]);
					if (count($numbers) == 1) {
						if (is_numeric($numbers[0])) {
							$count++;
							array_push($seatnumbers, $GLOBALS['lang']['row'].' '.$seats[0].' | '.$GLOBALS['lang']['seat'].' '.$numbers[0]);
						} else {
							error('x003');
						}
					} elseif (count($numbers) == 2) {
						if (is_numeric($numbers[0]) && is_numeric($numbers[1])) {
							$count += abs($numbers[1] - $numbers[0]) + 1;
							if ($numbers[0] > $numbers[1]) {
								$tmp = $numbers[0];
								$numbers[0] = $numbers[1];
								$numbers[1] = $tmp;
							}
							for ($j=$numbers[0]; $j<=$numbers[1]; $j++) {
								array_push($seatnumbers, $GLOBALS['lang']['row'].$seats[0].' | '.$GLOBALS['lang']['seat'].' '.$j);
							}
						} else {
							error('x003');
						}
					} else {
						error('x003');
					}
				} else {
					error('x003');
				}
			}
		}
		if ($count > 100) {
			error('x004');
		}
		if (is_numeric($post[10]) && strlen($post[10]) == 2 && is_numeric($post[11]) && strlen($post[11]) == 2) {
			$zeit = $post[10].':'.$post[11];
		} elseif (is_numeric(substr($post[10], 0, 2)) && strlen($post[10]) == 4 && is_numeric($post[11]) && strlen($post[11]) == 2) {
			if (substr($post[10], 0, 1) == '0') {
				$hour = substr($post[10], 1, 1);
			} else {
				$hour = substr($post[10], 0, 2);
			}
			$zeit = $hour.':'.$post[11].' '.substr($post[10], 2, 2);
		} else {
			error('x005');
		}
		if (substr($data, strlen($data)-4) == '.tkt' && substr($data, 0, 1) != '.') {
			$file = TMPL_DIR.'/'.$data.'/info.xml';
			if (file_exists($file)) {
				$fp = fopen($file, 'rb');
				$file_content = fread($fp, filesize($file));
				fclose($fp);
				$xml = XMLtoArray($file_content);
/* => */		//echo '<pre>';
/* => */		//print_r($xml['TICKET']['VARIANTS']['VARIANT']);
				if (array_key_exists('content', $xml['TICKET']['VARIANTS']['VARIANT'])) {
					$variant = $xml['TICKET']['VARIANTS']['VARIANT'];
				} else {
					$variant = $xml['TICKET']['VARIANTS']['VARIANT'][$post[1]];
				}
				sort($variant['COLOUR']);
				sort($variant['IMAGE']);
/* => */		//print_r($variant);
				$template_variant = array();
				if (array_key_exists('content', $variant['COLOUR'])) {
					$tmp = explode(' ', trim($variant['COLOUR']['content']));
					$template_variant[$variant['COLOUR']['NAME']] = $tmp;
				} else {
					for ($i=0; $i<count($variant['COLOUR']); $i++) {
						$tmp = explode(' ', trim($variant['COLOUR'][$i]['content']));
						$template_variant[$variant['COLOUR'][$i]['NAME']] = $tmp;
					}
				}
				if (array_key_exists('IMAGE', $variant)) {
					if (array_key_exists('content', $variant['IMAGE'])) {
						$template_variant[trim($variant['IMAGE']['NAME'])] = trim($variant['IMAGE']['content']);
					} else {
						for ($i=0; $i<count($variant['IMAGE']); $i++) {
							$template_variant[$variant['IMAGE'][$i]['NAME']] = trim($variant['IMAGE'][$i]['content']);
						}
					}
				}
/* => */		//print_r($template_variant);
				// start pdf
				if (trim($xml['TICKET']['BASIC']['PAGE-HEIGHT']) > trim($xml['TICKET']['BASIC']['PAGE-WIDTH'])) {
					$orientation = 'P';
				} else {
					$orientation = 'L';
				}
				$pdf = new FPDF('P', 'mm', array(trim($xml['TICKET']['BASIC']['PAGE-WIDTH']), trim($xml['TICKET']['BASIC']['PAGE-HEIGHT'])));
				$pdf->SetAuthor('Stefan Stadlberger | yadur studios');
				$pdf->SetTitle('Tickets');
				$pdf->SetCreator('yadur studios Ticket Creator | http://ticket.yadur.com');
				$pdf->SetSubject('Tickets');
				$perpage = $xml['TICKET']['BASIC']['TICKETS-ROWS'] * $xml['TICKET']['BASIC']['TICKETS-COLUMS'];
				$template = array();
				if (array_key_exists('content', $xml['TICKET']['TEMPLATE']['OBJECT'])) {
					$template[0] = $xml['TICKET']['TEMPLATE']['OBJECT'];
				} else {
					$template = $xml['TICKET']['TEMPLATE']['OBJECT'];
				}
/* => */		//print_r($template);
				$offseti = 0;
				if (is_numeric($post[12])) {
					$offseti = fmod(round($post[12]), $perpage);
				}
				$start = 0 + $offseti;
				$stop = $count + $offseti;
				$new = true;
				for ($i=$start; $i<$stop; $i++) {
					if ($new) {
						$new = false;
						$pdf->AddPage();
					} else {
						if (fmod($i, $perpage) == 0) {
							$pdf->AddPage();
						}
					}
					$cordx = trim($xml['TICKET']['BASIC']['BORDER-LEFT']) + fmod($i, trim($xml['TICKET']['BASIC']['TICKETS-COLUMS'])) * (trim($xml['TICKET']['BASIC']['SPACE-VERTICAL']) + trim($xml['TICKET']['BASIC']['TICKET-WIDTH']));
					$cordy = trim($xml['TICKET']['BASIC']['BORDER-TOP']) + (floor($i/trim($xml['TICKET']['BASIC']['TICKETS-COLUMS'])) - floor($i/$perpage) * trim($xml['TICKET']['BASIC']['TICKETS-ROWS'])) * (trim($xml['TICKET']['BASIC']['SPACE-HORIZONTAL']) + trim($xml['TICKET']['BASIC']['TICKET-HEIGHT']));
					for ($j=0; $j<count($template); $j++) {
						switch ($template[$j]['TYPE']) {
							case 'static-text':
								$style = str_replace('N', '', trim($template[$j]['STYLE']));
								$pdf->SetFont(trim($template[$j]['FONT']), $style, trim($template[$j]['SIZE']));
								$pdf->SetTextColor($template_variant[trim($template[$j]['TEXT-COLOUR'])][0], $template_variant[trim($template[$j]['TEXT-COLOUR'])][1], $template_variant[trim($template[$j]['TEXT-COLOUR'])][2]);
								$offset = 0;
								if (trim($template[$j]['ALIGN']) == 'right') {
									$offset = $pdf->GetStringWidth(trim($template[$j]['TEXT']));
								} elseif (trim($template[$j]['ALIGN']) == 'center') {
									$offset = $pdf->GetStringWidth(trim($template[$j]['TEXT'])) / 2;
								}
								$pdf->Text($cordx+trim($template[$j]['X-CORD'])-$offset, $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['TEXT']));
								break;
							case 'dynamic-text':
								$text = '';
								switch (trim($template[$j]['TEXT'])) {
									case 'film':
										$text = stripslashes($post[5]);
										break;
									case 'subtitle':
										$text = stripslashes($post[6]);
										break;
									case 'seat':
										$text = $seatnumbers[$i-$offseti];
										break;
									case 'zeit':
										$text = $zeit;
										break;
									case 'tag':
										$text = date('j', $date);
										break;
									case 'monat':
										$months = $GLOBALS['lang']['months_pdf'];
										$text = $months[date('n', $date)-1];
										break;
									case 'jahr':
										$text = date('Y', $date);
										break;
									case 'kino':
										$text = stripslashes($post[2]);
										break;
									case 'info':
										$text = stripslashes($post[3]);
										break;
									case 'uhr':
										$text = $GLOBALS['lang']['uhr'];
										break;
								}
								$style = str_replace('N', '', trim($template[$j]['STYLE']));
								$pdf->SetFont(trim($template[$j]['FONT']), $style, trim($template[$j]['SIZE']));
								$pdf->SetTextColor($template_variant[trim($template[$j]['TEXT-COLOUR'])][0], $template_variant[trim($template[$j]['TEXT-COLOUR'])][1], $template_variant[trim($template[$j]['TEXT-COLOUR'])][2]);
								$offset = 0;
								if (trim($template[$j]['ALIGN']) == 'right') {
									$offset = $pdf->GetStringWidth($text);
								} elseif (trim($template[$j]['ALIGN']) == 'center') {
									$offset = $pdf->GetStringWidth($text) / 2;
								}
								$pdf->Text($cordx+trim($template[$j]['X-CORD'])-$offset, $cordy+trim($template[$j]['Y-CORD']), $text);
								break;
							case 'rectangle':
								$pdf->SetFillColor($template_variant[trim($template[$j]['RECT-COLOUR'])][0], $template_variant[trim($template[$j]['RECT-COLOUR'])][1], $template_variant[trim($template[$j]['RECT-COLOUR'])][2]);
								$pdf->Rect($cordx+trim($template[$j]['X-CORD']), $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['WIDTH']), trim($template[$j]['HEIGHT']), 'F');
								break;
							case 'picture':
								if ($template_variant[trim($template[$j]['PICT-ID'])] != 'none') {
									$pdf->Image(TMPL_DIR.'/'.$data.'/'.$template_variant[trim($template[$j]['PICT-ID'])], $cordx+trim($template[$j]['X-CORD']), $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['WIDTH']), trim($template[$j]['HEIGHT']));
								}
								break;
							case 'dynamic-picture':
/* => */						//print_r($_FILES);
/* => */						//echo 'das';
								if (isset($_FILES['bild'])) {
									if (!isset($img)) {
										$x = trim($template[$j]['WIDTH']) / 25.4 * 150;
										$y = trim($template[$j]['HEIGHT']) / 25.4 * 150;
										$img = scale_image($x, $y, 'zoom', $_FILES['bild']['tmp_name'], $_FILES['bild']['type'], 'png');
									}
									if ($img === false) {
										if ($template_variant[trim($template[$j]['PICT-ID'])] != 'none') {
											$pdf->Image(TMPL_DIR.'/'.$data.'/'.$template_variant[trim($template[$j]['PICT-ID'])], $cordx+trim($template[$j]['X-CORD']), $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['WIDTH']), trim($template[$j]['HEIGHT']));
										}
									} else {
										$pdf->Image(DYN_IMG_DIR.'/'.$img[0], $cordx+trim($template[$j]['X-CORD']), $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['WIDTH']), trim($template[$j]['HEIGHT']));
									}
								} else {
									if ($template_variant[trim($template[$j]['PICT-ID'])] != 'none') {
										$pdf->Image(TMPL_DIR.'/'.$data.'/'.$template_variant[trim($template[$j]['PICT-ID'])], $cordx+trim($template[$j]['X-CORD']), $cordy+trim($template[$j]['Y-CORD']), trim($template[$j]['WIDTH']), trim($template[$j]['HEIGHT']));
									}
								}
								break;
						}
					}
					if (isset($_POST['regmark'])) {
						$dist = trim($xml['TICKET']['BASIC']['REG-DISTANCE']);
						$len = trim($xml['TICKET']['BASIC']['REG-LENGTH']);
						$width = trim($xml['TICKET']['BASIC']['TICKET-WIDTH']);
						$height = trim($xml['TICKET']['BASIC']['TICKET-HEIGHT']);
						$pdf->SetLineWidth(0.2);
						$pdf->SetDrawColor(0);
						$pdf->Line($cordx-$dist, $cordy, $cordx-$dist-$len, $cordy);
						$pdf->Line($cordx, $cordy-$dist, $cordx, $cordy-$dist-$len);
						
						$pdf->Line($cordx+$width+$dist, $cordy, $cordx+$width+$dist+$len, $cordy);
						$pdf->Line($cordx+$width, $cordy-$dist, $cordx+$width, $cordy-$dist-$len);
						
						$pdf->Line($cordx-$dist, $cordy+$height, $cordx-$dist-$len, $cordy+$height);
						$pdf->Line($cordx, $cordy+$height+$dist, $cordx, $cordy+$height+$dist+$len);
						
						$pdf->Line($cordx+$width+$dist, $cordy+$height, $cordx+$width+$dist+$len, $cordy+$height);
						$pdf->Line($cordx+$width, $cordy+$height+$dist, $cordx+$width, $cordy+$height+$dist+$len);
					}
				}
				do {
					$p_name = 'tickets-'.time().'-'.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT).'.pdf';
				} while (file_exists(DOCUMENT_ROOT_PATH.'/tickets/'.$p_name));
				$pdf->Output(DOCUMENT_ROOT_PATH.'/tickets/'.$p_name, 'F');
				done($p_name);
				
			} else {
				error('x001');
			}
		} else {
			error('x001');
		}
	} else {
		error('x002');
	}
}

function error ($no) {
	$errors = $GLOBALS['lang']['errors'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?>
		</title>
		<style type="text/css" media="screen">@import "c/main.css";</style>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="de-de" />
		<meta name="ROBOTS" content="ALL" />
		<meta name="Copyright" content="Copyright (c) yadur studios" />
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
		<div id="main">
			<img src="i/banner.jpg" alt="main title" width="550" height="176" border="0" id="mainimage" />
			<h1><?=$GLOBALS['lang']['error_o']?></h1>
			<?= $errors[$no] ?><br /><br />
			<a href="javascript:history.go(-1);"><?=$GLOBALS['lang']['back']?></a>
			<br /><br />
			<a href="http://stadlberger.com" class="yadur" target="_blank">Stefan Stadlberger</a> | <a href="http://yadur.com" class="yadur" target="_blank">yadur studios</a> | <a href="http://validator.w3.org/check?uri=referer" class="yadur" target="_blank">XHTML</a> <a href="http://jigsaw.w3.org/css-validator/check/referer" class="yadur" target="_blank">CSS</a><br /><br />
		</div>
	</body>
</html><?
	exit;
}

function init () {
	// load templates
	$GLOBALS['templates'] = array();
	$dir = opendir(TMPL_DIR);
	$bla = true;
	while (false !== ($data = readdir($dir))) {
		if (substr($data, strlen($data)-4) == '.tkt' && substr($data, 0, 1) != '.') {
			$file = TMPL_DIR.'/'.$data.'/info.xml';
			if (file_exists($file)) {
				$fp = fopen($file, 'rb');
				$file_content = fread($fp, filesize($file));
				fclose($fp);				
				$xml = XMLtoArray($file_content);
				$variants = array();
				if (array_key_exists('content', $xml['TICKET']['VARIANTS']['VARIANT'])) {
					array_push($variants, $xml['TICKET']['VARIANTS']['VARIANT']['NAME']);
				} else {
					for ($i=0; $i<count($xml['TICKET']['VARIANTS']['VARIANT']); $i++) {
						array_push($variants, $xml['TICKET']['VARIANTS']['VARIANT'][$i]['NAME']);
					}
				}
				array_push($GLOBALS['templates'], array($data, trim($xml['TICKET']['BASIC']['DESIGN-NAME']), $variants, trim($xml['TICKET']['BASIC']['MAIN-LANG'])));
			}
		}
	}
	closedir($dir);
	$tmp = array();
	for ($i=0; $i<count($GLOBALS['templates']); $i++) {
		if ($GLOBALS['templates'][$i][3] == $GLOBALS['orderlang']) {
			array_push($tmp, $GLOBALS['templates'][$i]);
		}
	}
	for ($i=0; $i<count($GLOBALS['templates']); $i++) {
		if ($GLOBALS['templates'][$i][3] != $GLOBALS['orderlang']) {
			array_push($tmp, $GLOBALS['templates'][$i]);
		}
	}
	$GLOBALS['templates'] = $tmp;
}

function XMLtoArray($XML)
{
   $xml_parser = xml_parser_create();
   xml_parse_into_struct($xml_parser, $XML, $vals);
   xml_parser_free($xml_parser);
   $_tmp='';
   foreach ($vals as $xml_elem)
   { 
       $x_tag=$xml_elem['tag'];
       $x_level=$xml_elem['level'];
       $x_type=$xml_elem['type'];
       if ($x_level!=1 && $x_type == 'close')
       {
           if (isset($multi_key[$x_tag][$x_level]))
               $multi_key[$x_tag][$x_level]=1;
           else
               $multi_key[$x_tag][$x_level]=0;
       }
       if ($x_level!=1 && $x_type == 'complete')
       {
           if ($_tmp==$x_tag) 
               $multi_key[$x_tag][$x_level]=1;
           $_tmp=$x_tag;
       }
   }
   foreach ($vals as $xml_elem)
   { 
       $x_tag=$xml_elem['tag'];
       $x_level=$xml_elem['level'];
       $x_type=$xml_elem['type'];
       if ($x_type == 'open') 
           $level[$x_level] = $x_tag;
       $start_level = 1;
       $php_stmt = '$xml_array';
       if ($x_type=='close' && $x_level!=1) 
           $multi_key[$x_tag][$x_level]++;
       while($start_level < $x_level)
       {
             $php_stmt .= '[$level['.$start_level.']]';
             if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level]) 
                 $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
             $start_level++;
       }
       $add='';
       if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete'))
       {
           if (!isset($multi_key2[$x_tag][$x_level]))
               $multi_key2[$x_tag][$x_level]=0;
           else
               $multi_key2[$x_tag][$x_level]++;
             $add='['.$multi_key2[$x_tag][$x_level].']'; 
       }
       if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes',$xml_elem))
       {
           if ($x_type == 'open') 
               $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
           else
               $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
           eval($php_stmt_main);
       }
       if (array_key_exists('attributes',$xml_elem))
       {
           if (isset($xml_elem['value']))
           {
               $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
               eval($php_stmt_main);
           }
           foreach ($xml_elem['attributes'] as $key=>$value)
           {
               $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
               eval($php_stmt_att);
           }
       }
   }
     return $xml_array;
}

function done ($name) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?>
		</title>
		<style type="text/css" media="screen">@import "c/main.css";</style>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="de-de" />
		<meta name="ROBOTS" content="ALL" />
		<meta name="Copyright" content="Copyright (c) yadur studios" />
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
		<div id="main">
			<img src="i/banner.jpg" alt="main title" width="550" height="176" border="0" id="mainimage" />
			<h1><?=$GLOBALS['lang']['t_c']?></h1>
			<?=$GLOBALS['lang']['t1']?> <a href="<?=ROOT_PATH.'/tickets/'.$name?>" target="_blank"><?=$GLOBALS['lang']['tl']?></a> <?=$GLOBALS['lang']['t2']?>
			<br /><br />
			<a href="?delete=<?=$name?>"><?=$GLOBALS['lang']['delete_ticket']?></a>
			<br /><br />
			<a href="http://stadlberger.com" class="yadur" target="_blank">Stefan Stadlberger</a> | <a href="http://yadur.com" class="yadur" target="_blank">yadur studios</a> | <a href="http://validator.w3.org/check?uri=referer" class="yadur" target="_blank">XHTML</a> <a href="http://jigsaw.w3.org/css-validator/check/referer" class="yadur" target="_blank">CSS</a><br /><br />
		</div>
	</body>
</html><?
}

function delete_ticket () {
	$delete = $_GET['delete'];
	$output = $GLOBALS['lang']['deleted'];
	if (preg_match('/^tickets-[0-9]{10}-[0-9]{3}\.pdf/', $delete) && strlen($delete) == 26) {
		if (file_exists(DOCUMENT_ROOT_PATH.'/tickets/'.$delete)) {
			unlink(DOCUMENT_ROOT_PATH.'/tickets/'.$delete);
		} else {
			$output = $GLOBALS['lang']['errors']['x006'];
		}
	} else {
		$output = $GLOBALS['lang']['errors']['x007'];
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?>
		</title>
		<style type="text/css" media="screen">@import "c/main.css";</style>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="de-de" />
		<meta name="ROBOTS" content="ALL" />
		<meta name="Copyright" content="Copyright (c) yadur studios" />
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
		<div id="main">
			<img src="i/banner.jpg" alt="main title" width="550" height="176" border="0" id="mainimage" />
			<?=$output?>
			<br /><br />
			<a href="http://stadlberger.com" class="yadur" target="_blank">Stefan Stadlberger</a> | <a href="http://yadur.com" class="yadur" target="_blank">yadur studios</a> | <a href="http://validator.w3.org/check?uri=referer" class="yadur" target="_blank">XHTML</a> <a href="http://jigsaw.w3.org/css-validator/check/referer" class="yadur" target="_blank">CSS</a><br /><br />
		</div>
	</body>
</html><?
}

function print_html () {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?=$GLOBALS['lang']['t_c']?>
		</title>
		<style type="text/css" media="screen">@import "c/main.css";</style>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="de-de" />
		<meta name="ROBOTS" content="ALL" />
		<meta name="Copyright" content="Copyright (c) yadur studios" />
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
			subcats = new Array();<?
				for ($i=0; $i<count($GLOBALS['templates']); $i++) {
					echo '
			subcats['.$i.'] = new Array();';
					echo '
			subcats['.$i.'][0] = "'.$GLOBALS['templates'][$i][0].'";';
					echo '
			subcats['.$i.'][1] = new Array();';
					for ($j=0; $j<count($GLOBALS['templates'][$i][2]); $j++) {
					echo '
			subcats['.$i.'][1]['.$j.'] = "'.$GLOBALS['templates'][$i][2][$j].'";';
					}
				}
?>

			del = <?= count($GLOBALS['templates'][0][2]) ?>;
			function submenu() {
				design = 1;
				variante = 2;
				if (document.forms[0].elements[0].name == "tickets[]") {
					design = 0;
					variante = 1;
				}
				for (i=0; i<subcats.length; i++) {
					if (subcats[i][0] == document.forms[0].elements[design].value) {
						what = i;
					}
				}
				for (i=0; i<del; i++) {
					document.forms[0].elements[variante].options[del-i-1] = null;
				}
				for (i=0; i<subcats[what][1].length; i++) {
					document.forms[0].elements[variante].options[i] = new Option(subcats[what][1][i], i);
				}
				del = i;
			}
			function link_popup(url, w, h, s) {
				window.open(url, '_blank', 'location=0,statusbar=0,menubar=0,width=' + w + ',height=' + h + ',scrollbars=' + s);
			}
			//-->
		</script>
		
	</head>
	<body>
		<div id="main">
		<form method="post" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" target="_blank">
			<img src="i/banner.jpg" alt="main title" width="550" height="176" border="0" id="mainimage" />
			<div id="lang">
			<?
			for ($i=0; $i<count($GLOBALS['INSTALLED_LANG']); $i++) {
				echo '<a href="?lang='.$GLOBALS['INSTALLED_LANG'][$i].'">'.$GLOBALS['INSTALLED_LANG'][$i].'</a> ';
			}
			?>
			</div>
			<fieldset>
				<legend><?=$GLOBALS['lang']['design']?></legend>
				<div class="row">
					<span class="field-label">
						<label for="design"><?=$GLOBALS['lang']['design']?></label>
					</span>
					<span class="field-self">
						<select id="design" name="tickets[]" onchange="submenu()"><?
							for ($i=0; $i<count($GLOBALS['templates']); $i++) {
								echo '
							<option value="'.$GLOBALS['templates'][$i][0].'">'.$GLOBALS['templates'][$i][1].'</option>';
							}
		?>
						</select>
						<a href="help.php?id=0" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="variante"><?=$GLOBALS['lang']['variant']?></label>
					</span>
					<span class="field-self">
				<select id="variante" name="tickets[]"><?
					for ($i=0; $i<count($GLOBALS['templates'][0][2]); $i++) {
						echo '
					<option value="'.$i.'">'.$GLOBALS['templates'][0][2][$i].'</option>';
					}
?>
				</select>
						<a href="help.php?id=0" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
			</fieldset>
			<fieldset>
				<legend><?=$GLOBALS['lang']['hometheater']?></legend>
				<div class="row">
					<span class="field-label">
				<label for="kname"><?=$GLOBALS['lang']['name']?></label>
					</span>
					<span class="field-self">
				<input type="text" name="tickets[]" value="" id="kname" class="input-large" />
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="infos"><?=$GLOBALS['lang']['info']?></label>
					</span>
					<span class="field-self">
				<input type="text" name="tickets[]" value="" id="infos" class="input-large" />
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="sitze"><?=$GLOBALS['lang']['seats']?></label>
					</span>
					<span class="field-self">
				<input type="radio" name="holdudieladio" value="1" checked="checked" onclick="document.forms[0].seats_none.disabled=false; document.forms[0].sitze.disabled=true;" id="radio1"  /><input type="text" id="seats_none" name="tickets[]" class="input-small" /> <?=$GLOBALS['lang']['ticketsnoseats']?>
						<a href="help.php?id=1" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
				<div class="row">
					<span class="field-label">
						&nbsp;
					</span>
					<span class="field-self">
				<input type="radio" name="holdudieladio" value="2" onclick="document.forms[0].seats_none.disabled=true; document.forms[0].sitze.disabled=false;" id="radio2" /><input type="text" name="tickets[]" id="sitze" class="input-large" disabled="disabled"/>
						<a href="chairs.php" onclick="link_popup(this, 500, 500, 'no');return false" target="_blank"><?=$GLOBALS['lang']['choose']?></a>
						<a href="help.php?id=1" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
			</fieldset>
			<fieldset>
				<legend><?=$GLOBALS['lang']['film']?></legend>
				<div class="row">
					<span class="field-label">
				<label for="titel"><?=$GLOBALS['lang']['title']?></label>
					</span>
					<span class="field-self">
				<input type="text" name="tickets[]" class="input-large" id="titel" />
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="subtitel"><?=$GLOBALS['lang']['subtitle']?></label>
					</span>
					<span class="field-self">
				<input type="text" name="tickets[]" class="input-large" id="subtitel" />
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="datum"><?=$GLOBALS['lang']['date']?></label>
					</span>
					<span class="field-self">
				<select id="datum" name="tickets[]"><?
					for ($i=1; $i<32; $i++) {
						echo '
					<option value="'.$i.'"';
						if (date('j') == $i) {
							echo ' selected="selected"';
						}
						echo '>'.$i.'.</option>';
					}
?>
				</select>
				<select id="datum2" name="tickets[]"><?
					$months = $GLOBALS['lang']['months_html'];
					for ($i=0; $i<12; $i++) {
						echo '
					<option value="'.($i+1).'"';
						if (date('n') == $i+1) {
							echo ' selected="selected"';
						}
						echo '>'.$months[$i].'</option>';
					}
?>
				</select>
				<select id="datum3" name="tickets[]"><?
					$year = date('Y');
					for ($i=$year; $i<($year+6); $i++) {
						echo '
					<option value="'.$i.'">'.$i.'</option>';
					}
?>
				</select>
					</span>
				</div>
				<div class="row">
					<span class="field-label">
				<label for="time"><?=$GLOBALS['lang']['time']?></label>
					</span>
					<span class="field-self">
				<select id="time" name="tickets[]"><?
					if ($GLOBALS['lang']['time_f'] == '24') {
						for ($i=0; $i<24; $i++) {
							echo '
					<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'"';
							if (20 == $i) {
								echo ' selected="selected"';
							}
							echo '>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
						}
					} else {
						for ($i=0; $i<12; $i++) {
							echo '
					<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'am">'.str_pad($i, 2, '0', STR_PAD_LEFT).' am</option>';
						}
						echo '
					<option value="12pm">12 pm</option>';
						for ($i=1; $i<12; $i++) {
							echo '
					<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'pm">'.str_pad($i, 2, '0', STR_PAD_LEFT).' pm</option>';
						}
					}
?>
				</select>:<select id="time2" name="tickets[]"><?
					for ($i=0; $i<60; $i++) {
						echo '
					<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'">'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
					}
?>
				</select>
					</span>
				</div>
			</fieldset>
			<fieldset id="last">
				<legend><?=$GLOBALS['lang']['options']?></legend>
				<div class="row">
					<span class="field-label">
						<label for="regmarks"><?=$GLOBALS['lang']['crop']?></label>
					</span>
					<span class="field-self">
						<input type="checkbox" name="regmark" id="regmarks" checked="checked" />
						<a href="help.php?id=2" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
				<div class="row">
					<span class="field-label">
						<label for="bild"><?=$GLOBALS['lang']['image']?></label>
					</span>
					<span class="field-self">
						<input type="file" name="bild" id="bild" />
						<a href="help.php?id=3" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
				<div class="row">
					<span class="field-label">
						<label for="frei"><?=$GLOBALS['lang']['free']?></label>
					</span>
					<span class="field-self">
						<input type="text" name="tickets[]" id="frei" value="0" class="input-small" />
						<a href="help.php?id=4" onclick="link_popup(this, 500, 300, 'yes');return false" target="_blank" class="help"><img src="i/pix.gif" alt="Hilfe" width="13" height="13" border="0" /></a>
					</span>
				</div>
			</fieldset>
			<input type="submit" value="<?=$GLOBALS['lang']['create']?>" name="doTheMacDaddy"/>
			<br /><br />
			<a href="http://stadlberger.com" class="yadur" target="_blank">Stefan Stadlberger</a> | <a href="http://yadur.com" class="yadur" target="_blank">yadur studios</a> | <a href="http://validator.w3.org/check?uri=referer" class="yadur" target="_blank">XHTML</a> <a href="http://jigsaw.w3.org/css-validator/check/referer" class="yadur" target="_blank">CSS</a>
		</form>
		</div>
	</body>
</html><? } ?>