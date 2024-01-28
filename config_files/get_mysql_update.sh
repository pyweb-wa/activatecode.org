#!/bin/bash
# MySQL database credentials
DB_USER="phpmyadmin2"
DB_PASSWORD="P@tchP@1234"
DB_NAME="smsdb"
HOST="192.168.100.20"
# Backup directory
BACKUP_DIR="/root/github_code/activatecode.org/mysql_backup/"

# Date format for backup file
DATE_FORMAT="%Y-%m-%d"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Generate the backup file name with the current date
BACKUP_FILE="$BACKUP_DIR/backup_$(date +$DATE_FORMAT).sql"

#mysql
mysql -uroot -e "set global pxc_strict_mode=PERMISSIVE;"
# Perform the backup
mysqldump -u $DB_USER -p$DB_PASSWORD -h $HOST --no-data --routines smsdb > $BACKUP_FILE
mysqldump -u $DB_USER -p$DB_PASSWORD -h $HOST --no-create-info --where="1=0" smsdb application_code countryList finance_accounts foreignapi foreignapiservice payment_gateway >> $BACKUP_FILE

mysql -uroot -e "set global pxc_strict_mode=ENFORCING;"

# Compress the backup file using gzip
gzip $BACKUP_FILE

# Remove the original SQL backup file
#rm $BACKUP_FILE

# Delete backups older than 10 days
#find $BACKUP_DIR -name "backup_*" -mtime +10 -exec rm {} \;
