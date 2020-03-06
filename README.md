Kinonyashka
========================

Welcome to the Kinonyashka!

Application for booking of visitors to films in one cinema.

Installation 
--------------

You can create clear mysql database with file: db_only_structure.sql

Or you can import database with sample data with file: db_width_sample_data.sql

You will also have to set up virtual host:

copy and modify data from virtual_host.conf file and modify it to fit your needs

required options are ServerName and DocumentRoot


Utilization 
--------------

Login page: http://you-host-name/auth/login

To sign in into administrator panel you can use default credentials: 

admin_username: yura.kralya@gmail.com
admin_password: 123123qwe

Or you can change this data in /config.yml and log in with your username and password.


Admin panel
--------------

On start dashboard page you can see list of all films with count of seats by session
and progress bars on reserved and empty places. 
(URL: /you-host-name/admin/dashboard/index or click on Space Cinema in top left corner) 

If you click on seats counter you can see visualizator about seats on film session.

You can add film by clicking on left menu "Add Film" or follow the link /you-host-name/admin/films/add

You can edit film by clicking on left menu "List Film" or follow the link /you-host-name/admin/films/index,
then click button "Edit" on selected film.

Fields "Film Title" and "Film Description" are required.

Log out button locate in right top corner.

Frontend
--------------

Detail film page url is /you-host-name/films/view/film/{FILM_URL}

You can check and reserve seat only after you fill fields: Name, Email, Phone and Session Time 
and click "Start booking" button.

After checking seats you need click "Confirm Selected Places" button.