<?php
/*
################################################################################
# Copyright (c) 2013 Jorge I. Zuluaga
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# The software is under the specific terms of the Creative Common
# Share-alike 3.0 license
# http://creativecommons.org/licenses/by-sa/3.0/
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
################################################################################
# COMMON RESOURCES FOR PHP INTERFACE
################################################################################
*/
//////////////////////////////////////////////////////////////////////////////////
//CONFIGURATION VARIABLES
//////////////////////////////////////////////////////////////////////////////////
include_once("config.php");

//////////////////////////////////////////////////////////////////////////////////
//GLOBAL VARIABLES
//////////////////////////////////////////////////////////////////////////////////
date_default_timezone_set("EST");
$DATE=getdate();
$TIME=getToday("%mday%mon%year%hours%minutes%seconds");
$PYTHONCMD="MPLCONFIGDIR=../../tmp python";

$TEST=0;
$VERBOSE=0;

$PAGENAME=$_SERVER["SCRIPT_NAME"];

$RUNSDIR="runs";
$DEFCHART="imgs/default-chart";

$FIELDS=array(
	      //IDENTICAL VARIABLES
	      "FIGDPI"=>"600.0",
	      "TITLE"=>"Kepler Super Earths",
	      "SUBTITLE"=>"Comprehensive Exoplanetary Radial Chart",
	      "FIGSCALE"=>"1.8",
	      "INTERARC"=>"5",
	      "QUERY"=>"'Kepler' in planet.ID and planet.Rp>0.0 and planet.Rp<3.0/11.2",
	      "TWIDTH"=>"45",
	      "RADIALSLICE"=>"20",
	      "OFFNAME"=>"-20",
	      "RADIALWIDTH"=>"0.5",
	      "RADIALNAMEWIDTH"=>"0.5",
	      "TICKFONT"=>"14",
	      "NAMEFONT"=>"8",
	      "TITLEFONT"=>"54",
	      "SUBFONT"=>"42",

	      //VARIABLES TO BE ADAPTED
	      "CATALOGUE"=>"Confirmed",

	      //TEMPERATURE
	      "CHECKTEQ"=>"yes",
	      "TRANGE"=>"100.0-2500.0",
	      "THABRANGE"=>"250.0-300.0",
	      "PALTEQ"=>"spectral",
	      "LABELTEQ"=>"Effective Temperature (K)",
	      "SYMBTEQ"=>"T_{eq}",
	      "TICKSTEQ"=>"[200,350,400,500,600,700,800,900,1000,2000,3000]",

	      //RADIUS
	      "CHECKRP"=>"yes",
	      "LABELRP"=>"Planetary Radii (\$R_J\$)",
	      "SYMBRP"=>"R_p",
	      "TICKSRP"=>"[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,1.0,2.0,4.0,6.0,8.0,10.0]",

	      //NAMES
	      "QNAMES"=>"yes",

	      //NAME TIPS
	      "QNAMETIPS"=>"yes",

	      //NAME TIPS
	      "QALTERNATE"=>"no",

	      //PRESETS
	      "NEWPRESET"=>"My preset",
	     );

$CATALOGUES=array("Confirmed"=>"open_exoplanet_catalogue.csv",
		  "Kepler"=>"open_exoplanet_catalogue_kepler.csv");

