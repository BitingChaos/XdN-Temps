<?php

/*

Tested on Watchdog 15/15-P, running firmware 3.3.3 (Geist) and 3.4.0 (Vertiv).

Nicholas Caito

ncaito@gmail.com

2019-06-26

*/



// ----- adjustables -----

// comma-separated list of IPs/hosts to query
$hosts = array(
        "192.168.1.20",
        "192.168.1.30",
        "10.0.0.54"
        );

// set the time zone here if it isn't set in php.ini
// date_default_timezone_set('America/Chicago');

// return only the actual SNMP values ("1" instead of "INTEGER: 1")
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

// get date and format it
$tempdate = date('F jS, Y @ g:i A');

// how long to wait (in miliseconds) for device response
$timeout = "50000";

// times to retry connection to device
$retry = "2";

// community string
$community = "public";



// ----- watchdog 15 snmp strings ----

// OID for location
$oidLoc = "iso.3.6.1.2.1.1.6.0";

// OID for temp
$oidTemp = "iso.3.6.1.4.1.21239.5.1.2.1.5.1";

// OID for humidity
$oidHum = "iso.3.6.1.4.1.21239.5.1.2.1.6.1";

// OID for locale unit (0=F, 1=C)
$oidUnit = "iso.3.6.1.4.1.21239.5.1.1.7.0";



// ----- function to get location -----

function get_loc($host) {

 global $oidLoc;
 global $timeout;
 global $retry;
 global $community;

 if (($snmpLoc = @snmpget($host, $community, $oidLoc, $timeout, $retry))!=null) {

  // return value
  return $snmpLoc;

 } else {

  // return nothing
  return "Invalid Host";

 }

}



// ----- function to get locale unit -----

function get_unit($host) {

 global $oidUnit;
 global $timeout;
 global $retry;
 global $community;

 if (($snmpUnit = @snmpget($host, $community, $oidUnit, $timeout, $retry))!=null) {

  switch ($snmpUnit) {

   case "0":
    return "F"; 
    break;

   case "1":
    return "C";
    break;

   default:
    break;

  }
 
 } else {

  // return nothing
  return "X";

 }

}



// ----- function to get temperature -----

function get_temp($host){

 global $oidTemp;
 global $timeout;
 global $retry;
 global $community;

 if (($snmpTemp = @snmpget($host, $community, $oidTemp, $timeout, $retry))!=null) {

  // convert to float and divide by ten
  $temp = ((float) $snmpTemp) / 10;

  // return value 
  return $temp;

 } else {

  // unable to read
  return "X";

 }

}



// ----- function to get humidity ----

function get_hum($host) {

 global $oidHum;
 global $timeout;
 global $retry;
 global $community;

 if (($snmpHum = @snmpget($host, $community, $oidHum, $timeout, $retry))!=null) {

  // return value
  return $snmpHum;

 } else {

  // return nothing
  return "X";
 
 }

}



// ----- get temps / loop function ----

function get_temps($hosts) {

 // loop for each host
 foreach ($hosts as $ip) {

  $temploc = get_loc($ip);
  $tempunit = get_unit($ip);
  $tempcur = get_temp($ip);
  $temphum = get_hum($ip);

  echo "<div>\n";
  echo "Location: $temploc\n";
  echo "<br />Temperature: <strong>" . $tempcur . "º" . $tempunit . "</strong>\n";
  echo "<br />Humidity: $temphum%\n";
  echo "</div>\n\n";

 }

}


// ---- formatted html begin ----

function html_begin() {

 global $tempdate;

 echo '<html>
<head>
<style>
 div {
  font-family: Verdana, Arial, Helvetica, Ubuntu, Sans-Serif;
 }
 div > div {
  padding: 8px;
 }
 div > div:nth-of-type(odd) {
  background: #f1f1f1;
  background: linear-gradient(to right, #f1f1f1, #ffffff);
 }
</style>
</head>
<body>';

echo "\n<div class=\"grayback\">\n\n";

echo "<strong>$tempdate</strong><br /><br />\n\n";

}



// ----- formatted html end -----

function html_end() {

 echo "</div>\n\n";

 echo "</body></head>\n";

}



// ----- do stuff -----

html_begin();

get_temps($hosts);

html_end();





// EoF

