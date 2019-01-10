******The following setup is for the demo********

Setting Up Database:
	Go to pgAdmin4
	Under Project1, run the following query *
		CREATE TABLE users (
		    id SERIAL PRIMARY KEY,
		    username VARCHAR(50) NOT NULL UNIQUE,
		    password VARCHAR(255) NOT NULL,
		    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		);

Setting up php files
	Go to C:\Bitnami\wappstack-7.1.13-0\apps\demo\htdocs
	Extract this zip file into the folder
	Under config.php, modify the file to your login credentials

Populating data
	Register users until you happy.


*Note that SERIAL is a special datatype that does auto-incrementing upon each insert. I tot this would be better as a primary key since users cannot manipulate this data in anyway

****HAVE FUN PLAYINGA ROUND!!!!! :)*****