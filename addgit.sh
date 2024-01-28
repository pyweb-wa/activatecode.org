#!/bin/bash

read -p "Enter commit title: "  commit
echo $commit
if [ -z "$commit" ]
then
echo "you did'nt enter any commit"
exit
else

git add .
git commit -m "$commit"
git push origin main

fi
