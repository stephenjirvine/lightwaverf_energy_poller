<?php
//LightwaveRF Energy Poller
//Version 0.1
//Author: Steve Irvine

//This section is required for Cacti's script server
//see http://www.cacti.net/downloads/docs/html/migration_php_scripts_to_script_server.html
$no_http_headers = true;
/* display No errors */
error_reporting(E_ERROR);
include_once(dirname(__FILE__) . "/../include/global.php");
include_once(dirname(__FILE__) . "/../lib/snmp.php");
if (!isset($called_by_script_server)) {
    array_shift($_SERVER["argv"]);
	print call_user_func_array("ss_lwrfenergypoller", $_SERVER["argv"]);
}

//My Script starts here
function ss_lwrfenergypoller ($myip, $wifilinkip, $sendport, $recvport, $broadcast_string) {
//$myip is the IP address of the computer issuing the command
//$wifilinkip is the IP address of the wifilink
//$sendport = 9760, the UDP port LightwaveRF sends on
//$recvport = 9761, the UPD port LightwaveRF listens on
//$broadcast_string = '001,@?' to make the wifilink respond with energy usage data
//I have left these as arguments to allow the re-use of the script for future LWRF fuctionality

//TODO - Increment broadcast string to resolve potential race condition
//Perhaps we could use the minute of the hour e.g. 10 minutes past =010
$numbered_broadcast_string = date('Ni') . $broadcast_string

//Send the broadcast string, as a UDP datagram to the bvroadcast address on your network
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
socket_sendto($sock, $numbered_broadcast_string, strlen($numbered_broadcast_string), 0, '255.255.255.255', $sendport);
socket_close($sock);

//Listen for the response
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, $myip, $recvport);
socket_recvfrom($socket, $buf, 1024 , 0, $wifilinkip , $sendport);

//Ignore the first 5 characters of the response
$watts = substr($buf, 5);

//The explode function breaks the rest of the string up into multiple variables
//using "," as the delimiter
list($current_watts, $max_watts, $total_today, $total_yesterday) = explode(",", $watts);

//I only really care about the current reading, since cacti can work out the rest
//I can easily return the other values if necessary by returning them here
return $current_watts;
}
?>
