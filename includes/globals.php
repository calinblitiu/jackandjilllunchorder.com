<?php
	
///
// Global vars and data
// Created: Nov. 2017
//
//
// db connection and path



if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '192.168.1.113' || $_SERVER['REMOTE_ADDR'] == '192.168.1.80' || $_SERVER['REMOTE_ADDR'] == '192.168.1.77')
{
		// this is for all other local sites
	 
  	 $g_connServer = "localhost";
	 $g_connUserid = "root";
	 $g_connPwd = "master";
	 $g_connDBName = "jackjill";
	 $g_docRoot = "/var/websites/jackjill/";
  	 $g_serverName = "jackjill.dev";
	 define("DEFAULT_TIME_ZONE", "Asia/Calcutta");
	 $g_webRoot = "/";	
	 $g_zendPath = "";
      
}
else  if ($_SERVER["SERVER_ADDR"] == "162.253.124.64") { // mediawarrior.com
	 $g_connServer = "localhost";
	 $g_connUserid = "med176_juser";
	 $g_connPwd = 'z!!9cMHoGUD8';
	 $g_connDBName ="med176_jackjill";
	 $g_docRoot = "/home/med176/public_html/jack/";
	 $g_serverName = "6";
	 
	 $g_webRoot = "/jack/";
	 $g_zendPath = "" ;
	 $g_apiReferer = "http://mediawarrior.com";
	 define("DEFAULT_TIME_ZONE", "Australia/Melbourne");

} 

else   // jackandjill.com.au
{
    
	 $g_connServer = "localhost";
	 $g_connUserid = "c04uqiu0_juser";
	 $g_connPwd = '=L$1^]B;T1Tz';
	 $g_connDBName ="c04uqiu0_jj";
	 $g_docRoot = "";
	 $g_serverName = "http://jackandjilllunchorders.com/";
	 
	 $g_webRoot = "/";
	 $g_zendPath = "" ;
	 $g_apiReferer = "http://jackandjill.com.au";
 	 define("DEFAULT_TIME_ZONE", "Australia/Melbourne");


}

$gDocRoot = $g_docRoot; // in case someone mistypes it

$g_pwdSalt = "MNJAAJJ$nn";				// salt used to encrypt passwords
$g_resizedImagePath = "";				// path of image after executing resizeImage()

					// date display formats
define("DATE_FULL", 1);		// Sunday 15 july, 2008 14:20
define("DATE_NOTIME", 2);	// Sunday 15 July, 2008
define("DATE_NOWEEKDAY", 3); // 14 July , 2008
define("DATE_STANDARD", 4); // 2009-11-21
define("DATE_EXCEL", 5); // 14-Mar-2010

define("ORDER_STATUS_RECEIVED", "Received");
define("ORDER_STATUS_INPROGRESS", "In Progress");
define("ORDER_STATUS_CANCELLED", "Cancelled");
define("ORDER_STATUS_DELIVERED", "Delivered");

define("MEAL_DEAL_ITEM_DISPLAY_ID", "99999");	// this id is for displaying mealdeal in product list

/****eway credentials start****/
define("EWAY_API_KEY", "A1001Aq489/thVlzGyfcrO4TL8dR4kPYdbFe4mYRdXZ3j8dDsQLIi2wGt+FjBSfB1vQV8T");
define("EWAY_API_PWD", "kfiyrSvz");
define("EWAY_CLIENT_API_KEY", "wol6Lrp6QnI9ULOiQcPTpyevtko0Qj5ioJx+J9+9+rAq7nXtxGSGvJmp/QFdAB4pN7Zi0W/1cd9aAmIJXrOxoxs8hMmTYVELXuK3z6WoSN2+7T0KzGt6Y3yYQNvqxdlHr7A0PbqNDUEQGUFWUdlDC+OeVf3Nwv20HR4QVkJiQ76XfJ4WlekyNnzZ0m/nSPQaqLmjBdUS07yo0eaAxOBi+BBZpVK8hmVN3ut1esWe5kkuQJtUmFtqN/JSfG+KiJT7qfb4joIoEFFVdXv0JBQUvE8Gkx7MMZbzvcPF+PkfZfnGlbPgdKcSeTxU7rrrqMldkC2h5HQKDwuv6hh2n0N53w==");

