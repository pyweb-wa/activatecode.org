#!/bin/bash


## Check github Folder
rsync -n -av  --itemize-changes --delete --exclude-from='file_to_exclude.txt' /var/www/smsmarket/html/  /root/github_code/activatecode.org


## Check Server1
echo -e "\e[93mOuput From Server1\e[0m \n\n"
rsync -n -av  --itemize-changes --delete --exclude-from='file_to_exclude.txt'  -e ssh /var/www/smsmarket/html/ root@192.168.100.1:/var/www/smsmarket/html/


## Check Main
echo -e "\e[93mOuput From Main\e[0m \n\n"
rsync -n -av  --itemize-changes --delete --exclude-from='file_to_exclude.txt'  -e ssh /var/www/smsmarket/html/ root@192.168.100.2:/var/www/smsmarket/html/


## Check Server3
echo -e "\e[93mOuput From Server3\e[0m \n\n"

rsync -n -av  --itemize-changes --delete --exclude-from='file_to_exclude.txt'  -e ssh /var/www/smsmarket/html/ root@192.168.100.21:/var/www/smsmarket/html/
