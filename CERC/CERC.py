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
# Version: alpha 2.0 (see changeslog.txt)
# Last update: May 17, 2013
################################################################################
from util import *

#############################################################
#CONFIGURATION FILE
#############################################################
try:
    confile=argv[1]
    print "Loading provided file '%s'..."%confile
except:
    confile='config.py'
    print "Loading preconfigured file 'config.py'..."

conf=loadconf(confile)

#############################################################
#SCALE VARIABLES WITH FIGURE RESOLUTION
#############################################################
for var in ['TICKFONT','NAMEFONT','TITLEFONT','WATERFONT','SUBFONT']:
    exec("conf.%s*=conf.FACDPI"%(var))

#############################################################
#FIGURE SUFFIX
#############################################################
try:
    figsuf=argv[2]
    print "Figure suffix: '%s'..."%figsuf
except:
    figsuf="Diagram";

#############################################################
#MAP FILE
#############################################################
figwidth=conf.FIGWIDTH*100
mapfile=CHARTDIR+conf.FIGFILE+"-"+figsuf+".html"
fmap=open(mapfile,"w");
fmap.write("""
<html>
<img src="%s-%s.png" usemap="#help"></img>
<width_screen style="display:none">100%%</width_screen>
<width style="display:none">%d</width>
<height style="display:none">%d</height>
<map name='help'>
"""%(conf.FIGFILE,figsuf,figwidth,figwidth))

#############################################################
#CREATE FIGURE
#############################################################
plt.close("all")
fig=plt.figure(figsize=(conf.FIGWIDTH,conf.FIGWIDTH),dpi=conf.FIGDPI)
b=0;l=0;w=1;h=1
ax=fig.add_axes([l,b,w,h])

#############################################################
#PREPARE REGION
#############################################################
ax.set_frame_on(False)
ax.axes.get_xaxis().set_visible(False)
ax.set_xticks([]);ax.set_yticks([])

#############################################################
#LOAD DATA
#############################################################
allplanets,callplanets=readCatalogueFields(conf.DATADIR+conf.CATFILE,
                                           conf.CATFIELDS)

#############################################################
#PURGE AND FILTER DATABASE
#############################################################
planets=purgeCatalogue(allplanets,conf.CATREQ)
selplanets=filterCatalogue(planets,conf.CATFIELDS,conf.QUERY)
nplanets=len(selplanets.keys())
Rps=[]
IDs=[]
for id in selplanets.keys():
    IDs+=[id]
    Rps+=[selplanets[id].Rp]
Rpmax=max(Rps);Rpmin=min(Rps)