define("EWAY_SANDBOX_API_KEY", "A1001CAohAQiUYcXKEjKB2F5v6sl+LrUxoW3JUZqjUqmoKYGKp8W6YJ27J1YVjAF48u+GI");
define("EWAY_SANDBOX_API_PWD", "2Vss5ORd");


/**eway credentials end*****/


/**********smtp server details*************/
$g_smtpServer = "mail.jackandjill.com.au"; //"b1s4-1b-mel.hosting-services.net.au"; 
$g_smtpPort = 587;
$g_smtpUserId = "orders@jackandjill.com.au";
$g_smtpPwd = "ZXnP3-4)v@!h";

$g_fromEmailId = "orders@jackandjill.com.au";
$g_fromName = "Jack & Jill Catering";

/********smtp server details end********/


/**
 * Write array to disk
 * @param string $filename full path to file
 * @param array $result  array
 */
function writeArrayToDisk($filename, $result) {

		$f = fopen($filename ,"w");
		if ($f) {
			fwrite($f, serialize($result));	
			fclose($f);
		} else
			exit("Error writing to file");
}


/**
 * Read array from disk
 * @param string $filename full path to file
 * @returns array $result unserialized array
 */
function readArrayFromDisk($filename) {
		$result = null;

		$f = fopen($filename ,"r");
		if ($f) {
			$raw = fread($f, filesize($filename));	
			$result = unserialize($raw);
			fclose($f);
		} else
			exit("Error reading from file");	

		return $result;
}



/**
 * Convert date into a user friendly display format
 * Parameters: dt->raw dt
 *			   nFormat->format type
 * Returns   : strDt->formatted date
 ******/

function getNiceDate($dt, $nFormat) {
	$strDt = strtotime(str_replace('/','-',$dt));
	$arrdate = getdate($strDt);
	$nYear = $arrdate[year];
	$nMonth = $arrdate[month];
	$nDay = $arrdate[mday];
	$nHour = $arrdate[hours];
	if ($nHour < 10)
		$nHour = "0" . $nHour;
	$nMin = $arrdate[minutes];
	if ($nMin < 10)
		$nMin = "0" . $nMin;
	$strWeekDay = $arrdate[weekday];
	if ($nFormat != DATE_NOWEEKDAY)
		$strDt = substr($strWeekDay,0,3);
	else
		$strDt = "";
	if ($nFormat == DATE_STANDARD) {
		$strDt = $nYear . "-" ;
		if ((int) $arrdate[mon] < 10)
			$strDt .= "0";
		$strDt .= $arrdate[mon] . "-" ;
		if ((int) $nDay < 10)
			$strDt .= "0";
		$strDt .= $nDay;
	}			
	else if ($nFormat == DATE_EXCEL) {
		$strDt = " " . $nDay . " " . substr($nMonth,0,3) . " " . $nYear ;
	}
	else
		$strDt .= " " . $nDay . " " . substr($nMonth,0,3) . "," . $nYear ;
	if ($nFormat == DATE_FULL) {
		$strDt .= " " . $nHour . ":" . $nMin;
	}		
	return $strDt;
}

				
/**
 * Php security function
 Parameters: Posted data
 ******/
function getSecureData($data) {
	$data = mysql_real_escape_string($data);
	$data = stripcslashes($data);
	return $data;
	
}


/***
 * Convert a pwd to a hash
 * Parameters: pwd->password text (max.15 chars)
 * Returns   : hpwd->password hash
 **********/
function getPwdHash($pwd) {
	$hpwd = "";
	global $g_pwdSalt;
	if (strlen($pwd) > 15)
		$pwd = substr($pwd,0,15);
	$hpwd = md5($g_pwdSalt . $pwd);
	return $hpwd;
}

