#!/bin/bash
BACKUPDIR=/var/backups/
python /usr/local/bin/generate_dump_db_piwik.py | bash > $BACKUPDIR/dump_db_piwik.sql