#############################################################
#EQUILIBRIUM TEMPERATURES SCALE
#############################################################
deltaT=log10(conf.TCMAXA/conf.THAB)-log10(conf.TCMINA/conf.THAB)
if conf.QTEMPERATURE:
    Ttheta1=-conf.TWIDTH
    Ttheta2=+conf.TWIDTH

    #==================================================
    #SCALE
    #==================================================
    thetas=[90-Ttheta1,90-Ttheta2]
    theta1=min(thetas)
    theta2=max(thetas)
    
    #==================================================
    #PALETTE
    #==================================================
    dT=0.0;qstepini=False;qstepend=False
    print conf.AR1/2,conf.AR2/2
    for T in logspace(log10(conf.TCMINA),log10(conf.TCMAXA),1000):
        Teq=log(T/conf.THAB)
        Tcolor=(log10(T/conf.THAB)-log10(conf.TCMINA/conf.THAB))/deltaT
        color=conf.TPALETTE(Tcolor)
        
        if T>conf.THAB and not qstepini:dT+=conf.TSTEP;qstepini=True
        if T>conf.TOUT and not qstepend:dT+=conf.TSTEP;qstepend=True
        theta=Ttheta1+(Ttheta2-Ttheta1)/deltaT*(log10(T/conf.THAB)-log10(conf.TCMINA/conf.THAB))+dT

        argline=dict(linewidth=conf.LW*1,color=color,
                     fmap=fmap,fcom="TSCALE",ftit="%.1f"%T,flink="JavaScript:void(null)",
                     fs=conf.FIGSCALE,fw=figwidth,fda=4*conf.TWIDTH/1000.0,
                     fpre="T<sub>eq</sub> = ")
        radLine(ax,conf.AR1/2,conf.AR2/2,theta,**argline)
        #print T,theta;break;
        #exit(0)

    #==================================================
    #TICKS
    #==================================================
    conf.TTICKS+=[conf.TCMINA,conf.TCMAXA]
    conf.TTICKS=array(conf.TTICKS)
    conf.TTICKS.sort()

    dT=0.0;qstepini=False;qstepend=False
    for T in conf.TTICKS:
        if T<conf.TCMINA or T>conf.TCMAXA:continue

        if T>conf.THAB and not qstepini:dT+=conf.TSTEP;qstepini=True
        if T>conf.TOUT and not qstepend:dT+=conf.TSTEP;qstepend=True
        theta=Ttheta1+(Ttheta2-Ttheta1)/deltaT*(log10(T/conf.THAB)-log10(conf.TCMINA/conf.THAB))+dT

        argline=dict(linewidth=conf.LW*1,color='k')
        radLine(ax,conf.AR1/2,conf.AR2/2,theta,**argline)

        #print T,theta;break;
        
    dT=0.0;qstepini=False;qstepend=False
    for T in conf.TTICKS:
        if T<conf.TCMINA or T>conf.TCMAXA:continue

        if T>conf.THAB and not qstepini:dT+=conf.TSTEP;qstepini=True
        if T>conf.TOUT and not qstepend:dT+=conf.TSTEP;qstepend=True
        theta=Ttheta1+(Ttheta2-Ttheta1)/deltaT*(log10(T/conf.THAB)-log10(conf.TCMINA/conf.THAB))+dT

        argtext=dict(fontsize=conf.TICKFONT,color='k',
                     horizontalalignment='center',verticalalignment='center')
        dtheta=1
        if theta<0:
            dtheta*=-1
        radText(ax,(conf.AR1+conf.AR2)/4.0,theta+dtheta,"%.0f"%T,
                **argtext)

    #==================================================
    #HZ LABEL
    #==================================================
    T=(conf.THAB+conf.TOUT)/2.0
    theta=Ttheta1+(Ttheta2-Ttheta1)/deltaT*(log10(T/conf.THAB)-log10(conf.TCMINA/conf.THAB))
    radText(ax,1.03*conf.AR2/2,theta+dtheta,"HZ",**argtext)

    #==================================================
    #LABELS
    #==================================================
    r=conf.AR2/2+1*conf.TICKH
    angletext=(Ttheta2+Ttheta1)/2
    ax.text(r*sin(angletext*pi/180),r*cos(angletext*pi/180),
            r"Equilibrium Temperature",fontsize=2*conf.TICKFONT,
            horizontalalignment='center')

    r=conf.AR2/2+0*conf.TICKH
    Ttheta2+=dT
    ax.text(r*sin(Ttheta1*pi/180),r*cos(Ttheta1*pi/180),
            r"$T^{\,\rm min}_{\rm eq}$",fontsize=2*conf.TICKFONT,
            rotation=270-Ttheta1,
            horizontalalignment='right',verticalalignment='bottom')
    ax.text(r*sin(Ttheta2*pi/180),r*cos(Ttheta2*pi/180),
            r"$T^{\,\rm max}_{\rm eq}$",fontsize=2*conf.TICKFONT,
            rotation=90-Ttheta2,
            horizontalalignment='left',verticalalignment='bottom')

#############################################################
#ARC SCALES
#############################################################
#Number of scales
narcs=max([len(conf.ARCS),1])
darc=(180-2*conf.OFFNAME-(narcs-1)*conf.INTERARC)/narcs
thini=90+conf.OFFNAME

iarc=0