/***
 * Validate a url
 * Parameters: url->url path
 * Returns: true/false
 ******/
function isValidURL($url)
{
	 return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}  


/***
 * Convert seconds to time (years,months,days,hours,mins,seconds)
 * Parameters: time->seconds
 * Returns   : value->array of time components
 *********/
function secToTime($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    return (array) $value;
  }else{
    return (bool) FALSE;
  }
}





/**
 * function to calculate the difference in date fom now
 ********/
function nicetime($date)
{
    if(empty($date)) {
        return "No date provided";
    }
  
    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");
  
    $now             = time();
    $unix_date         = strtotime($date);
  
       // check validity of date
    if(empty($unix_date)) {  
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {  
        $difference     = $now - $unix_date;
        $tense         = "ago";
      
    } else {
        $difference     = $unix_date - $now;
        $tense         = "from now";
    }
  
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
  
    $difference = round($difference);
  
    if($difference != 1) {
        $periods[$j].= "s";
    }
 	
				// special check if duration < 1 second
	if ($difference == 0 && $tense == "from now")
		return " just now";
 
    return "$difference $periods[$j] {$tense}";
}

/**
 * Change a line of text into url friendly format
 * Removes reserved characters and unsafe characters as defined in RFC 3986
 * @param string $text original text
 * @return string $retVal modified text
 */
function makeTextURLSafe($text) {
	$retVal = $text;
    			// remove reserved characters
	$retVal = str_replace("&", "-", $retVal);
	$retVal = str_replace("$", "-", $retVal);
	$retVal = str_replace( "+", "-", $retVal);
	$retVal = str_replace( ",", "-", $retVal);	
	$retVal = str_replace( "/", "-", $retVal);	
	$retVal = str_replace( ":", "-", $retVal);	
	$retVal = str_replace( ";", "-", $retVal);	
	$retVal = str_replace( "=", "-", $retVal);	
	$retVal = str_replace( "?", "-", $retVal);	
	$retVal = str_replace( "@", "-", $retVal);	
				
				// remove unsafe characters
	$retVal = str_replace( "<", "-", $retVal);	
	$retVal = str_replace( ">", "-", $retVal);	
	$retVal = str_replace( "[", "-", $retVal);	
	$retVal = str_replace( "]", "-", $retVal);	
	$retVal = str_replace( "{", "-", $retVal);	
	$retVal = str_replace( "}", "-", $retVal);	
	$retVal = str_replace( "|", "-", $retVal);	
	$retVal = str_replace( "\\", "-", $retVal);	
	$retVal = str_replace( "^", "-", $retVal);	
	$retVal = str_replace( "~", "-", $retVal);	
	$retVal = str_replace( "%", "-", $retVal);	
	$retVal = str_replace( "#", "-", $retVal);	
	$retVal = str_replace(" ", "-", $retVal);
	$retVal = str_replace(".", "-", $retVal);
	$retVal = str_replace( "!", "-", $retVal);	
	$retVal = str_replace( "(", "-", $retVal);	
	$retVal = str_replace( ")", "-", $retVal);	



	return $retVal;

}

/**
 * Truncate text at a word, sentence or punctuation boundary
 * @param string $text original text
 * @param int $maxlen max.length of text to show
 * @return string $retVal truncated text
 */
function truncateText($text, $maxlen) {
	$retVal = "";
	$delimiters = array(".", " ", ",", "!", "\n", "?");
	
	if (strlen($text) <= $maxlen)
		$retVal = $text;
	else {
		$i = $maxlen;
		while(!in_array(substr($text,$i, 1), $delimiters))
		{
			$i = $i+1;
		}
		$retVal = substr($text,0,$i);
		
	}				
	
	return $retVal;
}

 


/**
 * Change url to friendly format 
 * Parameters: url->url in default format
 * Returns   : retVal->url in friendly format
 *********/
function friendlyURL($url) {
	$retVal = $url;
	global $g_webRoot;

		$data = str_replace("..", "", $retVal);

					// /merchant/selected-merchant.php?sc=<shortcode>
		if (preg_match_all(MERCHANT_PATTERN, $data, $regs)) {
			$shortCode = substr($regs[0][0], strpos($regs[0][0], "sc=")+3);
				$retVal = $g_webRoot . "/merchant/" . $shortCode;
		}
	

		

	return $retVal;
}

function date_difference($start, $end="NOW")
{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if($time>=0 && $time<=59) {
                // Seconds
                $timeshift = "Only ".$time.' seconds left';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);
               
                $presec = $pmin-$premin[0];
                $sec = $presec*60;
               
                $timeshift = "Only ".$premin[0].' min left';
				$timeshift = "0 day left";
				//.round($sec,0).' sec ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);
               
                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);
               
                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = "Only ".$prehour[0].' hrs left';
				$timeshift = "0 day left";
				//.$min[0].' min '.round($sec,0).' sec ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24);

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);
               
                $presec = '0.'.$min[1];
                $sec = $presec*60;
							
                $timeshift = "Only ". $preday[0].' days left';
				if($preday[0] == 1)
					$timeshift = "1 day left";
				if($preday[0] > 31)
					$timeshift = "Over 1 month to expiry";
				if($preday[0] > 365)
					$timeshift = "Over 1 year to expiry";

				//.$prehour[0].' hrs '.$min[0].' min ';

        }
        return $timeshift;
}

