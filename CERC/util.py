#!/usr/bin/env python
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
#
################################################################################
# UTIL FILE
################################################################################

#############################################################
#PYTHON DEPENDENCIES
#############################################################
import os
from numpy import *
from matplotlib import use
use('Agg')
from matplotlib import pyplot as plt,patches as pat,cm as cm
from sys import exit,argv

#############################################################
#GLOBAL CONFIGURATIONS
#############################################################
DATADIR="data/"
CHARTDIR="charts/"

#############################################################
#ROUTINES
#############################################################
class dict2obj(object):
    """
    Convert dictionary to object
    """
    def __init__(self,dic={}):self.__dict__.update(dic)
    def __add__(self,other):
        for attr in other.__dict__.keys():
            exec("self.%s=other.%s"%(attr,attr))
        return self

def linear(value):
    return value

def SEX(angle):
    parts=angle.split()
    return parts[0]+parts[1]*60+parts[2]*60**2

def radLine(ax,r1,r2,angle,
            tipini=None,tipinimarker='o',tipinisize=5,tipinicolor='k',
            tipend=None,tipendmarker='o',tipendsize=5,tipendcolor='k',
            fmap=None,fcom="",fpre="",ftit="test",finfo="",flink="",fw=0,fs=1,fda=0,
            **args):
    """
    Draw a radial line in the circular chart
    """
    xa=array([r1,r2])*sin(angle*pi/180)
    ya=array([r1,r2])*cos(angle*pi/180)
    ax.plot(xa,ya,**args)

    if tipini=='point':
        ax.plot([xa[0]],[ya[0]],
                color=tipinicolor,
                marker=tipinimarker,markersize=tipinisize)
    if tipend=='point':
        ax.plot([xa[1]],[ya[1]],
                color=tipendcolor,
                marker=tipendmarker,markersize=tipendsize)
    try:
        fmap.write("\n")

        x1=array([r1,r2])*sin((angle-fda/2)*pi/180)
        y1=array([r1,r2])*cos((angle-fda/2)*pi/180)
        x2=array([r1,r2])*sin((angle+fda/2)*pi/180)
        y2=array([r1,r2])*cos((angle+fda/2)*pi/180)

        x1=(x1+fs)/(2*fs)*fw;
        y1=(1-(y1+fs)/(2*fs))*fw;
        x2=(x2+fs)/(2*fs)*fw;
        y2=(1-(y2+fs)/(2*fs))*fw;

        if finfo=="":finfo=ftit
        fmap.write("""<area alt="%s" title="%s" shape="poly" coords="%f,%f,%f,%f,%f,%f,%f,%f" href="%s" target="_blank"><info style="diplay:none">%s%s</info></area>\n"""%(ftit,ftit,x1[0],y1[0],x1[1],y1[1],x2[0],y2[0],x2[1],y2[1],flink,fpre,finfo))
    except:
        pass

def radLine2(ax,r1,r2,angle,
             tipini="None",
             tipend="None",
             **args):
    """
    Draw a radial line in the circular chart
    """
    xa=array([r1,r2])*sin(angle*pi/180)
    ya=array([r1,r2])*cos(angle*pi/180)
    ax.plot(xa,ya,**args)

    if 'point' in tipini:
        tiprops=tipini.split(":")

        try:tipinimarker=tiprops[1]
        except:tipinimarker='o'

        try:tipinisize=int(tiprops[2])
        except:tipinisize=5

        try:tipinicolor=tiprops[3]
        except:tipinicolor='k'

        if tipinimarker=="":tipinimarker='o'
        if tipinisize=="":tipinisize=5
        if tipinicolor=="":tipinicolor='k'

        ax.plot([xa[0]],[ya[0]],
                color=tipinicolor,
                marker=tipinimarker,markersize=tipinisize)

    if 'point' in tipend:
        tiprops=tipend.split(":")

        try:tipendmarker=tiprops[1]
        except:tipendmarker='o'

        try:tipendsize=int(tiprops[2])
        except:tipendsize=5

        try:tipendcolor=tiprops[3]
        except:tipendcolor='k'

        if tipendmarker=="":tipendmarker='o'
        if tipendsize=="":tipendsize=5
        if tipendcolor=="":tipendcolor='k'

        ax.plot([xa[1]],[ya[1]],
                color=tipendcolor,
                marker=tipendmarker,markersize=tipendsize)

def radText(ax,r,angle,text,
            fmap=None,fcom="",ftit="test",flink="",fw=0,fs=1,fr=0,
            **args):
    """
    Draw a text aligned in radial direction
    """
    xa=r*sin(angle*pi/180)
    ya=r*cos(angle*pi/180)
    complement=90
    if angle>180:complement=270
    if angle<0:complement=-90
    rotatetext=complement-angle
    ax.text(xa,ya,text,
            rotation=rotatetext,
            **args)

    #MAP INFORMATION
    try:
        fmap.write("\n")
        xc=(xa+fs)/(2*fs)*fw
        yc=(1-(ya+fs)/(2*fs))*fw
        r=fr
        fmap.write("""<area alt="%s" title="%s" shape="circle" coords="%f,%f,%f" href="%s" target="_blank"><info style="display:none">%s</info></area>\n"""%(ftit,ftit,xc,yc,r,flink,ftit))
    except:
        pass

