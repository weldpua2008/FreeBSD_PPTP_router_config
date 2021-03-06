# -*- text -*-
##
## admin.sql -- PostgreSQL commands for creating the RADIUS user.
##
##	WARNING: You should change 'localhost' and 'radpass'
##		 to something else.  Also update raddb/sql.conf
##		 with the new RADIUS password.
##
##	WARNING: This example file is untested.  Use at your own risk.
##		 Please send any bug fixes to the mailing list.
##
##	$Id$

#
#  Create default administrator for RADIUS
#
CREATE USER 'radius'@'localhost' WITH PASSWORD 'radpass';

# The server can read any table in SQL
GRANT SELECT ON radcheck TO 'radius'@'localhost';
GRANT SELECT ON radreply TO 'radius'@'localhost';
GRANT SELECT ON radgroupcheck TO 'radius'@'localhost';
GRANT SELECT ON radgroupreply TO 'radius'@'localhost';
GRANT SELECT ON radusergroup TO 'radius'@'localhost';

# The server can write to the accounting and post-auth logging table.
GRANT ALL on radius.radacct TO 'radius'@'localhost';
GRANT ALL on radius.radpostauth TO 'radius'@'localhost';