function getDomain($url)
{
	$nowww = ereg_replace('www\.','',$url);
	$domain = parse_url($nowww);
	if(!empty($domain["host"]))
    {
    	 return $domain["host"];
    } else
     {
	     return $domain["path"];
     }
 
}



function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}


function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function convertNumberToShort($number) {

	if ($number >= 999999999) { // 1 b 
		$number = round($number/1000000000,2) . "B";

	} else if ($number >= 999999) { // 1 m 
		$number = round($number / 1000000,2) . "M";

	} else if ($number >= 999) { // 1k
		$number = round($number / 1000, 2) . "K";
	}
	else if ($number >= 0) {}
	else
		$number = "N.A";
	return $number;

}


function secondsToHMS($seconds) {
  $t = round($seconds);
  return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

/**
 * Generate a random string given a set of allowed characters
 * @param string $valid_chars string of valid characters. if null then 0-9 is assumed
 * @param int $length max length of string
 * @return string $random_string generated string
 */
function get_random_string($valid_chars, $length)
{

    if ($valid_chars == null || $valid_chars == '')
	$valid_chars = "012345679";
    // start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length

    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}


function mealTypeToString($mt) {
	if ($mt == "R")
	  return "Recess";
	else if ($mt == "L")
	  return "Lunch";
	else if ($mt == "RL")
	  return "Recess + Lunch";
}	  



 function insertInArray($array, $index, $val) { //function decleration
    $temp = array(); // this temp array will hold the value 
    $size = count($array); //because I am going to use this more than one time
    // Validation -- validate if index value is proper (you can omit this part)       
        if (!is_int($index) || $index < 0 || $index > $size) {
            echo "Error: Wrong index at Insert. Index: " . $index . " Current Size: " . $size;
            echo "<br/>";
            return false;
        }    
    //here is the actual insertion code
    //slice part of the array from 0 to insertion index
    $temp = array_slice($array, 0, $index);//e.g index=5, then slice will result elements [0-4]
    //add the value at the end of the temp array// at the insertion index e.g 5
    array_push($temp, $val);
    //reconnect the remaining part of the array to the current temp
    $temp = array_merge($temp, array_slice($array, $index, $size)); 
    $array = $temp;//swap// no need for this if you pass the array cuz you can simply return $temp, but, if u r using a class array for example, this is useful. 

     return $array; // you can return $temp instead if you don't use class array
}
?>
