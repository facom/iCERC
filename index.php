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
include_once("common.php");

//////////////////////////////////////////////////////////////////////////////////
//SAVE CONFIGURATION FILE
//////////////////////////////////////////////////////////////////////////////////
if(isset($SAVEPRESET)){
  $parts=preg_split("/\//",$CONFIGFILE);
  $fname=end($parts);
  $fname=preg_replace("/config-/","",$fname);
  $fname=preg_replace("/\.py/","",$fname);
  if(file_exists("pres/$fname") and false){
    echo "Preset already exist.<br/>";
  }else{
    shell_exec("mkdir 'pres/$fname'");
    shell_exec("cp -rf runs/run-$SESSID/config-$SESSID.py 'pres/$fname/config.py'");
    shell_exec("cp -rf runs/run-$SESSID/database.dat 'pres/$fname/database.dat'");
    shell_exec("cp -rf runs/run-$SESSID/charts/*[0-9].png 'pres/$fname/chart.png'");
    shell_exec("cp -rf runs/run-$SESSID/charts/*[0-9].pdf 'pres/$fname/chart.pdf'");
    shell_exec("cp -rf runs/run-$SESSID/charts/*[0-9].html 'pres/$fname/chart.html'");
    shell_exec("sed -e 's/CERC[-a-zA-Z0-9]*/chart/' 'pres/$fname/chart.html' > tmp/html.$SESSID");
    shell_exec("cp -rf tmp/html.$SESSID 'pres/$fname/chart.html'");
    shell_exec("cp -rf runs/run-$SESSID/charts/*[0-9]-thumb.png 'pres/$fname/chart-thumb.png'");
  }
  echo "Preset saved.";
  $CONFIGFILE="pres/$fname/config.py";
}
//////////////////////////////////////////////////////////////////////////////////
//LOAD CONFIGURATION FILE
//////////////////////////////////////////////////////////////////////////////////
$imgname="test";
if(!isset($CONFIGFILE)){
  $CONFIGFILE="pres/Default/config.py";
}
if(file_exists("$CONFIGFILE")){
  $confsource=$CONFIGFILE;
  $lines=loadFile($confsource);
  foreach($lines as $line){
    if(preg_match("/^#/",$line) or
       preg_match("/^from/",$line) or
       !preg_match("/[\w\d]/",$line)
       ){continue;}
    preg_match("/(\w+)=(.*)/",$line,$matches);
    $var=$matches[1];
    $value=preg_replace("/[\"]/","",$matches[2]);
    $$var=$value;
    if($VERBOSE) echo "VAR: $var<br/>";
    if($VERBOSE) echo "VALUE: ".$$var."<br/>";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //COPY CONFIGURATION FILE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if(preg_match("/pres\//",$CONFIGFILE)){
    $parts=preg_split("/\//",$CONFIGFILE);
    $fname=$parts[1];
    shell_exec("rm -rf runs/run-$SESSID/charts/*.*");
    shell_exec("cp -rf 'pres/$fname/config.py' runs/run-$SESSID/config-$SESSID.py");
    shell_exec("cp -rf 'pres/$fname/database.dat' runs/run-$SESSID/database.dat");
    shell_exec("cp -rf 'pres/$fname/chart.pdf' runs/run-$SESSID/charts/CERC-0.pdf");
    shell_exec("cp -rf 'pres/$fname/chart.png' runs/run-$SESSID/charts/CERC-0.png");
    shell_exec("cp -rf 'pres/$fname/chart.html' runs/run-$SESSID/charts/CERC-0.html");
    shell_exec("sed -e 's/chart/CERC-0/' runs/run-$SESSID/charts/CERC-0.html > tmp/html.$SESSID");
    shell_exec("cp -rf tmp/html.$SESSID runs/run-$SESSID/charts/CERC-0.html");
    shell_exec("cp -rf 'pres/$fname/chart-thumb.png' runs/run-$SESSID/charts/CERC-0-thumb.png");
    $imgname="CERC-0";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LOAD SPECIAL VARIABLES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  foreach(array_keys($CATALOGUES) as $CATALOGUE){
    if($CATALOGUES[$CATALOGUE]=="$CATFILE") break;
  }
  $TICKSRP=$RTICKS;
  
  $QNAMESF=$QNAMES;
  $QNAMES="yes";
  if($QNAMESF=="False") $QNAMES="no";

  $CHECKRP="yes";
  if($QSLICE=="False") $CHECKRP="no";

  $QNAMETIPSF=$QNAMETIPS;
  $QNAMETIPS="yes";
  if($QNAMETIPSF=="False") $QNAMETIPS="no";

  $QALTERNATEF=$QALTERNATE;
  $QALTERNATE="yes";
  if($QALTERNATEF=="False") $QALTERNATE="no";

  $PALTEQ=preg_replace("/cm\./","",$TPALETTE);
  
  $CHECKTEQ="yes";
  if($QTEMPERATURE=="False") $CHECKTEQ="no";

  $TRANGE="$TCMINA-$TCMAXA";
  $THABRANGE="$THAB-$TOUT";

  $ticks=preg_split("/\+\[THAB/",$TTICKS);
  $TICKSTEQ=$ticks[0];
  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LOAD ARCS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($VERBOSE) echo "Loading arcs...<br/>";
  if($VERBOSE) echo "ARCS: $ARCS<br/>";
  $arcs=preg_replace("/^\[/","",$ARCS);
  $varinfo=preg_split("/\),/",$arcs);
  if($VERBOSE) print_r($varinfo);
  
  $vararcs=array();
  foreach($varinfo as $var){
    if(preg_match("/^]$/",$var)) continue;
    if($VERBOSE) echo "VAR: $var<br/>";
    $var=preg_replace("/^\(/","",$var);
    $fields=preg_split("/,/",$var);
    $field=preg_replace("/'/","",$fields[0]);
    $vararcs[$field]=$fields;
  }

  //CHECKFIELDS
  foreach($SCATFIELDS as $catfield){
    $field=$catfield[0];
    $fieldtype=$catfield[1];
    $fieldname=$catfield[2];
    if($field=="ID"){continue;}
    if(preg_match("/'$field'/",$ARCS)){
      if($VERBOSE) print "Field: $field<br/>";
      $varconf=$vararcs["planet.$field"];
      $check="CHECK$field";
      $$check="yes";
      if($VERBOSE) print "Check:".$$check."<br/>";
      $label="LABEL$field";
      $$label=preg_replace("/'/","",$varconf[2]);
      if($VERBOSE) print "Label:".$$label."<br/>";
      $symbol="SYMB$field";
      $$symbol=preg_replace("/'/","",$varconf[1]);
      if($VERBOSE) print "Symbol:".$$symbol."<br/>";
      $palette="PAL$field";
      $$palette=preg_replace("/cm\./","",$varconf[3]);
      if($VERBOSE) print "Palette:".$$palette."<br/>";
      $tips="TIPS$field";
      $$tips=preg_replace("/'/","",$varconf[4]);
      if($VERBOSE) print "Tips:".$$tips."<br/>";
      $ticks="TICKS$field";
      $$ticks=implode(",",array_slice($varconf,6,100));
      if($VERBOSE) print "Ticks:".$$ticks."<br/>";
      $scale="SCALE$field";
      $$scale=preg_replace("/'/","",$varconf[5]);
      if($VERBOSE) print "Scale:".$$scale."<br/>";
    }
  }
  $CATFIELDS=$SCATFIELDS;
  
}else{
  $confsource="Default Values";
}
if(!isset($TIMEFLAG)) $TIMEFLAG=$TIME;

//////////////////////////////////////////////////////////////////////////////////
//GENERATE ACTION
//////////////////////////////////////////////////////////////////////////////////
$ajaxcmd=<<<AJAX
submitForm
  (
   'formgen',
   'genchart.php?TIMEFLAG='+getDateNow(),
   'content',
   function(element,rtext){
     element.innerHTML=rtext;
     $('#DIVBLANKET').css('display','none');
     $('#DIVOVER').css('display','none');
   },
   function(element,rtext){
     $('#DIVBLANKET').css('display','block');
     $('#DIVOVER').css('display','block');
   },
   function(element,rtext){
   }
   )
AJAX;

//////////////////////////////////////////////////////////////////////////////////
//HEADER
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<div class="header">
<a href="index.php">
<img src="imgs/iCERC-bar.png" height=120px/>
</a>
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//HEADER
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<form id="formgen"
      method="get" 
      action="JavaScript:void(null)" 
      enctype="multipart/form-data"
      onSubmit="$ajaxcmd">
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//MAIN TABLE
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<table class="main">
<tr>
<td class="column_form" valign="top">
<div style="text-align:center;font-size:10px">
<a href="JavaScript:void(null)"
  onclick="
toggleElement('TABPLANETS');
toggleElement('TABPROPERTIES');
toggleElement('TABDECORATION');
toggleElement('TABPRESETS');
">
  Show/Hide All
</a><br/>
  Configuration source: $confsource
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//PLANETS SELECTION
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<div class="section">
<a href="JavaScript:void(null)" onclick="toggleElement('TABPLANETS')">
Which planets?
</a>
</div>
<div class="hidden" id="TABPLANETS">
<table class="form">
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CATALOGUE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$catsel=generateSelect(array_keys($CATALOGUES),"CATALOGUE_Submit",$CATALOGUE,
		       "style='font-size:18px;width:100%'");
echo<<<PAGE
<tr>
<td class="field">
Catalogue
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Input catalogue: Confirmed, Kepler
">$HELPICON</a><br/>
$catsel
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FILTER
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Filter
<a href="JavaScript:void(null)" onclick="explainThis(this,'double')" explanation="
Filter command.  Use python syntaxis.$CLOSETEXT
">$HELPICON</a><br/>
<textarea cols="30" rows="2" name="QUERY_Submit">
$QUERY
</textarea>
<p></p>
<button id='filter' 
  name="Filter_Submit" value="0" 
  onclick="$(this).attr('value',1);$('#preview').attr('value',0)">Filter</button>
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FILTER
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Database:<br/>
<a id="database" href="CERC/data/open_exoplanet_catalogue_kepler.csv"
   target="_blank">
Complete
</a>
<br/>
<a id="filtered" href="runs/run-$SESSID/database.dat"
   target="_blank">
Filtered
</a>
</td>
</tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//PROPERTIES
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<div class="section">
<a href="JavaScript:void(null)" onclick="toggleElement('TABPROPERTIES')">
Which properties?
</a>
</div>
<div class="hidden" id="TABPROPERTIES">
<table class="form">
PAGE;

echo<<<PAGE
<tr><td class="field" style="border-bottom-style:solid"><b>Basic</b></td></tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//BEGIN TEMPERATURE
//============================================================
echo<<<PAGE
<tr><td class="field">
PAGE;

if($CHECKTEQ=="yes") $showstar="show";
else $showstar="hiddenstar";
$selprop=generateSelect(array('yes','no'),"CHECKTEQ_Submit","$CHECKTEQ","class='form' onchange=\"toggleElement('STARTEQ')\"");
echo<<<PAGE
Equilibrium temperature (K) $selprop
<a href="JavaScript:void(null)" onclick="toggleElement('TABTEMPERATURE')">
<sup>details</sup></a>
<div id="STARTEQ" class="$showstar"><img src="imgs/star.png"></div>
<div class="hidden" id="TABTEMPERATURE" style="background:#CCCCCC">
<table class="form">
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Temperature color coding range
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Use '-' to separate maximum and minimum
">$HELPICON</a><br/>
<input type='text' name="TRANGE_Submit" value="$TRANGE" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Habitable Zone temperature range
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Use '-' to separate maximum and minimum
">$HELPICON</a><br/>
<input type='text' name="THABRANGE_Submit" value="$THABRANGE" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Label
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Label
">$HELPICON</a><br/>
<input type='text' name="LABELTEQ_Submit" value="$LABELTEQ" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Symbol
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Symbol
">$HELPICON</a><br/>
<input type='text' name="SYMBTEQ_Submit" value="$SYMBTEQ" class="formimp">
</td>
</tr>
PAGE;

$palsel=generateSelect($PALETTES,"PALTEQ_Submit",$PALTEQ,"class='form'");
echo<<<PAGE
<tr>
<td class="field">
Palette
<a href="JavaScript:void(null)" onclick="explainThis(this,'double')" explanation="
Palette<br/>$PALHELP $CLOSETEXT
">$HELPICON</a><br/>
$palsel
</td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Ticks
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Ticks
">$HELPICON</a><br/>
<textarea name="TICKSTEQ_Submit" cols=40 rows=2>
$TICKSTEQ
</textarea>
</td>
</tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;
//============================================================
//END TEMPERATURE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//BEGIN RADIUS
//============================================================
echo<<<PAGE
<tr><td class="field">
PAGE;

if($CHECKRP=="yes") $showstar="show";
else $showstar="hiddenstar";
$selprop=generateSelect(array('yes','no'),"CHECKRP_Submit","$CHECKRP","class='form' onchange=\"toggleElement('STARRP')\"");
echo<<<PAGE
Planetary Radius (RJ) $selprop
<a href="JavaScript:void(null)" onclick="toggleElement('TABRADIUS')">
<sup>details</sup></a>
<div id="STARRP" class="$showstar"><img src="imgs/star.png"></div>
<div class="hidden" id="TABRADIUS" style="background:#CCCCCC">
<table class="form">
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Label
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Label
">$HELPICON</a><br/>
<input type='text' name="LABELRP_Submit" value="$LABELRP" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Symbol
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Symbol
">$HELPICON</a><br/>
<input type='text' name="SYMBRP_Submit" value="$SYMBRP" class="formimp">
</td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Ticks
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Ticks
">$HELPICON</a><br/>
<textarea name="TICKSRP_Submit" cols=40 rows=2>
$TICKSRP
</textarea>
</td>
</tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;
//============================================================
//END RADIUS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

echo<<<PAGE
<tr><td class="field" style="border-bottom-style:solid"><b>Additional</b></td></tr>
PAGE;

foreach($CATFIELDS as $catfield){
  $field=$catfield[0];
  $fieldtype=$catfield[1];
  $fieldname=$catfield[2];
  if($field=="ID"){continue;}

  $tab="TAB$field";
  $check="CHECK$field";
  $check_value=$$check;
  $label="LABEL$field";
  $label_value=$$label;
  $ffield="FFIELD$field";
  $ffield_value=$$ffield;
  $symbol="SYMB$field";
  $symbol_value=$$symbol;
  $palette="PAL$field";
  $palette_value=$$palette;
  $tips="TIPS$field";
  $tips_value=$$tips;
  $ticks="TICKS$field";
  $ticks_value=$$ticks;
  $scale="SCALE$field";
  $scale_value=$$scale;

  $classstar="hiddenstar";
  if($check_value=="yes"){
    $classstar="show";
  }
  
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//BEGIN PROPERTY
//============================================================
echo<<<PAGE
<tr><td class="field">
PAGE;

$selprop=generateSelect(array('yes','no'),"${check}_Submit","$check_value","class='form' onchange=\"toggleElement('STAR$field')\"");
echo<<<PAGE
$fieldname $selprop
<a href="JavaScript:void(null)" onclick="toggleElement('$tab')">
<sup>details</sup></a>
<div id="STAR$field" class="$classstar"><img src="imgs/star.png"></div>
<div class="hidden" id="$tab" style="background:#CCCCCC">
<table class="form">
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Field
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Field.  You can write here a formula.
">$HELPICON</a><br/>
<input type='text' name="${ffield}_Submit" value="$ffield_value" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Label
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Label
">$HELPICON</a><br/>
<input type='text' name="${label}_Submit" value="$label_value" class="formimp"></td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Symbol
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Symbol
">$HELPICON</a><br/>
<input type='text' name="${symbol}_Submit" value="$symbol_value" class="formimp">
</td>
</tr>
PAGE;

$palsel=generateSelect($PALETTES,"${palette}_Submit",$palette_value,"class='form'");
echo<<<PAGE
<tr>
<td class="field">
Palette
<a href="JavaScript:void(null)" onclick="explainThis(this,'double')" explanation="
Palette<br/>$PALHELP $CLOSETEXT
">$HELPICON</a><br/>
$palsel
</td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Line tips
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Indicate the properties of the tips.<br/>
Syntax: [tipini properties];[tipend properties]<br/>
Tip properties: [point|None]:[symbol]:[markersize]:[color]<br/>
">$HELPICON</a><br/>
<input type='text' name="${tips}_Submit" value="$tips_value" class="formimp">
</td>
</tr>
PAGE;

echo<<<PAGE
<tr>
<td class="field">
Ticks
<a href="JavaScript:void(null)" onclick="explainThis(this,'single')" explanation="
Ticks
">$HELPICON</a><br/>
<textarea name="${ticks}_Submit" cols=40 rows=2>
$ticks_value
</textarea>
</td>
</tr>
PAGE;

$scalesel=generateSelect(array('linear','log'),"${scale}_Submit",$scale_value,"class='form'");
echo<<<PAGE
<tr>
<td class="field">
Scale
<a href="JavaScript:void(null)" onclick="explainThis(this,'double')" explanation="
Scale
">$HELPICON</a><br/>
$scalesel
</td>
</tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;
//============================================================
//END PROPERTY
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}


echo<<<PAGE
</td></tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//DECORATION
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<div class="section">
<a href="JavaScript:void(null)" onclick="toggleElement('TABDECORATION')">
Customize
</a>
</div>
<div class="hidden" id="TABDECORATION">
<table class="form">
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//TITLE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Title
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Use this ID to recover a previously generated chart.
">$HELPICON</a><br/>
<input type="text" name="TITLE_Submit" value="$TITLE" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//TITLE FONT
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Title font
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Font size for the title
">$HELPICON</a><br/>
<input type="text" name="TITLEFONT_Submit" value="$TITLEFONT" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//SUBTITLE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Subtitle
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Subtitle.
">$HELPICON</a><br/>
<input type="text" name="SUBTITLE_Submit" value="$SUBTITLE" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//SUBTITLE FONT
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Subtitle font
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Font size for the subtitle
">$HELPICON</a><br/>
<input type="text" name="SUBFONT_Submit" value="$SUBFONT" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FIGSCALE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Figure size ratio
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Ratio of frame size to chart radius
">$HELPICON</a><br/>
<input type="text" name="FIGSCALE_Submit" value="$FIGSCALE" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FIGRES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Figure resolution
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Figure resolution in DPI.  For high quality printing use 600 dpi.
">$HELPICON</a><br/>
<input type="text" name="FIGDPI_Submit" value="$FIGDPI" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//TEMPERATURE WIDTH
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Temperature scale width
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Angular width of the temperature scale
">$HELPICON</a><br/>
<input type="text" name="TWIDTH_Submit" value="$TWIDTH" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//SLICE WIDTH
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Radial slice width
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Angular width of the slice
">$HELPICON</a><br/>
<input type="text" name="RADIALSLICE_Submit" value="$RADIALSLICE" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//NAMES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$selnames=generateSelect(array('yes','no'),"QNAMES_Submit","$QNAMES","class='form'");
echo<<<PAGE
<tr>
<td class="field">
Do you want planetary names?
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Planetary names are in the border of the chart
">$HELPICON</a><br/>
$selnames
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//NAME TIPS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$seltips=generateSelect(array('yes','no'),"QNAMETIPS_Submit","$QNAMETIPS","class='form'");
echo<<<PAGE
<tr>
<td class="field">
Tips at the name arrows?
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Do you want circle at the tips of the arrows
">$HELPICON</a><br/>
$seltips
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//NAME ALTERNATE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$seltips=generateSelect(array('yes','no'),"QALTERNATE_Submit","$QALTERNATE","class='form'");
echo<<<PAGE
<tr>
<td class="field">
Do you want to alternate names?
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Alternate name position
">$HELPICON</a><br/>
$seltips
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//NAME FONT
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Names fontsize
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Font size for the subtitle
">$HELPICON</a><br/>
<input type="text" name="NAMEFONT_Submit" value="$NAMEFONT" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//OFFSET NAMES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Names positions offset
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Angle at which the names start
">$HELPICON</a><br/>
<input type="text" name="OFFNAME_Submit" value="$OFFNAME" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//ANGULAR DISTANCE BETWEEN ARCS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Angular distance between property arcs
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Angular distance between property arcs
">$HELPICON</a><br/>
<input type="text" name="INTERARC_Submit" value="$INTERARC" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//RADIAL LINE WIDTHS FOR NAME
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Name radial lines width
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Width of the radial lines for names
">$HELPICON</a><br/>
<input type="text" name="RADIALNAMEWIDTH_Submit" value="$RADIALNAMEWIDTH" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//TICK FONTS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Tick fontsize
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Font size for ticks
">$HELPICON</a><br/>
<input type="text" name="TICKFONT_Submit" value="$TICKFONT" class="formimp">
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//RADIAL LINE WIDTHS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Radial lines width
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Width of the radial lines
">$HELPICON</a><br/>
<input type="text" name="RADIALWIDTH_Submit" value="$RADIALWIDTH" class="formimp">
</td>
</tr>
PAGE;


echo<<<PAGE
</table>
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//SAVE
//////////////////////////////////////////////////////////////////////////////////
echo<<<PAGE
<div class="section">
<a href="JavaScript:void(null)" onclick="toggleElement('TABPRESETS')">
Presets
</a>
</div>
<div class="hidden" id="TABPRESETS">
<table class="form">
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//ID
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
This plot ID
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Use this ID to recover a previously generated chart.
">$HELPICON</a> <br/>
$SESSID
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//PRESET BY ID
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
Load a previous run
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Use and ID to reload a preset
">$HELPICON</a><br/>
<input id="loadid" type="text" name="PRESETID_Submit" value="$PRESETID" class="formimp">
<a href="JavaScript:void(null)" onclick="loadRun('$PAGENAME?','loadid')">Load</a>
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//SAVE PRESET
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
echo<<<PAGE
<tr>
<td class="field">
New community preset
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Save this run as a community preset
">$HELPICON</a><br/>
<input id="saveid" type="text" name="NEWPRESET_Submit" value="$NEWPRESET" class="formimp">
<a href="JavaScript:void(null)" onclick="loadRun('$PAGENAME?SAVEPRESET&','saveid')">Save</a>
</td>
</tr>
PAGE;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//COMMUNITY PRESETS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$list=shell_exec("cd pres;ls -md *");
$presets=preg_split("/\s*,\s*/",$list);
$selpres="<select id='selpres' name='PRESETS'>";
foreach($presets as $preset){
  if(!preg_match("/\w/",$preset)) continue;
  $valpres="$preset/config.py";
  $selpres.="<option value='$valpres'>$preset";
}
$selpres.="</select>";
echo<<<PAGE
<tr>
<td class="field">
Community presets
<a href="JavaScript:void(null)" onclick="explainThis(this)" explanation="
Choose a preset used by somebody else in the iCERC comunity
">$HELPICON</a><br/>
$selpres
<a href="JavaScript:void(null)" onclick="loadPreset('$PAGENAME','selpres')">Load</a>
</td>
</tr>
PAGE;

echo<<<PAGE
</table>
</div>
PAGE;

//////////////////////////////////////////////////////////////////////////////////
//INTERMEZZO
//////////////////////////////////////////////////////////////////////////////////
echo "</td>";

//////////////////////////////////////////////////////////////////////////////////
//PREVIEW
//////////////////////////////////////////////////////////////////////////////////
$imginfo=imgInfo($SESSID,$imgname);
echo<<<PAGE
<td class="column_preview" valign="top">
<div style="text-align:center;padding:5px;">
<button id='preview' 
  name="Preview_Submit" value="0" 
  onclick="$(this).attr('value',1);$('#update').attr('value',0);$('#filter').attr('value',0)">Preview</button>
<button id='update'
  name="Update_Submit" value="0" 
  onclick="$(this).attr('value',1);$('#preview').attr('value',0);$('#filter').attr('value',0)">Update Figure</button>
</div>
<div id="DIVBLANKET" class="DIVBLANKET" style="display:none"></div>
<img id="DIVOVER" class="DIVOVER" 
  src="imgs/load.gif" style="display:none"/>
<div id="content" style="text-align:center" class="preview">
$imginfo
</div>
</td>
</tr>
</table>
</form>
PAGE;

finalizePage();
?>