def radText1(ax,r,angle,text,**args):
    """
    Draw a text aligned in radial direction
    """
    xa=r*sin(angle*pi/180)
    ya=r*cos(angle*pi/180)

    ha='left';va='center'

    if angle<90:
        va='bottom'
        rotatetext=90-angle
    elif angle<100:
        va='center'
        rotatetext=90-angle
    elif angle<180:
        va='top'
        rotatetext=90-angle
    elif angle<270:
        va='top'
        ha='right'
        rotatetext=270-angle
    else:
        va='bottom'
        ha='right'
        rotatetext=270-angle

    args['horizontalalignment']=ha
    args['verticalalignment']=va
    #ax.plot([xa],[ya],'ko',markersize=5)
    ax.text(xa,ya,text,
            rotation=rotatetext,
            **args)

def radText2(ax,r,angle,text,**args):
    """
    Draw a text aligned in radial direction
    """
    xa=r*sin(angle*pi/180)
    ya=r*cos(angle*pi/180)
    complement=90
    if angle>180:complement=270
    if angle<0:complement=-90
    ax.text(xa,ya,text,rotation=complement-angle,**args)
    #ax.plot([xa],[ya],'o')

def readCatalogueHeader(file,fid=0):
    """
    Read catalogue whose first line contain the fields
    """
    import csv
    csvfile=open(file,"rb")
    content=csv.reader(csvfile,delimiter=',')
    i=0
    keys=[]
    objects=dict()
    classes=dict()
    for row in content:
        if '#' in row[0]:continue
        if i==0:
            for field in row:
                keys+=[field.strip("#").strip()]
        else:
            objects[row[fid]]=dict()
            f=0
            for field in row:
                objects[row[fid]][keys[f]]=field
                f+=1
            classes[row[fid]]=dict2obj(objects[row[fid]])
        i+=1
    nobjs=i-1
    print "%d objects read from %s..."%(nobjs,file)
    return objects,classes

def readCatalogueFields(file,keyfields,fid=0):
    """
    Read catalogue given the fields
    """
    import csv

    csvfile=open(file,"rb")
    content=csv.reader(csvfile,delimiter=',')
    i=0
    objects=dict()
    classes=dict()
    keys=[]
    for fields in keyfields:keys+=[fields[0]]
    for row in content:
        if '#' in row[0]:continue
        objects[row[fid]]=dict()
        f=0
        for field in row:
            objects[row[fid]][keys[f]]=field
            f+=1
        classes[row[fid]]=dict2obj(objects[row[fid]])
        i+=1
    nobjs=i-1
    print "%d objects read from %s..."%(nobjs,file)
    return objects,classes

def purgeCatalogue(objects,fields):
    """
    Purge catalogue according to non-existing fields
    """
    selected=dict()
    n=0
    for key in objects.keys():
        obj=objects[key]
        qfail=False
        for field in fields:
            str=obj[field].strip("\n").strip("\r").strip()
            if str is "":
                qfail=True
                break
        if qfail:continue
        selected[key]=obj
        n+=1
    print "%d objects selected..."%n
    return selected

def filterCatalogue(objects,keyfields,query):
    condition="""
filter=(%s)
"""%query

    if query=="":condition="filter=True"
    
    #CONVERT TO NUMBER OBJECT FIELDS
    keys=[]
    for fields in keyfields:keys+=[fields[0]]
    objectsn=dict()
    for objkey in objects.keys():
        objectsn[objkey]=dict()
        f=0
        for key in keys:
            field=objects[objkey][key]
            type=keyfields[f][1]
            if field=='':
                if type==float or type==int:field='0'
                if type==SEX:field='0 0 0'
            objectsn[objkey][key]=type(field)
            f+=1

    #FILTER
    filtered=dict()
    for key in objectsn.keys():
        planet=dict2obj(objectsn[key])
        exec(condition)
        if filter:
            filtered[key]=planet

    print "%d objects filtered..."%(len(filtered.keys()))
    return filtered

def loadconf(filename):
    import os
    d=dict()
    conf=dict2obj()
    if os.path.lexists(filename):
        execfile(filename,{},d)
        conf+=dict2obj(d)
        qfile=True
    else:
        print "Configuration file '%s' does not found."%filename
        exit(0)
    return conf

def latex2HTML(text):
    import re as replace
    newtext=text
    newtext=newtext.replace("$","")
    newtext=replace.sub(r"_([^\s^\)^=]+)",r"<sub>\1</sub>",newtext)
    newtext=replace.sub(r"\^([^\s^\)^=]+)",r"<sup>\1</sup>",newtext)
    return newtext

def planetInfo(planet,arcs):
    props=""
    iarc=0
    url="http://exoplanet.eu/catalog/%s"%planet.ID
    for ARC in arcs:
        psymb=ARC[0]
        pname=latex2HTML(ARC[2]);
        exec("p=%s"%psymb)
        props+="%s = "%pname+str(p)
        props+="<br/>"
        iarc+=1
    planetinfo="""
<b>%s</b><br/>
<a href="%s" target="_blank">More</a>
<p></p>
R<sub>p</sub> = %.2f R<sub>Jup</sub><br/>
T<sub>eq</sub> = %.2f K<br/>
%s
"""%(planet.ID,url,planet.Rp,planet.Teq,props);
    return planetinfo

