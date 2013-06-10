function loadContent(script,elementid,
		     onsuccessfunc,onwaitfunc,onerrorfunc,
		     timeout,async)
{
  var x=new XMLHttpRequest();
  var element=document.getElementById(elementid);
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
  x.open("POST",script,async);
  x.send();
  if(timeout>0){
      callback="loadContent('"+script+"','"+elementid+"',"+onsuccessfunc.toString()+","+onwaitfunc.toString()+","+onerrorfunc.toString()+","+timeout.toString()+","+async.toString()+")";
      setTimeout(callback,timeout);
  }
}

function submitForm(formid,script,elementid,
		    onsuccessfunc,onwaitfunc,
		    onerrorfunc)
{
  var x=new XMLHttpRequest();
  var element=document.getElementById(elementid);
  var form=document.getElementById(formid);
  
  //GET SUBMIT FORM ELEMENTS
  i=0;
  qstring="";
  while(formel=form.elements[i]){
    if(esname=$(formel).attr("name")){
      if(esname.search("_Submit")>=0){
	ename=esname.split("_")[0];
	value=$(formel).attr("value")
	qstring+=ename+"="+value+"&";
      }
    }
    i++;
  }
  script=script+"&"+qstring;
  
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

  script=script+"?";
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

function strLength(string)
{
  var ruler=$("#RULER");
  ruler.html(string);
  return ruler.width();
}

function explainThis(object,action)
{
  name=$(object).attr("name");
  explanation=$(object).attr("explanation");
  boxclass="explanation";
  boxref='.'+boxclass;

  boxwidth=strLength(explanation);
  boxheight='';
  elheight=$(object).height();
  boxtop=+elheight;
  boxleft=0;
  
  unheight="px";
  unwidth="";
  $.openDOMWindow({
        height:boxheight+unheight,
	width:boxwidth+unwidth,
	positionType:'anchored', 
	anchoredClassName:boxclass, 
	anchoredSelector:object,
	positionTop:boxtop,
	positionLeft:boxleft,
	borderSize:1,
	loader:0,
	//windowBGColor:"#F3B06C",
	windowBGColor:"lightgray",
	windowPadding:10
	}
    );

  $(boxref).css("font-size","12px");
  $(boxref).html(explanation);

  if(action=='double'){
    $(this).
      dblclick(function(){$.closeDOMWindow({anchoredClassName:boxclass});});
  }else{
    $(this).
      mouseout(function(){$.closeDOMWindow({anchoredClassName:boxclass});});
  }
    
}

function toggleElement(elementid)
{
  $("#"+elementid).toggle('fast',null);
}

function loadRun(page,input)
{
  rid=$('#'+input).attr("value");
  options="CONFIGFILE=runs/run-"+rid+"/config-"+rid+".py";
  window.location.href=page+options;
}

function loadPreset(page,input)
{
  rid=$('#'+input).attr("value");
  options="?CONFIGFILE=pres/"+rid;
  window.location.href=page+options;
}

function getDateNow()
{
    var now=new Date();
    date=
	now.getDate()+"-"+now.getMonth()+"-"+now.getFullYear()+"-"+
	now.getHours()+"-"+now.getMinutes()+"-"+now.getSeconds();
    return date;
}

