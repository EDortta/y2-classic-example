# README

This folder is used to save secured files. 

One way to test if the Nginx configuration is working correctly is to try accessing this file. If everything is set up properly and configured elegantly, the browser should return a 403 Forbidden error, indicating that access to this file is restricted.

**THIS IS NOT MENT TO BE A PRODUCTION ENVIRONMENT. THIS IS FOR DEVELOPMENT AND DEMONSTRATION PURPOSES ONLY.**

## Change `password.json`

This is a commonly  `password.json` file.
```json
{
    "user1": {
        "password": "Qw3rTy!@#$"
    },
    "user2": {
        "password": "Zx9Cv8Bn6M"
    },
    "user3": {
        "password": "Lk2Jh4Fg5D"
    }
}
```

You need to produce a password.json file in order to change the default `admin` / `admin` values.

Of course is that in production you will store these calues in a database and not in a file.