$CATFIELDS=array(
		 array('ID','str','Primary identifier of planet','ID','[]','linear'),
		 array('BF','int','Binary flag (0-3)','BF','[]','linear'),
		 array('Mp','float','Planetary mass (Jupiter masses)','Mp','[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0,2.0,3.0,4.0,5.0,6.0,7.0,8.0,9.0,10.0]','linear'),
		 array('Rp','float','Radius (Jupiter radii)','Rp','[]','linear'),
		 array('P','float','Period (days)','P','[]','log'),
		 array('a','float','Semi-major axis (Astronomical Units)','a','[0.01,0.1,1.0,10.0,100.0]','log'),
		 array('e','float','Eccentricity','e','[0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0]','linear'),
		 array('q','float','Periastron (degree)','q','[]','log'),
		 array('L','float','Longitude (degree)','L','[]','linear'),
		 array('W','float','Ascending node (degree)','W','[]','linear'),
		 array('i','float','Inclination (degree)','i','[]','linear'),
		 array('Teq','float','Surface or equilibrium temperature (K)','Teq','[100,200,300,400,500,600,700,800,900,1000,2000]','log'),
		 array('taup','float','Age (Gyr)','taup','[0.3,0.6,0.9,1.0,2.0,3.0,4.0,5.0,6.0,7.0,8.0,9.0,10.0,20.0]','linear'),
		 array('method','str','Discovery method','method','[]','linear'),
		 array('year','int','Discovery year (yyyy)','year','[]','linear'),
		 array('update','str','Last updated (yy/mm/dd)','update','[]','linear'),
		 array('ra','SEX','Right ascension (hh mm ss)','ra','[0,6,12,18,24]','linear'),
		 array('dec','SEX','Declination (+/-dd mm ss)','dec','[-90,-60,-30,0,30,60,90]','linear'),
		 array('d','float','Distance from Sun (parsec)','d','[0.0,1.0,2.0,3.0,4.0,5.0,6.0,7.0,8.0,9.0,10.0,20.0,30.0,40.0,50.0,60.0,70.0,80.0,90.0,100.0,1000.0]','log'),
		 array('Ms','float','Host star mass (Solar masses)','Ms','[0.1,0.2,0.3,0.4,0.5,0.6,0.8,0.9,1.0,2.0,3.0]','linear'),
		 array('Rs','float','Host star radius (Solar radii)','Rs','[0.1,0.2,0.3,0.4,0.5,0.6,0.8,0.9,1.0,2.0,3.0]','linear'),
		 array('Z','float','Host star metallicity (log relative to solar)','Z','[-1.0,0.0,1.0]','linear'),
		 array('Ts','float','Host star temperature (K)','Ts','[2000,3000,4000,5000,6000,7000,8000,9000,10000,20000]','linear'),
		 array('taus','float','Host star age (Gyr)','taus','[]','linear')
		 );
$SCATFIELDS=$CATFIELDS;

$CATFIELDNAMES=array();
$i=0;
foreach($CATFIELDS as $fields){
  $CATFIELDNAMES[$i]=$fields[0];
  $field=$fields[0];
  $check="CHECK$field";
  $$check="no";
  $ffield="FFIELD$field";
  $$ffield="planet.$field";
  $label="LABEL$field";
  $$label=$fields[2];
  $symbol="SYMB$field";
  $$symbol=$fields[3];
  $palette="PAL$field";
  $$palette="gray";
  $tips="TIPS$field";
  $$tips="None;None";
  $ticks="TICKS$field";
  $$ticks=$fields[4];
  $scale="SCALE$field";
  $$scale=$fields[5];
  $i++;
}

$PALETTES=array('Accent','Greys','PuBuGn','RdYlGn_r','YlOrRd','cool','gist_heat_r','hsv_r','seismic',
		'Accent_r','Greys_r','PuBuGn_r','Reds','YlOrRd_r','cool_r','gist_ncar','jet','seismic_r',
		'Blues','LUTSIZE','PuBu_r','Reds_r','afmhot','coolwarm','gist_ncar_r','jet_r','spec',
		'Blues_r','OrRd','PuOr','ScalarMappable','afmhot_r','coolwarm_r','gist_rainbow','ma','spec_reversed',
		'BrBG','OrRd_r','PuOr_r','Set1','autumn','copper','gist_rainbow_r','mpl','spectral',
		'BrBG_r','Oranges','PuRd','Set1_r','autumn_r','copper_r','gist_stern','np','spectral_r',
		'BuGn','Oranges_r','PuRd_r','Set2','binary','cubehelix','gist_stern_r','ocean','spring',
		'BuGn_r','PRGn','Purples','Set2_r','binary_r','cubehelix_r','gist_yarg','ocean_r','spring_r',
		'BuPu','PRGn_r','Purples_r','Set3','bone','datad','gist_yarg_r','os','summer',
		'BuPu_r','Paired','RdBu','Set3_r','bone_r','division','gnuplot','pink','summer_r',
		'CMRmap','Paired_r','RdBu_r','Spectral','brg','flag','gnuplot2','pink_r','terrain',
		'CMRmap_r','Pastel1','RdGy','Spectral_r','brg_r','flag_r','gnuplot2_r','print_function','terrain_r',
		'Dark2','Pastel1_r','RdGy_r','YlGn','bwr','get_cmap','gnuplot_r','prism','winter',
		'Dark2_r','Pastel2','RdPu','YlGnBu','bwr_r','gist_earth','gray','prism_r','winter_r',
		'GnBu','Pastel2_r','RdPu_r','YlGnBu_r','cbook','gist_earth_r','gray_r','rainbow',
		'GnBu_r','PiYG','RdYlBu','YlGn_r','cmap_d','gist_gray','hot','rainbow_r',
		'Greens','PiYG_r','RdYlBu_r','YlOrBr','cmapname','gist_gray_r','hot_r','register_cmap',
		'Greens_r','PuBu','RdYlGn','YlOrBr_r','colors','gist_heat','hsv','revcmap');
