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
FIGDPI=600.0
FACDPI=(FIGDPI/600.0)

#Title
#Leave empty to remove
TITLE="Well known planets"

#Subtitle
#Leave empty to remove
SUBTITLE="Comprehensive Exoplanetary Radial Chart"

#Watermark
#Leave empty to remove
WATERMARK="Zuluaga (2013), http://astronomia.udea.edu.co/research/CERC"

#Scale of the figure (in chart radius)
FIGSCALE=1.8

#Inner radius of the color scales (in chart radius)
ARCSCALE=1.2

#Size of the figure
FIGWIDTH=int(24*FIGDPI/600)

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#DATABASE
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Catalogue
CATFILE="open_exoplanet_catalogue.csv"

#Fields of the catalogue
CATFIELDS=[('ID',str,'Primary identifier of planet'),('BF',int,'Binary flag (0-3)'),('Mp',float,'Planetary mass (Jupiter masses)'),('Rp',float,'Radius (Jupiter radii)'),('P',float,'Period (days)'),('a',float,'Semi-major axis (Astronomical Units)'),('e',float,'Eccentricity'),('q',float,'Periastron (degree)'),('L',float,'Longitude (degree)'),('W',float,'Ascending node (degree)'),('i',float,'Inclination (degree)'),('Teq',float,'Surface or equilibrium temperature (K)'),('taup',float,'Age (Gyr)'),('method',str,'Discovery method'),('year',int,'Discovery year (yyyy)'),('update',str,'Last updated (yy/mm/dd)'),('ra',SEX,'Right ascension (hh mm ss)'),('dec',SEX,'Declination (+/-dd mm ss)'),('d',float,'Distance from Sun (parsec)'),('Ms',float,'Host star mass (Solar masses)'),('Rs',float,'Host star radius (Solar radii)'),('Z',float,'Host star metallicity (log relative to solar)'),('Ts',float,'Host star temperature (K)'),('taus',float,'Host star age (Gyr)'),]

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#ARC SCALES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
ARCS=[('planet.Mp','Mp','$M_p$ ($M_J$)',cm.cool,'point:o:5:k;None:o:5:k','log',[0.001,0.002,0.004,0.006,0.008,0.01,0.02,0.04,0.06,0.08,0.1,0.2,0.4,0.6,0.8,1.0,2.0,4.0,6.0,8.0,10.0]),('planet.a','a','$a$ (AU)',cm.jet,'point:o:5:k;None:o:5:k','log',[0.01,0.02,0.04,0.06,0.08,0.1,0.2,0.4,0.6,0.8,1.0,2.0,4.0,6.0,8.0,10.0,20.0,40.0,60.0,80.0,100.0]),('planet.e','e','$e$',cm.hot,'point:o:5:k;None:o:5:k','linear',[0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0]),]

#Inter Arc distance
INTERARC=2

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
CATREQ=['Teq','Rp','Mp','a','e',]

#Filters
QUERY="planet.Rp<10"

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#LINES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Width of the lines
LW=1.0*FIGDPI/600

#Width of the radial lines
RADIALWIDTH=0.2

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#FONTS
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
TICKFONT=14
NAMEFONT=8
TITLEFONT=54
WATERFONT=24
SUBFONT=42

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#EQUILIBRIUM TEMPERATURES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include Temperature Scale
QTEMPERATURE=True

#Pallete of the equilibrium temperature
TPALETTE=cm.spectral

#Width of the temperature scale
TWIDTH=45

#Habitable zone limits
THAB=250.0
TOUT=300.0

#Limits of equilibrium temperatures
TCMINA=100.0
TCMAXA=2500.0

#Temperature ticks
TTICKS=[200,350,400,500,600,700,800,900,1000,2000,3000]+[THAB,(THAB+TOUT)/2,TOUT]

#HZ Gap
TSTEP=0.5

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#RADIAL SCALE
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include radial slice
QSLICE=True

#Radial ticks
RTICKS=[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,1.0,1.2,1.4,1.6,1.8,2.0]

#Radial slice
RADIALSLICE=20

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#PLANETARY NAMES
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#Include planetary names
QNAMES=True

#Offset Arc Name
OFFNAME=10

#Alternate name position
QALTERNATE=False

#Tips
QNAMETIPS=False

#Width of the radial lines
RADIALNAMEWIDTH=0.5

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#ADDITIONAL
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
TIMEFLAG="3-5-2013-22-24-40";