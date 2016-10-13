venice-demo
===========

A Symfony project created on February 25, 2016, 7:52 pm.

# Issue on linux(potentially Mac OS)
If you are using *necktie_url: 'http://localhost/app_dev.php'* in parameters.yml you will have problems!!! 
This url is used on two places:
    - while redirecting to necktie in login process
    - in api calls (in the login process, too)
    
When using "localhost", The redirection is ok as the browser translated the "localhost" to host machine IP. 
But the api calls are not using browser and the "localhost" refers to the current venice container. 
So the venice is sending API calls to it's own (not existing) API.
The localhost has to be replaced by your network IP.

#How to make JS work
In docker:/var/app type:
    npm run build
    (hit ENTER)

if you have a problem with docker, you can do the same from project folder,
but you need npm (on linux you probably will have to run the script with sudo)
