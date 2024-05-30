# Developer

The developer focused tree, is called `web` and here is it tree:

```bash
.
├── .secure
│   └── password.json
├── assets
│   ├── i18n
│   └── jwt-bin
├── composer.json
├── config
│   ├── connection.json
│   ├── i18n.json
│   ├── mode.json
│   └── randomness.json
├── index.php
├── logs
│   └── 2024-05-30.log
├── pages
│   ├── assets
│   │   ├── css
│   │   │   ├── bootstrap.min.css
│   │   │   └── styles.css
│   │   └── js
│   │       ├── login.js
│   │       ├── password-recovery.js
│   │       └── registration.js
│   ├── login.html
│   ├── password-recovery.html
│   └── registration.html
└── vendor
```

### The main assets folder

You can see that we have an `assets` folder that os global. This is created by YeAPF and it is where it put some belongings that are needed to the correct execution of the system.

At least two folders are here: `i18n` and `jwt-bin` The first is used by the `i18n` (*internationalization*) plugin as a cache.  The `jwt-bin` is used to put jwt tokens that cannot be used again. As in the login: the token is generated and is valid only for one try. Once used, it's putted in the bin folder. Even with short time to live, there's a window where the token could be used. That's the reason it's dropped and not just forgotten. 

An improvement you can do is a garbage collector. Anyway, YeAPF2 has it own jwt-bin garbage collector.

### Composer recipe

`composer.json` is the recipe for composer command. You can use `composer update` in this folder each time you would like to keep your components updated. 

### Config folder

