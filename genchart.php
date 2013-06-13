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
# MAIN FORM
################################################################################
*/
include("common.php");

if($Filter){
  $qfilter="1";
}else{
  $qfilter="";
}

if(!$Update){
$VERBOSE=0;
//////////////////////////////////////////////////////////////////////////////////
//PREPARE VARIABLES
//////////////////////////////////////////////////////////////////////////////////

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CATALOGUE FILE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$CATALOGUE=$CATALOGUES[$CATALOGUE];
if($VERBOSE) echo "CATALOGUE:".$CATALOGUE."<br/>";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CATALOGUE FIELDS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$CATFS="[";
for($i=0;$i<count($CATFIELDS);$i++){
  $field=$CATFIELDS[$i];
  $CATFS.="('$field[0]',$field[1],'$field[2]'),";
}
$CATFS.="]";
if($VERBOSE) echo "CATFIELDS:".$CATFS."<br/>";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//ARCS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$CATREQS="[";
if($CHECKTEQ=='yes'){
  $CATREQS.="'Teq',";
}
if($CHECKRP=='yes'){
  $CATREQS.="'Rp',";
}
$ARCS="[";
for($i=0;$i<count($CATFIELDS);$i++){
  $catfield=$CATFIELDS[$i];
  $field=$catfield[0];
  $checkvar="CHECK$field";
  if($$checkvar=="yes"){
    $ffield="FFIELD$field";$ffield=$$ffield;
    $symb="SYMB$field";$symb=$$symb;
    $label="LABEL$field";$label=$$label;
    $palette="PAL$field";$palette=$$palette;
    $tips="TIPS$field";$tips=$$tips;
    $scale="SCALE$field";$scale=$$scale;
    $ticks="TICKS$field";$ticks=$$ticks;
    $CATREQS.="'$field',";
$ARCS.=<<<ARCS
('$ffield','$symb','$label',cm.$palette,'$tips','$scale',$ticks),
ARCS;
  }
}
$ARCS.="]";
$CATREQS.="]";
if($VERBOSE) echo "ARCS:".$ARCS."<br/>";
if($VERBOSE) echo "CATREQS:".$CATREQS."<br/>";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//TEMPERATURE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$Ts=preg_split("/\s*-\s*/",$THABRANGE);
$THAB=$Ts[0];
$TOUT=$Ts[1];
if($VERBOSE) echo "THABS: $THAB - $TOUT<br/>";

$Ts=preg_split("/\s*-\s*/",$TRANGE);
$TCMINA=$Ts[0];
$TCMAXA=$Ts[1];
if($VERBOSE) echo "TRANGE: $TCMINA - $TCMAXA<br/>";

$QTEMPERATURE="True";
if($CHECKTEQ!="yes"){$QTEMPERATURE="False";}
$QSLICE="True";
if($CHECKRP!="yes"){$QSLICE="False";}
if($VERBOSE) echo "TEMP: $QTEMPERATURE, SLICE: $QSLICE<br/>";
$QNAMESBOOL="True";
if($QNAMES=="no"){$QNAMESBOOL="False";}
if($VERBOSE) echo "QNAMES: $QNAMESBOOL<br/>";

$QALTERNATEBOOL="True";
if($QALTERNATE=="no"){$QALTERNATEBOOL="False";}

$QNAMETIPSBOOL="True";
if($QNAMETIPS=="no"){$QNAMETIPSBOOL="False";}

//////////////////////////////////////////////////////////////////////////////////
//GENERATE CONFIGURATION
//////////////////////////////////////////////////////////////////////////////////
$config=<<<CONFIG
################################################################################
# CERC CONFIGURATION FILE
################################################################################
from util import *

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#CUSTOMIZE
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Figure file
FIGFILE="CERC"

#Resolution of the figure
FIGDPI=$FIGDPI
FACDPI=(FIGDPI/600.0)

#Title
#Leave empty to remove
TITLE="$TITLE"

#Subtitle
#Leave empty to remove
SUBTITLE="$SUBTITLE"

#Watermark
#Leave empty to remove
WATERMARK="Zuluaga (2013), http://astronomia.udea.edu.co/research/CERC"

#Scale of the figure (in chart radius)
FIGSCALE=$FIGSCALE

#Inner radius of the color scales (in chart radius)
ARCSCALE=1.2

#Size of the figure
FIGWIDTH=int(24*FIGDPI/600)

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#DATABASE
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Catalogue
CATFILE="$CATALOGUE"

#Fields of the catalogue
CATFIELDS=$CATFS

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#ARC SCALES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
ARCS=$ARCS

#Inter Arc distance
INTERARC=$INTERARC

#Inner radius of the scales
AR1=2*ARCSCALE

#outher radius of the scales
AR2=2.2*ARCSCALE

#WIDTH OF THE SCALE ARC LINES
SCALEWIDTH=2

#Width of the ticks in the arc scale
TICKWIDTH=2

#Radial extent of the ticks
TICKH=0.05

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#FILTERS
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Required fields
CATREQ=$CATREQS

#Filters
QUERY="$QUERY"

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#LINES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Width of the lines
LW=1.0*FIGDPI/600

#Width of the radial lines
RADIALWIDTH=$RADIALWIDTH

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#FONTS
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
TICKFONT=$TICKFONT
NAMEFONT=$NAMEFONT
TITLEFONT=$TITLEFONT
WATERFONT=24
SUBFONT=$SUBFONT

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#EQUILIBRIUM TEMPERATURES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include Temperature Scale
QTEMPERATURE=$QTEMPERATURE

#Pallete of the equilibrium temperature
TPALETTE=cm.$PALTEQ

#Width of the temperature scale
TWIDTH=$TWIDTH

#Habitable zone limits
THAB=$THAB
TOUT=$TOUT

#Limits of equilibrium temperatures
TCMINA=$TCMINA
TCMAXA=$TCMAXA

#Temperature ticks
TTICKS=$TICKSTEQ+[THAB,(THAB+TOUT)/2,TOUT]

#HZ Gap
TSTEP=0.5

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#RADIAL SCALE
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include radial slice
QSLICE=$QSLICE

#Radial ticks
RTICKS=$TICKSRP

#Radial slice
RADIALSLICE=$RADIALSLICE

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#PLANETARY NAMES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include planetary names
QNAMES=$QNAMESBOOL

#Offset Arc Name
OFFNAME=$OFFNAME

#Alternate name position
QALTERNATE=$QALTERNATEBOOL

#Tips
QNAMETIPS=$QNAMETIPSBOOL

#Width of the radial lines
RADIALNAMEWIDTH=$RADIALNAMEWIDTH

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#ADDITIONAL
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
TIMEFLAG="$TIMEFLAG";
CONFIG;

//////////////////////////////////////////////////////////////////////////////////
//CREATE FILE
//////////////////////////////////////////////////////////////////////////////////
$fc=fopen("$RUNSDIR/$SESSDIR/config-$SESSID.py","w");
fwrite($fc,$config);
fclose($fc);

//////////////////////////////////////////////////////////////////////////////////
//RUN
//////////////////////////////////////////////////////////////////////////////////
$out=0;
$figsuf="$SESSID-$TIMEFLAG";
$figfile="CERC-$figsuf";
$cmd="cd $RUNSDIR/$SESSDIR;$PYTHONCMD CERC.py config-$SESSID.py $figsuf $qfilter &> run.log;echo $?";
if(!$TEST){
  shell_exec("cd $RUNSDIR/$SESSDIR;rm -rf charts/*.{png,pdf}");
  $out=shell_exec($cmd);
  if(str2int($out)){
echo<<<ERROR
<div style="background:yellow">
<a target="_blank" href="$RUNSDIR/$SESSDIR/run.log">Error</a>
</div>
ERROR;
     $figfile="error";
  }
  shell_exec("$CONVERT -resize 30% $RUNSDIR/$SESSDIR/charts/$figfile.png $RUNSDIR/$SESSDIR/charts/$figfile-thumb.png &> tmp/convert.log");
}
sleep(1);
}else{
  $figsuf="$SESSID-$TIMEFLAG";
  $figfile="CERC-$figsuf";
}
//////////////////////////////////////////////////////////////////////////////////
//OUTPUT
//////////////////////////////////////////////////////////////////////////////////
if($TEST){
  echo "CMD: $cmd<br/>";
  echo "CONVERT: $CONVERT<br/>";
  print_r($_GET);
}
$imginfo=imgInfo($SESSID,$figfile);
echo<<<OUTPUT
$imginfo
OUTPUT;
?>
