WWWUSER=$(shell grep www /etc/passwd | head -n 1 | cut -f 1 -d ':')
WWWGROUP=$(shell grep www /etc/group | head -n 1 | cut -f 1 -d ':')
WWWDIRS=tmp runs pres

clean:
	@echo "Cleaning..."
	@rm -rf *~ */*~
	@rm -rf *.pyc

deps:
	@echo "Checking python dependencies..."
	@python -c "from util import *"

perms:	
	chown -R :$(WWWGROUP) $(WWWDIRS)
	chmod -R g+w $(WWWDIRS) 
