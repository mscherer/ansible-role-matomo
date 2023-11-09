#!/usr/bin/python
import ConfigParser
config = ConfigParser.SafeConfigParser()
config.read('/var/www/piwik/config/config.ini.php')
print "mysqldump -u %s -h %s -p%s %s" % ( config.get('database','username'), config.get('database','host'), config.get('database','password'), config.get('database','dbname'))
