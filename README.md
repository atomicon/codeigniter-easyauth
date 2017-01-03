Codeigniter-Easyauth
====================

Very basic and "need-to-know" authentication package.

This package is very basic and focuses on 1 thing only: authentication

Easyauth is very configurable.

> For a secure solution of handling sessions you may want to give [codeigniter-native-session][1] a go. It handles sessions server-side instead of cookies

How to use:

- Run the SQL file (it is in the folder 'sql')
- Copy **config/easyauth.php** from config to **application/config/easyauth.php**
- Copy **libraries/easyauth.php** from config to **application/libraries/easyauth.php**
- Copy **config/Auth.php** from controllers to **application/controllers/Auth.php**
- Copy the folder **views/auth-simple** or **views/auth-bootstrap** to **application/views/auth**

Thats it. (you can login with: **admin@admin.com** and **password**)

Installation
============
- Copy "codeigniter-easyauth" folder in third party
- Copy "codeigniter-easyauth/controllers/Auth.php" to your controllers directory
- Paste the following section to your routes.php config file:

```
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['register'] = 'auth/register';
$route['forgot-password'] = 'auth/forgot_password';
$route['reset-password/(:any)'] = 'auth/reset_password/$1';
$route['impersonate/(:any)'] = 'auth/impersonate/$1';
$route['unimpersonate'] = 'auth/unimpersonate';
```

***note***

It isn't mandatory that you call your database table 'users' you can change that if you like.
The same for your controller... a simple find and replace on 'Auth' will do.

Current functionality
=====================

(examples below are based on controller name **auth** e.g. *http://localhost/cidev/auth/login* etc.)

- auth/login
- auth/logout
- auth/register
- auth/forgot
- auth/profile
- auth/reset/xxx

The reset link is only available if a user 'lost' his password. He will be receive an email with a link (***reset-password/xxx***) with a unique hash

Goal
====

The goal of codeigniter-easyauth is keeping everything as simple and clean as possible.

When I am completely satisfied I will turn this into a spark

[1]: https://github.com/atomicon/codeigniter-native-session