pmin=dict()
pmax=dict()
deltap=dict()
pconv=dict()
for ARC in conf.ARCS:
    #==================================================
    #Limits of the arc in plot coordinates
    #==================================================
    psymb=ARC[0]
    ptex=ARC[1]
    pname=ARC[2]
    palette=ARC[3]
    pscale=ARC[5]
    scalespace=linspace
    pconv[psymb]=linear

    if pscale=='log':
        scalespace=logspace
        pconv[psymb]=log10

    print "Arc for %s..."%pname

    ptheta1=thini+iarc*darc+iarc*conf.INTERARC
    ptheta2=ptheta1+darc

    #==================================================
    #Limits of the arc in plot coordinates
    #==================================================
    thetas=[90-ptheta1,90-ptheta2]
    theta1=min(thetas)
    theta2=max(thetas)

    print "\tAngular span: ",ptheta1,ptheta2

    #==================================================
    #EXTREMES
    #==================================================
    pvec=[]
    for id in selplanets.keys():
        planet=selplanets[id]
        cmd=("pvec+=[%s]"%psymb)
        exec(cmd)
    pmin[psymb]=min(pvec)
    pmax[psymb]=max(pvec)
    deltap[psymb]=pconv[psymb](pmax[psymb])-pconv[psymb](pmin[psymb])

    print "\tProperty extremes: ",pmin[psymb],pmax[psymb]

    #==================================================
    #TICKS
    #==================================================
    pticks=ARC[6]+[pmin[psymb],pmax[psymb]]
    #print "\tProperty ticks: ",pticks

    for p in pticks:
        #p=0.12
        if p<pmin[psymb] or p>pmax[psymb]:continue
        dp=pconv[psymb](p)-pconv[psymb](pmin[psymb])
        ptheta=ptheta1+(ptheta2-ptheta1)/deltap[psymb]*dp
        argline=dict(linewidth=1*conf.LW,color='k')
        radLine(ax,conf.AR2/2,conf.AR2/2+conf.TICKH,ptheta,**argline)
        argtext=dict(fontsize=conf.TICKFONT,color='k')
        radText1(ax,conf.AR2/2+1.5*conf.TICKH,ptheta,"%.3f"%p,**argtext)

    #==================================================
    #PALETTE
    #==================================================
    for p in scalespace(pconv[psymb](pmin[psymb]),pconv[psymb](pmax[psymb]),1000):
        dp=pconv[psymb](p)-pconv[psymb](pmin[psymb])
        pcolor=1-dp/deltap[psymb]
        ptheta=ptheta1+(ptheta2-ptheta1)/deltap[psymb]*dp
        color=palette(pcolor)
        argline=dict(linewidth=3*conf.LW,color=color,
                     fmap=fmap,fcom="%s ARC"%psymb,
                     ftit="%.3f"%p,flink="JavaScript:void(null)",
                     fs=conf.FIGSCALE,fw=figwidth,fda=4*(ptheta2-ptheta1)/1000.0,
                     fpre=latex2HTML(pname)+" = "
                     )
        radLine(ax,conf.AR1/2,conf.AR2/2,ptheta,**argline)

    #==================================================
    #OPTIONS OF THE ARC BORDER
    #==================================================
    argarc=dict(edgecolor='k',linewidth=conf.SCALEWIDTH*conf.LW,angle=0,
                theta1=theta1,theta2=theta2,
                fill=False)

    #==================================================
    #DRAW BORDER OF ARC
    #==================================================
    ax.add_patch(pat.Arc((0,0),conf.AR1,conf.AR1,**argarc))
    ax.add_patch(pat.Arc((0,0),conf.AR2,conf.AR2,**argarc))

    argline=dict(linewidth=conf.TICKWIDTH*conf.LW,color='k')
    radLine(ax,conf.AR1/2,conf.AR2/2,ptheta1,**argline)
    radLine(ax,conf.AR1/2,conf.AR2/2,ptheta2,**argline)

    #==================================================
    #LABELS
    #==================================================
    r=conf.AR2/2+5*conf.TICKH
    angletext=(ptheta2+ptheta1)/2
    print "\tAngle text:",angletext

    if angletext<180:
        halign="left"
    else:
        halign="right"
    halign='center';valign='center'
    rotatetext=180-angletext
    ax.text(r*sin(angletext*pi/180),r*cos(angletext*pi/180),
            r"%s"%pname,fontsize=2*conf.TICKFONT,rotation=rotatetext,
            horizontalalignment=halign,
            verticalalignment=valign
            )

    iarc+=1