Take a look into that folder. It contains: the mode in which your application is running (`devel`, `stage`, `production`), the connection with redis and Postgres (that you're not using in this example in order to keep it simple). The i18n access key for IBM Watson's translator. And the randomness seed and configuration.

The ideia in this files is that each time you build a new application, you will like to change those values. When your application goes to production, too. And when you suspect you KEY has been leaked, you can change it only in that point. (But in such case, maybe is better put all the application in read-only mode and start with a fresh server)

### Logs folder

A log file per day is putted here. Truly talking it's more than a log file, is a trace/log file. Each time an exception is raised, the stack is dumped there allowing you to investigate what's happening.

You can improve this example, doing a log rotate on that.

An improvement that is planned is to separate trace files from log files. Another one is the level and the scope of the log file. It was present in YeAPF! 0.x but was not yet implemented as the log unit is all new one.

### Pages folder

Is where your application resides.

An small example, can be obtaining taking a look into `login.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="pages/assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="pages/assets/css/styles.css">
</head>
<body>
  <div class="container access-container">
    <h1>Login</h1>
    <form id="loginForm">
      <!-- Form fields -->
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
      <a href="registration" class="btn btn-secondary">Register</a>
      <a href="password-recovery" class="btn btn-secondary">Forgot Password?</a>
    </form>
  </div>
  <script src="pages/assets/js/login.js"></script>
</body>
</html>
```

As you can see, nothing special is there. All the files references are from the root folder. By this reason, to load `login.js`, you need to explicitly tell `pages/...`  as it is here where it resides.

That allows you to load files from other folders easily. In a further example, you will se the usage of `templates` and you will understand better why we chosen that way.

To load this page, you just visit `http://localhost:8183/login` 

An improvement you can do or request for operation area is to eliminate the autoindex in nginx configuration.

### The index.php flow 

Here is an initial version of `index.php` 

```php
<?php
require_once 'vendor/autoload.php';
\YeAPF\WebApp::setUselessURILevel(0);
\YeAPF\WebApp::go();
```

You can try with this version a see what happens. It will work

All the redirections will be operational and nothing changes from the distribution version.

So let's start there.

First we load all the composer modules with `require_once 'vendo/autoload.php'` YeAPF! Will already instantiate a web object that will be ready to use.

Second, we indicate that we're using a raw schema of reference. That mean, the first item mentioned in the URI will be the start of the path. There're some times where the root part of the URI will be used by a main redirector and then the second, third or fourth part will be our own application. As this is not the case int this example, we're indicate to use all the path. Or to not despise no part of the path.

The third and last useful line, indicate to the WebApp to do the process. That means, return the result to the browser.

### Controlling the cache

Open `view-source:http://192.168.71.100:8183/login`  to see the producer html code.

Pay attention to the URL of the css and js files. For example, in my view it looked like this:

```htl
<link href="pages/assets/css/bootstrap.min.css?ts_d7a1f878Z=9884" rel="stylesheet">
```

As you can see, it is different of what we wrote in the `login.html`. Refresh that tab and you will see how it changes.

That is very useful when you're developing as the browser will consider that file as a new file forcing it download again. But is a heavy weight in a production site where you will want to run faster as you can.

You can disable or enforce the usage of a anti-cache changing index.php as this: 

```php
\YeAPF\WebApp::go([], true);
```

The second parameter indicates that YeAPF will change the references to the files to inject a random parameter to force the browse to load again such file instead of use whatever it has in it cache.

But, sometimes you want to do it but not all the time. Say only one time per minute.

You can do as this:

```php
\YeAPF\WebApp::go([], date("YmdHi"));
```

Try and get a look. The files will only be loaded one time per minute.

### Passing values from php to javascript

Let's say you want to pass a tag formed by a constant and a server timestamp from PHP to javascript.

This can serve you as an example:

```php
<?php
require_once 'vendor/autoload.php';
$myTag='theServerName.example.local@'.date("Y-m-dTH:i:s");
\YeAPF\WebApp::setUselessURILevel(0);
\YeAPF\WebApp::go(['tag'=>$myTag], date("YmdHi"));
```

And then in `login.html` (just because is the file we're working on right now) you can add this

```html
<script>
  var tag="#(tag)";
  console.log("Server tag: ", tag)
</script>
```

Reload the source code `view-source:http://192.168.71.100:8183/login` 

Paying attention to the index.php and the modifications, you will see that we have created a `$myTag` with a constant followed by a readable timestamp.

Once the variable is created, it is passed as a parameter to `\YeAPF\WebApp::go()`  

As you can see in the browser, that value appears there. The magic is done by `#()` YeAPF! operator in the javascript part of the login.html page.

Any basic value can be passed in such way.

### A jwt token to call of mine

Now, let analyze the `index.php` included in this distribution.

The first part is dumped in the next figure. There's a function `createSessionToken()` that create a `JWT` token allowed only for be used at `login`

```php
<?php
require_once 'vendor/autoload.php';

function createSessionToken()
{
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->exp = time() + 3600;
    $aJWT->iss = 'login';
    $aJWT->uot = true;
    $aJWT->addAllowedSection(['lgn']);
    $jwtToken = $aJWT->createToken();
    return $jwtToken;
}
```

Later, we have an array called `$context`. The name is not mandatory, but it name easily shows it purpose: the operative context

```php
$context = [];
```

Then, we can create a session token and publish it like this:

```php
$context['sessionToken'] = createSessionToken();
```

Finally, we do the work. Or at least we delegate to YeAPF2 to do the work.

```php
\YeAPF\WebApp::go($context, date("YmdHi"));
```

As in our login page (`login.html`)  we have a code like this:

```html
  <script>
    window.sToken = "#(sessionToken)";    
  </script>
```

We'll have a global variable in `javascript` scope called `sToken` with the content of the `sessionToken` created in `php` 

In this example, we're using different names just to show where each part fits. However, when in a true application, the common behavior is to use exactly the same identifier in both side of the application.

Later, we will change the name and use it in `login.js`

### Basic redirection

Say you want to redirect the user to another page that not the requested. That is the case when your user has arrived at the main page, but you don't have or don't want a mains page. Just a login one. 

This is achieved setting the URI manually before call the `go()` as in this example:

```php
\YeAPF\WebApp::setUselessURILevel(0);

$uri = \YeAPF\WebApp::getURI();
if (strlen($uri)==0) {
    $uri = \YeAPF\WebApp::setURI('login');
}
```

After setting how our application will work, we get the current URI and put it into `$uri` variable.

If the `URI` is empty, that means the users just reached our main page... that does not exists. So we call `setURI()` with the desired redirection. But pay attention that we can use the result to the local `$uri` variable. It's not mandatory, but usually you will want to keep both synchronized. The equivalent code will be like this that, obviously, it's not too much smart:

```php
\YeAPF\WebApp::setUselessURILevel(0);

$uri = \YeAPF\WebApp::getURI();
if (strlen($uri)==0) {
    \YeAPF\WebApp::setURI('login');
    $uri = \YeAPF\WebApp::getURI();
}
```

or, what is smarter than this:

```php
\YeAPF\WebApp::setUselessURILevel(0);

$uri = \YeAPF\WebApp::getURI();
if (strlen($uri)==0) {
    $uri="login";
    \YeAPF\WebApp::setURI($uri);
}
```

by this reason, the first form is adopted.

### A simple index.php with jwt token and redirection

Now, we only want to create our login token when the user really have reached the login page. So our complete `index.php` code could get like this:

```php
<?php
require_once 'vendor/autoload.php';

function createSessionToken()
{
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->exp = time() + 3600;
    $aJWT->iss = 'login';
    $aJWT->uot = true;
    $aJWT->addAllowedSection(['lgn']);
    $jwtToken = $aJWT->createToken();
    return $jwtToken;
}

$context = [];

\YeAPF\WebApp::setUselessURILevel(0);
$uri = \YeAPF\WebApp::getURI();
if (strlen($uri)==0) {
    $uri = \YeAPF\WebApp::setURI('login');
}
if ("login"==$uri) {
   $context['sessionToken'] = createSessionToken(); 
}

\YeAPF\WebApp::go($context, date("YmdHi"));
```

Great, once you had understood what's happening here, let introduce a single modification. Here is the complete code again. Make your conclusions:

```php
<?php
require_once 'vendor/autoload.php';

function createSessionToken()
{
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->exp = time() + 3600;
    $aJWT->iss = 'login';
    $aJWT->uot = true;
    $aJWT->addAllowedSection(['lgn']);
    $jwtToken = $aJWT->createToken();
    return $jwtToken;
}
$context = [];

\YeAPF\WebApp::setUselessURILevel(0);
$uri = \YeAPF\WebApp::getURI("login");

if ("login"==$uri) {
   $context['sessionToken'] = createSessionToken(); 
}

\YeAPF\WebApp::go($context, date("YmdHi"));
```

Now, we're using `getURI()` with a default value that makes the same as in the prior example: if no page is indicated, let's go to `login` page. Your imagination will take you further.

### Discarding a jwt token

Let's consider this code:

```php
<?php
require_once 'vendor/autoload.php';

function createSessionToken()
{
  ...
}

function destroySessionToken()
{
    $sessionToken = $_COOKIE['sessionToken'];
    $aJWT = new \YeAPF\Security\yJWT();
    $aJWT->sendToBin($sessionToken);
}

$context = [];

\YeAPF\WebApp::setUselessURILevel(0);
$uri = \YeAPF\WebApp::getURI("login");

preg_match('/^[a-zA-Z_]{1,}[a-z0-9A-Z_]*/', $uri, $uri_path);
if ($uri_path) {
    $first = $uri_path[0];
    switch($first) {
        case 'login':
            $context['sessionToken'] = createSessionToken();
            break;
        case 'logout':
            destroySessionToken();
            \YeAPF\WebApp::setURI("login");
            break;
    }
}

\YeAPF\WebApp::go($context, 'tmp'.date("YmdHi"));
```

Here we have some new stuff that we would like to show to you.

1. Now we have a `destroySessionToken()` based on a `cookie`. Later we will see how that bridge has been done. By now, what is important is to understand that we can call `sendToBin()` to disable a JWT token already generated.
2. The second stuff there is an analyzer that depends on `preg_match()` and is introduced here to shows how to implement your own behavior. You can play with this to understand how it works following this link: https://www.phpliveregex.com/p/Kqg  But essentially what is said here is that a path *need* to start with a letter or an underscore and can be followed for any character into the set `[ a-z 0-9 A-Z _]` (spaces added for help in readability)
3. Finally we analyze the page reached by the user and then we do one of two things: a) create a login session token if it is `login` or b) we destroy the current token and we redirect the user to `login` page.



