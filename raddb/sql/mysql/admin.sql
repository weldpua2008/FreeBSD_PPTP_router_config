# -*- text -*-
##
## admin.sql -- MySQL commands for creating the RADIUS user.
##
##	WARNING: You should change '%' and 'radpass'
##		 to something else.  Also update raddb/sql.conf
##		 with the new RADIUS password.
##
##	$Id$

#
#  Create default administrator for RADIUS
#
CREATE USER 'radius'@'%';
SET PASSWORD FOR 'radius'@'%' = PASSWORD('KsakJU7632HGgasgh');

# The server can read any table in SQL
GRANT SELECT ON radius.* TO 'radius'@'%';

# The server can write to the accounting and post-auth logging table.
#
#  i.e. 
GRANT ALL on radius.radacct TO 'radius'@'%';
GRANT ALL on radius.radpostauth TO 'radius'@'%';