#############################################################
#MAIN CHART
#############################################################
print "Planet lines..."
angleslice=conf.RADIALSLICE
widthinfo=360-2*(90-conf.OFFNAME)-10
thetaini=90-widthinfo/2
dtheta=1.0*(widthinfo-angleslice)/(nplanets-1)

j=0
qslice=0
for i in argsort(Rps)[::-1]:
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #PLANET
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    ID=IDs[i]
    Rp=Rps[i]
    planet=selplanets[ID]

    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #TEMPERATURE
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    Teq=log10(planet.Teq/conf.THAB)
    Tcolor=(log10(planet.Teq/conf.THAB)-log10(conf.TCMINA/conf.THAB))/deltaT
    color=conf.TPALETTE(Tcolor)

    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #CIRCLE
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    rp=Rp/Rpmax
    ax.add_patch(pat.Circle((0,0),
                            radius=rp,
                            facecolor=color,edgecolor='k',linewidth=conf.LW*0.0))

    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #NAMES
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    if conf.QALTERNATE:
        if j%2==0:
            rID=1.01+0.07
        else:
            rID=1.01
    else:rID=1.01
    
    theta=thetaini+j*dtheta
    if theta>(90-angleslice/2):qslice=1
    theta+=qslice*angleslice
    if theta<90:
        hal='right';val='bottom'
    else:
        hal='left';val='bottom'

    if conf.QNAMES:
        flink="http://exoplanet.eu/catalog/%s"%planet.ID
       
        argline=dict(linewidth=conf.LW*conf.RADIALNAMEWIDTH,tipinimarker='o')
        if conf.QNAMETIPS:
            argline['tipini']='point';
        radLine(ax,rp,rID,theta-90,**argline)
        
        planetinfo=planetInfo(planet,conf.ARCS)
        argline=dict(linewidth=0,
                     fmap=fmap,ftit="%s"%planet.ID,flink=flink,
                     finfo="%s"%planetinfo,
                     fs=conf.FIGSCALE,fw=figwidth,fda=dtheta,
                     )
        radLine(ax,rID,conf.AR2/2,theta-90,**argline)

        nfont=int(max([min([(200.0/nplanets)*conf.NAMEFONT,10*conf.LW]),6*conf.LW]))
        argtext=dict(fontsize=nfont,color='k',
                     horizontalalignment=hal,verticalalignment=val)
        radText(ax,rID,theta-90,"%s"%ID,**argtext)

    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #PROPERTY LINES
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    iarc=0
    for ARC in conf.ARCS:
        psymb=ARC[0]
        exec("p=%s"%psymb)
        dp=pconv[psymb](p)-pconv[psymb](pmin[psymb])

        ptheta1=thini+iarc*darc+iarc*conf.INTERARC
        ptheta2=ptheta1+darc
        ptheta=ptheta1+(ptheta2-ptheta1)/deltap[psymb]*dp

        argline=dict(linewidth=conf.LW*conf.RADIALWIDTH,tipini='point')
        argline['tipini'],argline['tipend']=ARC[4].split(';');
        radLine2(ax,rp,conf.AR2/2,ptheta,
                 **argline)
        iarc+=1

    j+=1

for i in argsort(Rps):
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #PLANET
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    ID=IDs[i]
    Rp=Rps[i]
    rp=Rp/Rpmax
    planet=selplanets[ID]
    
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    #PLANET INFO
    #%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    xc=yc=figwidth/2.0;
    r=rp*(figwidth/(2*conf.FIGSCALE))
    url="http://exoplanet.eu/catalog/%s"%planet.ID

    planetinfo=planetInfo(planet,conf.ARCS)
    fmap.write("\n")
    fmap.write("""<area alt="%s" title="%s" shape="circle" coords="%f,%f,%f" href="%s" target="_blank">
<info style="display:none">%s</info>
</area>\n"""%(planet.ID,planet.ID,xc,yc,r,url,planetinfo))

