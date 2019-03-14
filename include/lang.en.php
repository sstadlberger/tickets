<?
$GLOBALS['lang']['time_f'] = '12';
$GLOBALS['lang']['pref'] = 'en';
$GLOBALS['lang']['t_c'] = 'Tickets for you Home Theater';


$GLOBALS['lang']['design'] = 'Design';
$GLOBALS['lang']['variant'] = 'Variant';

$GLOBALS['lang']['hometheater'] = 'Home Theater';
$GLOBALS['lang']['name'] = 'Name';
$GLOBALS['lang']['info'] = 'Info';
$GLOBALS['lang']['seats'] = 'Seats';
$GLOBALS['lang']['ticketsnoseats'] = 'tickets / no reserved seating';
$GLOBALS['lang']['choose'] = 'choose';

$GLOBALS['lang']['film'] = 'Film';
$GLOBALS['lang']['title'] = 'Title';
$GLOBALS['lang']['subtitle'] = 'Subtitle';
$GLOBALS['lang']['date'] = 'Date';
$GLOBALS['lang']['months_pdf'] = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$GLOBALS['lang']['months_html'] = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$GLOBALS['lang']['time'] = 'Time';

$GLOBALS['lang']['options'] = 'Options';
$GLOBALS['lang']['crop'] = 'Crop Marks';
$GLOBALS['lang']['image'] = 'Picture';
$GLOBALS['lang']['free'] = 'Skip';

$GLOBALS['lang']['create'] = 'Create PDF';


$GLOBALS['lang']['errors'] = array(
					'x001' => 'A problem occured while using the template.',
					'x002' => 'The date is invalid.',
					'x003' => 'Please enter a valid number of tickets or choose a number of seats.',
					'x004' => 'There is a limit of 100 tickets. That should be enough for the average home theater.',
					'x005' => 'The time doesn\'t exist.',
					'x006' => 'The ticket has already been deleted or the name of the ticket is wrong.<br />Make sure that you click the link and don\'t enter the URI yourself.<br />Whatever the reason is, the specified ticket doens\'t exist on the server.',
					'x007' => 'The name of the ticket is in an invalid format.<br />Make sure that you click the link and don\'t enter the URI yourself.'
					);

$GLOBALS['lang']['error_o'] = 'An error occured';
$GLOBALS['lang']['back'] = 'back';


$GLOBALS['lang']['tickets_done'] = 'The tickets are completed.';
$GLOBALS['lang']['t1'] = 'The';
$GLOBALS['lang']['tl'] = 'PDF with the tickets';
$GLOBALS['lang']['t2'] = 'was created successfully.<br />Right-click on the link and save the PDF.<br /><br />Have fun.';


$GLOBALS['lang']['uhr'] = 'time';
$GLOBALS['lang']['row'] = 'Row';
$GLOBALS['lang']['seat'] = 'Seat';
$GLOBALS['lang']['freeseat'] = 'no reserved seating';
$GLOBALS['lang']['seating'] = 'Seating';
$GLOBALS['lang']['done'] = 'done';
$GLOBALS['lang']['delete_ticket'] = 'delete ticket from server';
$GLOBALS['lang']['deleted'] = 'The ticket has been deleted.';


$GLOBALS['lang']['help'] = array(	
					'<h1>Design &amp; Variant</h1><p>You choose the design and the paper format here. Design and paper format are linked together, because the design is optimized for the paper size. Some designs are made for business cards.</p><p>The variant defines the color scheme.</p>', 
					'<h1>Seating</h1><p>You can choose here between a fixed number of tickets with out reseved seating or tickets with individual row- and seat numbers.</p><p><h2>No Reserved Seating</h2><br />Choose the upper radio button and enter the desired amount of tickets.</h2></p><p><h2>Individual Row- and Seat Numbers</h2><br />Choose the lower radio button.<br />Click on choose and select the seats you want tickets for. When you click done, the seat numbers will entered into the field.<br /><br /><i>The following instructions are for advanced users</i><br />If you want to manually change the seat numbers, the format is <i>Row , Start Seat - End Seat</i>. The rows are seperated with an semicolon (;).</p><p><img src="i/h_row1.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />For a home theater with three seats in the first row and four seats in the second row enter<br /><i>1,1-3; 2,1-4</i>.</p></p><p><img src="i/h_row2.gif" alt="Demo 2" width="200" height="121" border="1" class="demo" />Combinations are also possible. If you need tickets for the seats one and three through six in the first row, the seats five through two in the third row and the seats one through three and five in the second row then enter: <i>1,1; 1,3-6; 3,5-2; 2,1-3; 2,5</i>. It doesn\'t matter in which order the seats and rows are entered.</p>', 
					'<h1>Crop Marks</h1><p>Select this option if you want to print crop marks arround each ticket. If you plan to cut out the tickets by hand you should select this option. Don\'t use this option if you print the tickets on perforated or precut paper (i.e. paper the is made for business cards).</p><p><img src="i/h_marks_0.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />without crop marks</p><p><img src="i/h_marks_1.gif" alt="Demo 1" width="200" height="121" border="1" class="demo" />with crop marks</p>', 
					'<h1>Custom Picture</h1><p>If you upload an image here, it will replace the image on the ticket. An image size of 640 by 480 is sufficient for the most designs. Because the picture is resampled, higher resolutions have <b>no</b> influence on the quality of the final image and cause only a longer waiting time or even a timeout.</p>', 
					'<h1>Skip</h1><p>If you have already printed and cut out some tickets and want to reuse the paper, you can enter an offset value here.</p><p><img src="i/h_free_0.gif" alt="Demo 1" width="121" height="200" border="1" class="demo" />four tickets with zero skip</p><p><img src="i/h_free_3.gif" alt="Demo 1" width="121" height="200" border="1" class="demo" />four tickets with a skip of three</p>',
					'Es ist keine Hilfe verf&uuml;gbar.');
$GLOBALS['lang']['help_t'] = 'Help';
?>