sort($PALETTES);

$HELPICON=<<<HELP
<img src=imgs/help.png width=20px>
HELP;

$CLOSETEXT=<<<CLOSE
<br/><br/>
<i style='font-size:10px'>Double click for closing this help</i>
CLOSE;

$PALHELP=<<<PALETTE
<img src=imgs/palettes.png height=600>
PALETTE;

//////////////////////////////////////////////////////////////////////////////////
//FORM VALUES
//////////////////////////////////////////////////////////////////////////////////
foreach(array_keys($FIELDS) as $field){
  if(isset($_GET[$field])){$$field=$_GET[$field];}
  else if(isset($_POST[$field])){$$field=$_POST[$field];}
  else{$$field=$FIELDS[$field];}
}
foreach(array_keys($_GET) as $field){
    $$field=$_GET[$field];
}
foreach(array_keys($_POST) as $field){
    $$field=$_POST[$field];
}

//////////////////////////////////////////////////////////////////////////////////
//ROUTINES
//////////////////////////////////////////////////////////////////////////////////
function getToday($format)
{
  global $DATE;
  $fields=array("seconds","minutes","hours","mday","wday","mon","year","yday",
		"weekday","month","0");
 
  $date=$format;
  foreach($fields as $field){
    $value=$DATE["$field"];
    if($field=="mon"){
      $value=sprintf("%02d",$DATE["$field"]);
    }
    if($field=="mday"){
      $value=sprintf("%02d",$DATE["$field"]);
    }
    if($field=="hours"){
      $value=sprintf("%02d",$DATE["$field"]);
    }
    if($field=="minutes"){
      $value=sprintf("%02d",$DATE["$field"]);
    }
    if($field=="seconds"){
      $value=sprintf("%02d",$DATE["$field"]);
    }
    $date=str_replace("%$field",$value,$date);
  }
  return $date;
}

function fileOpen($file,$type)
{
  $fl=fopen($file,$type);
  
  if(!$fl){
    echo "<h2>FILE '$file' COULD NOT BE OPEN AS $type</h2>";
    exit(1);
  }
  
  return $fl;
}

function loadFile($file)
{
  $fl=fileOpen($file,"r");
  $info=array();
  $i=0;
  while(!feof($fl)){
    $f=fgets($fl);
    $f=rtrim($f);
    array_push($info,$f);
    $i++;
  }
  fclose($fl);
  
  return $info;
}

function str2int($value)
{
  return sprintf("%d",$value);
}
function genRandom($num)
{ 
  $PHP["SYMBOLS"]=array("a","b","c","d","e","f","g","h","i","j",
                        "k","l","m","n","o","p","q","r","s","t",
                        "u","v","w","x","y","z",1,2,3,4,5,6,7,8,9,0);
  $pass="";
  $nsymbs=count($PHP["SYMBOLS"]);
  for($i=0;$i<$num;$i++){
    $ind=rand(0,$nsymbs-1);
    $char=$PHP["SYMBOLS"][$ind];
    $pass.="$char";
  }

  return $pass;
}

