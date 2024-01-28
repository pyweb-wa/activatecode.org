#!/bin/bash
CONNECTION_IDS=$(mysql -uroot -N -e "SELECT ID FROM information_schema.processlist WHERE USER = 'mixsimverify';")

# Kill each connection
#for ID in $CONNECTION_IDS; do
#    mysql -uroot -e "KILL $ID;"
#done

echo "$CONNECTION_IDS" | while IFS= read -r ID; do
 #echo "Attempting to kill process with ID: $ID"
 if ! mysql -uroot -e "KILL $ID;" > /dev/null 2>&1; then
     echo "Failed to kill process with ID: $ID" >&2
 else
     echo "Successfully killed process with ID: $ID"
 fi
done
