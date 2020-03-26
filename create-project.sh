#!/bin/bash

echo "-------------------------------------------"
echo "| Thanks for choosing Fryske-Oranjekoeke! |"
echo "-------------------------------------------"
echo "> Let's get started, what is the name of the Project?"
read projectname

echo "> Creating project..."

# Create a Project Folder.
mkdir $projectname $projectname/app
cd $projectname/app

# Get Fryske-Oranjekoeke
mkdir vendor vendor/sandert2001
cd vendor/sandert2001
git clone https://github.com/SanderT2001/fryske-oranjekoeke.git &> /dev/null

# Go back to the Project App Root.
cd ../../

# Setup the Skeleton
cp -R ./vendor/sandert2001/fryske-oranjekoeke/app-skeleton/* ./

# Update config.ini
sed -i 's/"App Name"/"'$projectname'"/g' ./config/config.ini
sed -i 's/"Sander Tuinstra"/"'$USER'"/g' ./config/config.ini

echo "> Done! Have fun!"
