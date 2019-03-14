<?
$GLOBALS['lang']['time_f'] = '24';
$GLOBALS['lang']['pref'] = 'de';
$GLOBALS['lang']['t_c'] = 'Tickets f&uuml;r Dein Kino';


$GLOBALS['lang']['design'] = 'Design';
$GLOBALS['lang']['variant'] = 'Variante';

$GLOBALS['lang']['hometheater'] = 'Heimkino';
$GLOBALS['lang']['name'] = 'Name';
$GLOBALS['lang']['info'] = 'Zusatztext';
$GLOBALS['lang']['seats'] = 'Sitze';
$GLOBALS['lang']['ticketsnoseats'] = 'Karten / keine Platznummern';
$GLOBALS['lang']['choose'] = 'w&auml;hlen';

$GLOBALS['lang']['film'] = 'Film';
$GLOBALS['lang']['title'] = 'Titel';
$GLOBALS['lang']['subtitle'] = 'Untertitel';
$GLOBALS['lang']['date'] = 'Datum';
$GLOBALS['lang']['months_pdf'] = array('Januar', 'Februar', 'M'.chr(228).'rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
$GLOBALS['lang']['months_html'] = array('Januar', 'Februar', 'M&auml;rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
$GLOBALS['lang']['time'] = 'Uhrzeit';

$GLOBALS['lang']['options'] = 'Optionen';
$GLOBALS['lang']['crop'] = 'Schnittmarken';
$GLOBALS['lang']['image'] = 'Bild';
$GLOBALS['lang']['free'] = 'freilassen';

$GLOBALS['lang']['create'] = 'PDF Erstellen';


$GLOBALS['lang']['errors'] = array(
					'x001' => 'Es ist ein Problem mit dem Template aufgetreten.',
					'x002' => 'Das Datum ist ung&uuml;ltig.',
					'x003' => 'Bitte eine g&uuml;ltige Kartenanzahl / Sitznummerierung eingeben.',
					'x004' => '&Uuml;ber hundert Pl&auml;tze sind aber ein bischen viel f&uuml;r ein Heimkino.',
					'x005' => 'Die Uhrzeit gibt es ja gar nicht.',
					'x006' => 'Das Ticket wurde bereits gel&ouml;scht.',
					'x007' => 'Der Ticket Name ist falsch.'
					);

$GLOBALS['lang']['error_o'] = 'Ein Fehler ist aufgetreten';
$GLOBALS['lang']['back'] = 'zur&uuml;ck';


$GLOBALS['lang']['tickets_done'] = 'Die Tickets sind fertig';
$GLOBALS['lang']['t1'] = 'Das';
$GLOBALS['lang']['tl'] = 'PDF mit den Tickets';
$GLOBALS['lang']['t2'] = 'wurde erfolgreich erstellt.<br />Mach einen Rechtsklick auf den Link und sichere das PDF.<br /><br />Viel Spa&szlig;.';


$GLOBALS['lang']['uhr'] = 'Uhr';
$GLOBALS['lang']['row'] = 'Reihe';
$GLOBALS['lang']['seat'] = 'Sitz';
$GLOBALS['lang']['freeseat'] = 'freie Platzwahl';
$GLOBALS['lang']['seating'] = 'Sitzpl&auml;tze';
$GLOBALS['lang']['done'] = 'Fertig';
$GLOBALS['lang']['delete_ticket'] = 'Ticket vom Server l&ouml;schen';
$GLOBALS['lang']['deleted'] = 'Das Ticket wurde gel&ouml;scht.';


$GLOBALS['lang']['help'] = array(	
					'<h1>Design &amp; Variante</h1><p>Hier w&auml;hlst Du das Design und das Format des Papieres aus. Design und Papierformat sind fest miteinander verkn&uuml;pft weil bei unterschiedlichen Formaten ein anderes Design ben&ouml;tigt wird. Die meisten Formate sind auf die diversen Spezialpapiere f&uuml;r Visitenkarten ausgelegt.</p><p>Die Variante bestimmt das genau Farb- und Bildschema.</p>', 
					'<h1>Kartennummerierung</h1><p>Hier kannst hast Du die Wahl zwischen einer festen Anzahl Karten ohne Platznummern und Karten die individuelle Reihen- und Sitznummern haben. Die Anzahl der Karten wird hierbei automatisch errechnet.</p><p><h2>Keine Platznummern</h2><br />W&auml;hle den oberen Radiobutton aus und gib die Anzahl der Karten ein.</p><p><h2>Individuelle Reihen- und Sitznummern</h2><br />W&auml;hle den unteren Radiobutton aus und gib die Kartennummerierung in der Form <i>Reihe , Platz von - Platz bis</i> ein, die einzelnen Reihen jeweils durch einen Strichpunkt (;) getrennt.</p><p><img src="i/h_row1.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />F&uuml;r ein Kino, das in der ersten Reihe drei Pl&auml;tze und in der zweiten Reihe vier Pl&auml;tze hat w&auml;re das <i>1,1-3; 2,1-4</i>.</p></p><p><img src="i/h_row2.gif" alt="Demo 2" width="200" height="121" border="1" class="demo" />Es sind auch diverse Kombinationen m&ouml;glich. Wenn Karten f&uuml;r die Pl&auml;tze eins und drei bis sechs der ersten Reihe, die Pl&auml;tze f&uuml;nf bis zwei der dritten Reihe und die Pl&auml;tze eins bis drei und f&uuml;nf der zweiten Reihe ben&ouml;tigt werden, dann braucht es dazu folgende Eingabe: <i>1,1; 1,3-6; 3,5-2; 2,1-3; 2,5</i>. Die Reihenfolge der Reihen und Pl&auml;tze ist egal.</p>', 
					'<h1>Schnittmarken</h1><p>Wenn die Option Schnittmarken gew&auml;hlt ist, wird um jede Karte ein Satz Schnittmarken gedruckt. Wenn die Karten per Hand ausgeschnitten werden, sollte diese Option auf alle F&auml;lle angew&auml;hlt werden. Die Option sollte nicht bei gew&auml;hlt werden, wenn die Karten auf bereits perforiertes oder vorgeschnittenes Papier gedruckt werden.</p><p><img src="i/h_marks_0.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />ohne Schnittmarken</p><p><img src="i/h_marks_1.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />mit Schnittmarken</p>', 
					'<h1>Eigenes Bild</h1><p>Wenn hier ein Bild hochgeladen wird, wird die das Standardbild des Designs durch dieses Bild ersetzt. Ein Bild der Gr&ouml;&szlig;e 640 x 480 ist f&uuml;r die meisten Vorlagen mehr als ausreichend. Da die hochgeladenen Bilder auf das passende Format zugeschnitten werden, verbessern extrem hochaufl&ouml;sende Bilder die Qualit&auml;t <b>nicht</b>, sondern f&uuml;hren nur zu sehr langen Wartezeiten.</p>', 
					'<h1>Karten freilassen</h1><p>Falls schon einige Karten aus dem Bogen fehlen, kann hier eingestellt werden, wie viele Karten freigelassen werden sollen.</p><p><img src="i/h_free_0.gif" alt="Demo 1" width="121" height="200" border="1" class="demo" />vier Karten wurden erstellt</p><p><img src="i/h_free_3.gif" alt="Demo 1" width="121" height="200" border="1" class="demo" />vier Karten wurden erstellt und drei Karten wurden freigelassen</p>',
					'Es ist keine Hilfe verf&uuml;gbar.');
$GLOBALS['lang']['help_t'] = 'Hilfe';
?>