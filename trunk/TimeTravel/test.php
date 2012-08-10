<?php 

date_default_timezone_set('Africa/Johannesburg');
$hostname = '{imap.gmail.com:993/imap/ssl}SMS';

$inbox = imap_open($hostname,'sabside@gmail.com','Musn12nat');

$today = date('d-M-Y', time());
$startDate = date("d-M-Y", strtotime('2012-08-05'));
while ($startDate != $today){
	echo "current date: ".$startDate;
	$emails = imap_search($inbox,'ON '.$startDate);

	/* if emails are returned, cycle through each... */
	if($emails) {

		/* begin output var */
		$output = '';

		/* put the newest emails on top */
		rsort($emails);

		/* for every email... */
		$count = 0;
		foreach($emails as $email_number) {
			set_time_limit(20);
			/* get information specific to this email */
			$overview = imap_fetch_overview($inbox,$email_number,0);
			$message = imap_fetchbody($inbox,$email_number,1);


			$messageDate = $overview[0]->date;
			$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
			$myDateTime = new DateTime($messageDate, $gmtTimezone);

			$theDate = date('c', $myDateTime->format('U') + 1);
			$theDate = date('Y-m-d H:i:s', strtotime($theDate));

			/* output the email header information */
			/* $output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
			 $output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
			$output.= '<span class="from">'.$overview[0]->from.'</span>';
			$output.= '<span class="date">on '.$overview[0]->date.'</span>';
			$output.= '</div>'; */

			/* output the email body */
			$output.= '<div class="body">'. $theDate.' --- '.$message.'</div>';
			$count++;
			//if ($count > 5) break;
		}

		echo $output;
	
	}
	$tempDate = mktime(0,0,0,date("m", strtotime($startDate)),date("d", strtotime($startDate))+1,date("Y", strtotime($startDate)));
	$startDate = date("d-M-Y", $tempDate);
	
}
/* close the connection */
imap_close($inbox);

?>