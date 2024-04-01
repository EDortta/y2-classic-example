# The classic YeAPF2/web example

This is a classic example in the sense that the data is sent to the server, processed and returned to the client with redirection capabilities.

It is a good starting point for those searching a well known space while researching what can be done differently in YeAPF2

### What do you need?

1. If you're using Windows, install `github bash` or any other bash of your preference. This is not mandatory, but a free suggestion (Free as in "no cost" and in "freedom")
2. `git` for pull the project. But you can download the `.zip` version
3. `composer` for pull and update the php components
4. `docker` to run the containers
5. `vscodium` or other editor of your choice.

It's outside the scope of this README to explain how to install and keep update those tools.

## How to install and use (Short version)
1. clone this project in any folder. The next lines goes to your main folder, create a folder `Projects`, enter into it, and finally clone the project.

   ```bash
   cd ~/
   mkdir -p Projects
   cd Projects
   git clone https://github.com/EDortta/y2-classic-example.git
   ```
2. run `build.sh` to grant the folders, check PHP version, grant composer and build the containers.
   
   ```bash
   cd ~/Projects/y2-classic-example
   ./build.sh
   ```
3. open your navigator in `http://localhost:8183`. If you already have a project running on that port, change it in `docker-compose.yml`

## How to install and use (Long version)

1. clone this project in any folder. The next lines goes to your main folder, create a folder `Projects`, enter into it, and finally clone the project.

   ```bash
   cd ~/
   mkdir -p Projects
   cd Projects
   git clone https://github.com/EDortta/y2-classic-example.git
   ```

   It will show to you something like this:

   ```bash
   Cloning into 'y2-classic-example'...
   remote: Enumerating objects: 29, done.
   remote: Counting objects: 100% (29/29), done.
   remote: Compressing objects: 100% (23/23), done.
   remote: Total 29 (delta 4), reused 29 (delta 4), pack-reused 0
   Receiving objects: 100% (29/29), 36.36 KiB | 16.00 KiB/s, done.
   Resolving deltas: 100% (4/4), done.
   ```
2. use composer to install/update the php components you'll need

   ```bash
   cd ~/Projects/y2-classic-example/web
   composer update
   ```

   It will show something like this:

   ```bash
   Loading composer repositories with package information
   Updating dependencies
   Lock file operations: 2 installs, 0 updates, 0 removals
     - Locking edortta/yeapf2 (v2.0.13)
     - Locking firebase/php-jwt (v6.9.0)
   Writing lock file
   Installing dependencies from lock file (including require-dev)
   Package operations: 2 installs, 0 updates, 0 removals
     - Downloading edortta/yeapf2 (v2.0.13)
     - Installing edortta/yeapf2 (v2.0.13): Extracting archive
     - Installing firebase/php-jwt (v6.9.0): Extracting archive
   1 package suggestions were added by new dependencies, use `composer suggest` to see details.
   Generating autoload files
   No security vulnerability advisories found.
   
   ```
3. build the docker containers using the recipe. Pay attention to change the `docker-compose.yml` if your local port 8183 is used by another application. Pay attention that if you don't have the `docker-compose` installed you will need to change it by `docker compose` (without the dash between the command `docker` and the parameter `compose`).

   ```bash
   cd ~/Projects/y2-classic-example
   docker-compose build
   ```

   The output is more or less like this:

   ```bash
   Building php-fpm
   Sending build context to Docker daemon  4.096kB
   Step 1/12 : FROM php:8.2-fpm-alpine
   ...
   Successfully built a94dcdc61f88
   Successfully tagged y2-classic-example_nginx:latest
   ```
4. grant the existence of some folders and files

   ```bash
   cd ~/Projects/y2-classic-example
   mkdir -p logs/nginx
   touch logs/nginx/error.log
   touch logs/nginx/access.log
   ```

   Maybe you would need to adjust folder rights of `web/assets/jwt-bin` or `web/logs` as these folders are needed for correct operation. In such case, you can proceed like this:
   ```bash
   cd ~/Projects/y2-classic-example/web
   mkdir logs
   mkdir -p assets/jwt-bin
   sudo chmod -R 0777 logs assets
   ```
   
5. let the containers become alive

   ```bash
   cd ~/Projects/y2-classic-example
   docker-compose up
   ```

   The output is approximately as this:

   ```bash
   Starting y2-classic-example_php-fpm_1 ... done
   Starting y2-classic-example_nginx_1   ... done
   Attaching to y2-classic-example_php-fpm_1, y2-classic-example_nginx_1
   php-fpm_1  | [23-Nov-2023 13:04:52] NOTICE: fpm is running, pid 1
   php-fpm_1  | [23-Nov-2023 13:04:52] NOTICE: ready to handle connections
   nginx_1    | /docker-entrypoint.sh: /docker-entrypoint.d/ is not empty, will attempt to perform configuration
   ...
   nginx_1    | /docker-entrypoint.sh: Launching /docker-entrypoint.d/20-envsubst-on-templates.sh
   nginx_1    | /docker-entrypoint.sh: Launching /docker-entrypoint.d/30-tune-worker-processes.sh
   nginx_1    | /docker-entrypoint.sh: Configuration complete; ready for start up
   
   ```
6. open your browser and test `http:://localhost:8183`

   The output will be a login screen

## The structure of the project

Easily talking, if you're a [IT manager](docs/operation.md), you would like to take a look into `docker-compose.yml` and `infra` folder and left `web` for developers.
Otherwise, if you're a [developer](docs/developer.md), you would like to take a look into `web` folder.

Anyway, here is the tree. As they're well splitted, you can focus on your own interest while can take a look in the other. 

```bash
.
├── README.md
├── docker-compose.yml
├── infra
│   ├── nginx
│   │   ├── Dockerfile
│   │   ├── nginx.conf
│   │   └── nginx.conf.backup
│   ├── php-fpm
│   │   ├── Dockerfile
│   │   ├── php-debug.ini
│   │   └── www.conf
│   └── postgres
│       └── pgdata
├── logs
│   └── nginx
│       ├── access.log
│       └── error.log
└── web
    ├── assets
    │   ├── i18n
    │   └── jwt-bin
    ├── composer.json
    ├── composer.lock
    ├── config
    │   ├── connection.json
    │   ├── i18n.json
    │   ├── mode.json
    │   └── randomness.json
    ├── index.php
    ├── logs
    │   ├── 2023-11-22.log
    │   └── 2023-11-23.log
    ├── pages
    │   ├── assets
    │   │   ├── css
    │   │   │   ├── bootstrap.min.css
    │   │   │   └── styles.css
    │   │   ├── i18n
    │   │   ├── js
    │   │   │   ├── login.js
    │   │   │   ├── password-recovery.js
    │   │   │   └── registration.js
    │   │   └── jwt-bin
    │   ├── login.html
    │   ├── password-recovery.html
    │   └── registration.html    
    └── vendor
```
