WWWUSER=www-data
WWWGROUP=www-data
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