#############################################################
#RADIAL SLICE
#############################################################
if conf.QSLICE:
    thetaslice=90
    colorslice='w'
    
    rsl=+1.1
    xsl=-rsl*tan(angleslice/2*pi/180)
    ysl=rsl
    
    ax.add_patch(pat.Polygon([[0,0],[xsl,ysl],[-xsl,ysl]],
                             closed=True,fill=True,
                             facecolor=colorslice,edgecolor='None'))
    
    colorticks='k'
    for r in conf.RTICKS:
        if r>Rpmax or r<Rpmin:continue
        ax.add_patch(pat.Arc((0,0),2*r/Rpmax,2*r/Rpmax,
                             theta1=-angleslice/2,theta2=+angleslice/2,
                             angle=thetaslice,
                             color=colorticks))
        
        rtext=(r/Rpmax+0.01)
        xtext=rtext*cos((thetaslice-angleslice/2)*pi/180)
        ytext=rtext*sin((thetaslice+angleslice/2)*pi/180)
        ax.text(xtext,ytext,"%.2f"%r,fontsize=conf.NAMEFONT,
                horizontalalignment='right',verticalalignment='bottom',
                rotation=-angleslice/2)
    
    color='None'
    ax.add_patch(pat.Circle((0,0),
                            radius=1.0,
                            facecolor=color,edgecolor='k',linewidth=conf.LW*1))
    rtext=(1.01)
    xtext=rtext*cos((thetaslice-angleslice/2)*pi/180)
    ytext=rtext*sin((thetaslice+angleslice/2)*pi/180)
    ax.text(xtext,ytext,r"%.2f $R_{\rm J}$"%Rpmax,fontsize=conf.NAMEFONT,
            horizontalalignment='right',verticalalignment='bottom',
            rotation=-angleslice/2)
    
    
    solarsystem=dict(Earth=[0.0893,'g'],Neptune=[0.3571,'b'],
                     Saturn=[0.848,'m'],Jupiter=[1.0,'r'])
    for planet in solarsystem.keys():
        Rp=solarsystem[planet][0]
        color=solarsystem[planet][1]
        if Rp>Rpmax:continue
        ax.add_patch(pat.Arc((0,0),2*Rp/Rpmax,2*Rp/Rpmax,
                             theta1=-angleslice/2,theta2=+angleslice/2,
                             angle=thetaslice,
                             color=color,
                             linewidth=conf.LW*3))
        angletxt=0.0
        rtext=(Rp/Rpmax+0.01)
        xtext=rtext*cos((thetaslice+angletxt/2)*pi/180)
        ytext=rtext*sin((thetaslice+angletxt/2)*pi/180)
        ax.text(xtext,ytext,
                "%s"%planet,fontsize=conf.NAMEFONT,color=color,
                horizontalalignment='center',verticalalignment='center',
                rotation=angletxt/2)

#############################################################
#MARKINGS
#############################################################
if conf.TITLE!="":
    ax.set_title("%d %s"%(nplanets,conf.TITLE),
                 fontsize=conf.TITLEFONT,position=(0.5,0.95))

if conf.SUBTITLE!="":
    ax.text(0.5,0.94,conf.SUBTITLE,
            horizontalalignment='center',verticalalignment='top',
            transform=ax.transAxes,fontsize=conf.SUBFONT)

if conf.WATERMARK!="":
    ax.text(0.5,0.04,conf.WATERMARK,
            horizontalalignment='center',verticalalignment='top',
            transform=ax.transAxes,fontsize=conf.WATERFONT)

#############################################################
#CLOSE MAP FILE
#############################################################
fmap.write("""
</map>
</html>
""")
fmap.close()

#############################################################
#SAVE FIGURES
#############################################################
ax.set_xlim((-conf.FIGSCALE,conf.FIGSCALE))
ax.set_ylim((-conf.FIGSCALE,conf.FIGSCALE))
for format in 'pdf','png':
    figfile=CHARTDIR+conf.FIGFILE+"-"+figsuf+"."+format
    print "Figure '%s' saved..."%figfile
    plt.savefig(figfile)
