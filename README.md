#Web application to update CSV data into MySQL database

This web application is developed using HTML,CSS,PHP,JS and XAMPP Server.

Create a folder named uploads before executing.
Files are stored temporary in the folder.

To upload large files, change the max_execution_time to high limit in the file php.ini stored in xampp/php/ .
For Example: If the csv file contains 1 lakh records, it will take approximately 1 minute 30 seconds.  So, I should set max_execution_time=120. 
In general, set max_execution_time=1000.

In localhost/phpmyadmin site, create a database named grootan.
