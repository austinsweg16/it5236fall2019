# Welcome

This repository is for my Fall 2019 IT 5236 Distributed and Mobile Systems course.

The "webapp" folder contains a simple "To Do" web application. To get the web application woreking, you will need to (1) copy the contents of the "webapp" folder onto a web server with PHP support; (2) create a MySQL database named "IT5236" along with a database user account that has access to the database; (3) create a table in the database using the SQL found in sql/IT5236.sql; (4) rename "configuration-sample.php" to "configuration.php"; (5) edit "configuration.php" to include the username and password from step two.

If you have to return to the saved image, stop running Apache and disable it via command line, docker ps -a to find container name, start the container, enable it to start everytime you login.
