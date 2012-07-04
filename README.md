Codeigniter-Easyauth
====================

Very basic and "need-to-know" authentication package.

This package is very basic and focuses on 1 thing only: authentication

Easyauth is very configurable.

How to use:

- Run the SQL file (it is in the folder 'sql')
- Copy the easyauth.php from config to your application/config folder
- Copy the Auth.php from controllers to your application/controllers folder
- Copy the folder **views/auth-simple** or **views/auth-bootstrap** to **application/views/auth**

Thats it. (you can login with: ***admin@admin.com*** and ***password***)

*note*
It isn't mandatory that you call your database table 'users' you can change that if you like.
The same for your controller... a simple find & replace on 'Auth' 'auth' will do.

Current functionality
=====================

(examples below are based on controller name **'auth'**)

- auth/login
- auth/logout
- auth/register
- auth/forgot
- auth/profile
- auth/reset/xxx

The reset link is only available if a user 'lost' his password. He will be receive an email with a link (***auth/reset/xxx***) with a unique hash

Views
=====

There are 2 view-folders available

- **auth** *These views are in the most simple form*
- **auth-bootstrap** *These views are optimized for twitter bootstrap*

When installing easyauth make a choice:
- Copy **auth-simple** from views to **application/views/auth**
- Copy **auth-bootstrap** from views to **application/views/auth**

**(make sure to rename it to 'auth')**

Goal
====
The goal of codeigniter-easyauth is keeping everything as simple and clean as possible.

When I am completely satisfied I will turn this into a spark