### A simplified version

You can just use that YeAPF2 offers and do like this:

```php
...
$context = [];

\YeAPF\WebApp::setUselessURILevel(0);
$uri = \YeAPF\WebApp::getURI('login');
$first = \YeAPF\WebApp::splitURI()[0];
switch ($first) {
    case 'login':
        $context['sessionToken'] = createSessionToken();
        break;
    case 'logout':
        destroySessionToken();
        break;
}
...
```

We can open the login page and do a logout, but we cannot login indeed. 

### A look into login.js

Open `login.js` with your editor. The first you need to watch is that there is a call to the well old known `addOnLoadManager()` 

```javascript
addOnLoadManager(
    function() {
        ...
    }
);
```

Essentially what it does is to execute the declared function as early as the DOM is completely loaded. That means, ready to be used.

Then, you'll see that the only thing defined there is a `click` event associated with `btnLogin`.

```javascript
addOnLoadManager(function() {
  yDom.addEvent("btnLogin", "click", function (event) {
    event.preventDefault();
  
    const username = y$('username').value;
    const password = y$('password').value;
    ...
  }
});
```

Maybe you're not familiar with such things as anonymous functions. So let strip it in declarative ones.

```javascript
function loginEventHandler(event) {
    event.preventDefault();
  
    const username = y$('username').value;
    const password = y$('password').value;
    ...    
}

function setBtnLoginEvent() {
   yDom.addEvent("btnLogin", "click", loginEventHandler);
}

addOnLoadManager(setBtnLoginEvent);
```

