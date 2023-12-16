# Operation

This is the operational folder structure:

```bash
.
├── nginx
│   ├── Dockerfile
│   └── nginx.conf
└── php-fpm
    ├── Dockerfile
    ├── php-debug.ini
    └── www.conf
```

As you can see, there're two folders. One for `nginx` and other for `php-fpm`. Each of them, has it own well defined config file

Pay attention that `nginx.conf` redirect all requests  to `index.php` (that is attended by `php-fpm`)  except those files that are into the `assets` folder.  In such way, you can build a more secure server as commonly the `assets` folder will contain only static files in read-only mode and all the other request will pass by an unique php script.

### Log files

The logs are being putted into the docker host. In particular, in the `logs`  folder.  That allow you to review the logs without the need to enter into the container. As the php-fpm is configured to put the logs there too, you can put an eye on the fish and other on the cat at the same time.

An improvement you can do is to add a remote logger. That's specially interesting when instantiating more than one server. 