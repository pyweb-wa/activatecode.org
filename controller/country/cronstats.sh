#!/bin/bash

while true; do
    # Run your PHP script here
    php cronstats3.php

    # Check the exit status of the PHP script
    if [ $? -eq 0 ]; then
        echo "PHP script exited successfully."
    else
        echo "PHP script exited with an error. Retrying..."
    fi

    # Optional: Add a delay before restarting the script
    sleep 1
done