function genOnLoad($javas,$id='onload')
{
  global $PHP,$PROJ;
  $onload="";
$onload=<<<ONLOAD
<img id="$id" o
src="$PROJ[IMGDIR]/php.gif" style="display:none" 
onload="$javas">
ONLOAD;
 return $onload;
}

function finalizePage()
{
echo<<<FINALIZE


</body>
</html>

FINALIZE;
}

function generateSelect($list,$name,$defvalue,$action)
{
  $select=<<<SELECT
<select name='$name' $action>
SELECT;
  foreach($list as $value){
    $opsel="";
    if($value==$defvalue){$opsel="selected";}
    $select.="<option value='$value' $opsel>$value</option>";
  }
  $select.="</select>";
  return $select;
}

function generateCheckboxes($list,$name,$suffix,$defvalues)
{
  $dvalues=preg_split("/,/",$defvalues);
  $select="";
  foreach($list as $descript){
    $value=$descript[0];
    $opsel="";
    foreach($dvalues as $dvalue){
      if($value==$dvalue){$opsel="checked";break;}
    }
    $select.="<input type='checkbox' name='${name}${value}_$suffix' $opsel>$value |";
  }
  return $select;
}

function imgInfo($id,$imgname){
  global $RUNSDIR,$DEFCHART,$HELPICON;
  
  $imgfile="$RUNSDIR/run-$id/charts/$imgname";
$extra=<<<EXTRA
<a target="_blank" href="$RUNSDIR/run-$id/run.log">LOG</a> |
<a target="_blank" href="$RUNSDIR/run-$id/config-$id.py">CONFIG</a> |
EXTRA;
  if($imgname=="error"){
    $imgfile="imgs/error-chart";
    $extra="";
  }else if(!file_exists("$imgfile.png")){
    $imgfile="$DEFCHART";
    $extra="";
  }
  $sizepng=sprintf("%.1f",filesize("$imgfile.png")/1024.0);
  $sizepdf=sprintf("%.1f",filesize("$imgfile.pdf")/1024.0);

$imginfo=<<<IMG
<div style="text-align:center;font-size:10px">Image: $imgfile</div>
<a target="_blank" href="$imgfile.pdf">
<img src="$imgfile-thumb.png" style='width:100%'/>
</a>
$extra
<a target="_blank" href="$imgfile.png">PNG($sizepng)</a> | 
<a target="_blank" href="$imgfile.pdf">PDF($sizepdf)</a> | 
<a target="_blank" href="$imgfile.html">iHTML</a> |
<a target="_blank" href="map.php?map=$imgfile.html">iMAP</a>
<a href="JavaScript:void(null)"
  onClick="explainThis(this,'single')"
  explanation="
Interactive image
">$HELPICON</a>
IMG;

  return $imginfo;
}

//////////////////////////////////////////////////////////////////////////////////
//HEAD
//////////////////////////////////////////////////////////////////////////////////
require_once("styles.css");
echo<<<HEAD
<html>
  <head>
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="windows.js"></script>
    <script type="text/javascript" src="util.js"></script>
$STYLES
  </head>
<body>
HEAD;

//////////////////////////////////////////////////////////////////////////////////
//SESSION INFORMATION
//////////////////////////////////////////////////////////////////////////////////
if(!isset($_COOKIE['SESSID'])){
  $SESSID=genRandom(5);
  setcookie('SESSID',$SESSID);
}else{
  $SESSID=$_COOKIE['SESSID'];
}

//////////////////////////////////////////////////////////////////////////////////
//RUN INFORMATION
//////////////////////////////////////////////////////////////////////////////////
$SESSDIR="run-$SESSID";
$PRESETID="$SESSID";
if(!file_exists("$RUNSDIR/$SESSDIR")){
  shell_exec("cp -Rf $RUNSDIR/template $RUNSDIR/$SESSDIR");
}else{
  shell_exec("rm -rf $RUNSDIR/$SESSDIR/config-$SESSID.py");
}

//////////////////////////////////////////////////////////////////////////////////
//SELECT IMAGE
//////////////////////////////////////////////////////////////////////////////////
?>