As in Pascal, read from bottom to top. (Pascal forces you to define an element prior to use it. In Javascript that's not the case and that is the reason why usually js programming becomes a nightmare ... even with Typescript)

Anyway, in one way or in the other, you will finish with some functions. We will focus on them. Let's start with the most declarative way in order to explain some concepts.

```javascript
function processResponse(response) {
    if (response.ok) {
      console.log("Ok");
      // window.location.href = 'admin.html';
    } else {
      // Handle invalid login
      // Display error message or perform necessary actions
    }    
}

function processError(error) {
    console.error('Error:', error)
}

function loginEventHandler(event) {
    event.preventDefault();
  
    const username = y$('username').value;
    const password = y$('password').value;
    
    fetch('/login', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
      },
      body: JSON.stringify({ username, password, sessionToken })
    })
      .then(processResponse)
      .catch(processError);
}
```

We think that some things need to be explained. 

First the old fashioned function or selector `y$()` has been modified. You can get an element by it `id` or a set of element by it `name` or by it `class`.  You don't need to put something as `#` or `.` at the start. So use with care.

Second, `yDom` is new, but, you still will find old stuff as in `yDom.addEvent()` 

Third, we're using `fetch`, so if you're running and old version of `EcmaScript` a.k.a. `javascript` you'll get loosed.

Forth and most important, we're calling `/login` again. That drives us to `multiplexing` of entries in a web application.

### Multiplexing techniques

Multiplexing is an old technique that allow you to use the same stuff in a different way on the second or third usage. In an old 8/16 bits machine, you could use the same 8 bits wires to access 16 bits address indicating a shift in one of the wires, so while the number of wires maintains reduced, the resources you can access looked as a 16 bits one making the device cheaper and cooler. 

Here we will use the concept to reuse the `login`  path. First it has been used as a web request, now we will use it as an `api` entry. Even if `index.php` is not an `api` oriented script.

The purpose here is to show how to detect that situation and to response accordingly.

**Remember:** is not to use this in production. YeAPF2 has a more reliable way to do that using Swoole/HTTP2 and all the constraints controls that are useful in a RestFUL or gRPC environment.

As you can see in `login.js`, we're indicating that we're accepting json as reponse.

```javascript
'Accept': 'application/json'
```

So, we can catch this in the server to attend that.

#### First incarnation of login

Here is the easiest way to control a login. Check this and the we will see something more clever. 

In `index.php` we do that with this code:

```php
switch ($first) {
    case 'login':
        if (\YeAPF\WebApp::clientExpectJSON()) {
            $request = \YeAPF\WebApp::getRequest();
            if ($request['username']=='admin' && $request['password']=='admin') {
                // First token is just used for login
                destroySessionToken();

                // This new token is for administration
                $context['sessionToken'] = createSessionToken(['adm']);
                setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');

                // YeAPF2 pay attention to __json in the context
                // if it exists, then here will be the json response
                // to the client.
                $context['__json'] = json_encode([ 
                  'userLogged' => true,
                  'sessionToken' => $context['sessionToken']??null                
                ]);
            } else {
                http_response_code(401);
                $context['error'] = 'Invalid credentials';
            }
        } else {
            $context['sessionToken'] = createSessionToken();
            setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
        }        
        break;
    case 'logout':
        destroySessionToken();
        break;
}
```

We use `clientExpectJSON()` to detect the client is requesting a `json` answer. Then, we check that the user name and user password is `admin` (obviously that's not the correct way to do that).

If the credentials are ok, we do two things: 

1. destroy the current session token as it only was used for login and
2. create a new session token allowed to use `adm` section (YeAPF2 token's sections are determined by programmer and cannot have more than three letters each)

This new session token is returned in `$context` array and set in the current session as a cookie.

Now we change our `login.js` to use that result and redirect the user to the first administration page called "`admin`"

```js
function processResponse(response) {
  if (response.ok) {
    response.json().then(data => {
      if (typeof data.sessionToken=="string") {
        sessionToken=data.sessionToken;
        window.location.href="admin";
      } else {
        displayMessage('Invalid credentials', 'error');
      }      
    }).catch(error => {
      displayMessage('Error: ' + error.message, 'error');
    });
  } else {
    displayMessage('Server error', 'error');
  }
}
```


#### Second incarnation

Here is a better way to treat the logon challenge:

```php
switch ($first) {
    case 'login':
        if (\YeAPF\WebApp::clientExpectJSON()) {
            $request = \YeAPF\WebApp::getRequest();

            if (file_exists('.secure/password.json')) {
                $passwordFile = json_decode(file_get_contents('.secure/password.json'), true);
            } else {
                $passwordFile = [];
            }

            $userLogged=false;

            if (isset($request['username']) && isset($request['password']) && isset($passwordFile[$request['username']])) {

                if ($passwordFile[$request['username']]['password'] == $request['password']) {
                    destroySessionToken();
                    $context['sessionToken'] = createSessionToken(['adm']);
                    setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
                    $userLogged = true;
                }
            }
            $context['__json'] = json_encode([ 
                'userLogged' => $userLogged,
                'sessionToken' => $context['sessionToken']??null                
            ]);
            if (!$userLogged) {
                http_response_code(401);
                $context['error'] = 'Invalid username or password';
            }

        } else {
            $context['sessionToken'] = createSessionToken();
            setcookie('sessionToken', $context['sessionToken'], time() + 3600, '/');
        }
        break;
    case 'logout':
        destroySessionToken();
        break;
}

```
