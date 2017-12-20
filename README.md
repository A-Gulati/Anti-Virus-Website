# Anti-Virus-Website
This is a basic antivirus that checks user uploaded files for virus signatures from an admin created database of malicious files. 

Requires a backend mysql database (the db variable uses a database named login_test) which must contain a table called 'users' that has a column for user name and a md5 encrypted and hashed password. I have not facilitated a way to do this from within the code, it must be setup before running.

Consists of 3 pages:

-scan: the user can uplaod a file they want to scan, or choose to login as admin

-login: allows user to input credentials and login as admin

-admin: the admin can add known viruses to the database against which new files will be checked 
