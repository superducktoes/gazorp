# gazorp
Gazorp malware
https://research.checkpoint.com/the-gazorp-dark-web-azorult-builder/
<br>

# Install the needed packages
if httpd is installed already
```
# sudo service httpd stop
# sudo yum erase httpd httpd-tools apr apr-util

# sudo yum install httpd
# sudo yum install mysql mysql-server
# sudo yum install php56w php56w-xml php56w-xmlrpc php56w-soap php56w-gd

# sudo yum install php56w-mysqlnd 
```

# Create a new database to use
```
# mysqld start
# mysql -u root
```
### Create a new database called Garfield
```
mysql> CREATE DATABASE garfield;
```

### create garfield user and password
```
mysql> CREATE USER 'garfield'@'localhost' IDENTIFIED BY 'garfield';
mysql> GRANT ALL PRIVILEGES ON *.* to 'garfield'@'localhost';
```

# Install and start the client

```
service httpd start
```

extract the gazor.zip file

move /panel to /var/www/html/

- this is where I had some problems. use the modified install.php file to write the salt to /tmp
browse to http://ip/install.php

- fill out fields
. most should be garfield

- install.php should be removed automatically. if not:
```
# mv install.php install.php.bak
```


- check config.php to see if the variables have been updated and if not manually enter them.

- should now see a welcome screen when browsing to http://ip/index.php

## Login
![login](https://raw.githubusercontent.com/superducktoes/gazorp/master/login.PNG)

## Home Screen
![home screen](https://raw.githubusercontent.com/superducktoes/gazorp/master/home.PNG)
