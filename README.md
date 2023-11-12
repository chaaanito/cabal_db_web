# PNM stack for Cabal Online Private Server

-  PHP Version 8.1.0
-  NGINX Version 1.21.4
-  MSSQL Version 2019

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Freebie](#Freebie)

## Overview

Installation for PHP  -  NGINX  - MSSQL Stack for Cabal Website & Database using Docker.

## Installation
***DOCKER MUST BE INSTALLED***
1. Clone the repository:
2. CD to the Repository folder name.
3. Copy .env.example to .env and configure .env & change database with a strong password.
``````
-  cp .env.example .env
-  nano .env
``````

4. Compose Containers and restore Cabal Databases Automatically.
``````
-  docker-compose up -d
-  docker-compose exec sqlserver sh restoredb.sh
``````
## Freebie
Your files are mounted in WWW folder so have fun.

-->> ./www/:/var/www/html

Test if no Errors after build.
http://yourmachineip:8080

##CONFIGS

-Edit all files that are name config for Database connection.
-Webshop can be updated to CSV file. check Webshop.php and find $userID condition to upload.
