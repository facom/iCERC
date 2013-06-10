<?php
//////////////////////////////////////////////////////////////////////////////////
//COMMON
//////////////////////////////////////////////////////////////////////////////////
$qmap=true;
if(isset($_GET['map'])){
  $map=$_GET['map'];
  if(!file_exists($map)){$qmap=false;}
}else{$qmap=false;}
if(!$qmap){
  echo "<h1 style='text-align:center'>No map found.</h1>";
  return;
}
$mapimg=simplexml_load_file($map);
function isInShape($x,$y,$s,$c,$w,$h)
{
  $coords=preg_split("/,/",$c);
  if($s=="circle"){
    //echo "Testing $x,$y to shape $s with coords $c<br/>";
    $distance=sqrt(($coords[0]-$x)*($coords[0]-$x)+($coords[1]-$y)*($coords[1]-$y));
    //echo "Distance: $distance, Radius: $coords[2]<br/>";
    if($distance<$coords[2]){
      //echo "Inside: $x,$y inside $c<br/>";
      return 1;
    }
  }else if($s=="poly"){
    $ncoords=count($coords);
    $polyX=array();$polyY=array();
    $ix=0;$iy=0;
    for($i=0;$i<$ncoords;$i++){
      if(($i%2)==0){$polyX[$ix++]=$coords[$i];}
      else{$polyY[$iy++]=$coords[$i];}
    }
    $nsides=$ix;
    $j=$nsides-1;
    $oddNodes=0;
    for ($i=0;$i<$nsides;$i++){
      if((($polyY[$i]<$y and $polyY[$j]>=$y) or ($polyY[$j]<$y && $polyY[$i]>=$y)) and 
	 ($polyX[$i]<=$x or $polyX[$j]<=$x)){
	if($polyX[$i]+($y-$polyY[$i])/($polyY[$j]-$polyY[$i])*($polyX[$j]-$polyX[$i])<$x){
	  $oddNodes=!$oddNodes; }
      }
      $j=$i; 
    }
    /*
    if($oddNodes){
      print_r($coords);
      echo ",$nsides,$x,$y,$oddNodes<br/>";
    }
    */
    return $oddNodes;
  }else if($s=="rect"){
    $dx=($x-$coords[0])/($coords[2]-$coords[0]);
    $dy=($y-$coords[1])/($coords[3]-$coords[1]);
    if((dx>0 and dx<1) and (dy>0 and dy<1)) return 1;
  }
  return 0;
}
if(!isset($_GET["execmap"])){
//////////////////////////////////////////////////////////////////////////////////
//JAVASCRIPT AND STYLES
//////////////////////////////////////////////////////////////////////////////////
?>
<html>
  <head>
    <style type="text/css">
      html body{
      padding:0px;
      margin:0px;
      font:10px Arial;
      }
      div.info{
      position:fixed;
      padding:0px;
      background:lightgray;
      display:none;
      font-size:12px
      }
      div.content{
      padding:10px;
      }
      img.map{
      border:none;
      }
    </style>
    <script type="text/javascript">
	function mapExecute(divid,imageid,script,
			    onsuccessfunc,onwaitfunc,
			    onerrorfunc)
	{
	  var x=new XMLHttpRequest();
	  var element=document.getElementById(divid);
	  var image=document.getElementById(imageid);
	  xc=window.event.clientX;
	  xs=document.body.scrollLeft;
	  yc=window.event.clientY;
	  ys=document.body.scrollTop;
	  width=window.innerWidth;
	  width=image.width;
	  height=window.innerHeight;
	  height=image.height;

	  script=script+"xc="+xc+"&"+"yc="+yc;
	  script=script+"&";
	  script=script+"xs="+xs+"&"+"ys="+ys;
	  script=script+"&";
	  script=script+"w="+width+"&"+"h="+height;

	  x.onreadystatechange=function(){
	    rtext=x.responseText;
	    if(x.readyState==4){
	      if(x.status==200){
		onsuccessfunc(element,rtext);
	      }else{
		onerrorfunc(element,rtext);
	      }
	    }else{
	      onwaitfunc(element,rtext);
	    }
	  }

	  x.open("GET",script,true);
	  x.send();
	}
      function zoomImg(img,zoom)
      {
	image=document.getElementById(img);
	width="";
	if(image.style.width==""){
	  if(zoom>0)
	    width=100;
	}else{
	  width=parseInt(image.style.width.replace("%",""));
	  width=width+zoom*20;
	}
	image.style.width=width+"%";
      }
   </script>
  </head>
<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//MAP PAGE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//////////////////////////////////////////////////////////////////////////////////
//MAP COMMAND
//////////////////////////////////////////////////////////////////////////////////
$ajaxcmd=<<<AJAX
mapExecute
  (
   'infobox',
   'image',
   '?map=$map&execmap&',
   function(element,rtext){
     element.style.display='block';
     var coords=rtext.split('::');
     element.style.left=coords[0];
     element.style.top=coords[1];
     element.innerHTML=coords[2];
   },
   function(element,rtext){
     //element.style.display='none';
   },
   function(element,rtext){
   }
   )
AJAX;
//////////////////////////////////////////////////////////////////////////////////
//MAP PARSING
//////////////////////////////////////////////////////////////////////////////////
$img=$mapimg->img["src"];
$width=$mapimg->width_screen;
if(preg_match("/[\w\d]+/",$width)) $width="width:$width";
if(!file_exists("$img")){
  $path=preg_split("/\//",$map);
  $lpath=count($path);
  $dir="";
  for($i=0;$i<$lpath-1;$i++){
    $dir.=$path[$i]."/";
  }
  $img="$dir/$img";
}
//echo "IMG:$img, WIDTH:$width<br/>";
if(!file_exists("$img")){
  echo "<h1 style='text-align:center'>No image found</h1>";
  return;
}
//////////////////////////////////////////////////////////////////////////////////
//CONTENT
//////////////////////////////////////////////////////////////////////////////////
echo<<<CONTENT
<div id="infobox" class="info"></div>
<a href="JavaScript:void(null)" 
  onclick="$ajaxcmd"
  onmousemove="infobox.style.display='none'">
  <img class="map" id="image" src="$img" style="$width">
</a>
CONTENT;

//////////////////////////////////////////////////////////////////////////////////
//ZOOM BUTTONS
//////////////////////////////////////////////////////////////////////////////////
echo<<<CONTENT
<div style="position:fixed;top:0;left:0;background:white;padding:2px">
Zoom:
<a href="JavaScript:zoomImg('image',+1)">In</a> |
<a href="JavaScript:zoomImg('image',-1)">Out</a>
</div>
CONTENT;

}
else{
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//MAP INFORMATION PROVIDER
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//////////////////////////////////////////////////////////////////////////////////
//PARSE IMAGE
//////////////////////////////////////////////////////////////////////////////////
$width=$mapimg->width;
$height=$mapimg->height;
//////////////////////////////////////////////////////////////////////////////////
//MAP EXEC
//////////////////////////////////////////////////////////////////////////////////
//COORDINATES OF THE POINT
$xc=$_GET['xc'];
$yc=$_GET['yc'];
//SCROLL POSITION
$xs=$_GET['xs'];
$ys=$_GET['ys'];
//WIDTH AND HEIGHT OF THE IMAGE
$w=$_GET['w'];
$h=$_GET['h'];
//RETURN SCREEN POSITION OF THE MOUSE
echo "$xc::$yc::";
//COMPUTE ABSOLUTE POSITION IN THE SCREEN
$xa=($xc+$xs);
$ya=($yc+$ys);
//COMPUTE RELATIVE POSITION
$xr=$xa/$w;
$yr=$ya/$h;
//COMPUTE ABSOLUTE POSITION IN THE IMAGE
$xi=$xr*$width;
$yi=$yr*$height;
//LOOKING FOR INFORMATION
$areas=array();$i=0;
foreach($mapimg->map->area as $area){$areas[$i++]=$area;}
foreach(array_reverse($areas) as $area){
  echo $area;
  $shape=$area["shape"];
  $coords=$area["coords"];
  if(isInShape($xi,$yi,$shape,$coords,$width,$height))
    $selarea=$area;
}
if(isset($selarea)){
  //print_r($selarea);
  $infoxml=$selarea->info;
  $info=$infoxml->asXML();
  $info=preg_replace("/display:none/","display:block",$info);
}else{
  $info="Click into a sensitive area";
}
//DISPLAY INFORMATION
/*
echo "Screen:$xa/$w,$ya/$h<br/>";
echo "Relative:$xr,$yr<br/>";
echo "Image:$xi,$yi<br/>";
echo "Information:";
*/
echo "<div class='content'>$info</div>";
}
?>
</html>
