#!/bin/bash
#mysql -uroot  -e "SHOW STATUS LIKE 'wsrep%';"
mysql -uroot  -e "SHOW STATUS LIKE 'Threads%';"
bash nginx_status.sh
