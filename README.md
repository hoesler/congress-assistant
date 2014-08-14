Congress Assistant
==================
This is a PHP Web-Application based on the [CodeIgniter](http://ellislab.com/codeigniter "CodeIgniter") framework, initially created for the ESEB 2011 Congress in Tübingen.

Its purposes are:

* Synchronizing the time slots for talks in multiple lecture halls, by displaying a countdown during talks and a timeline of upcoming events and customizable slides for announcements between them. This is implemented via a [backbone.js](http://backbonejs.org/ "backbone.js") based frontend (Time synchronization is done by continuously fetching a master time from the the server).
* Serving webpages to assist in organizing activities (In Tübingen: Meet a Silverback, Poster Invitations, ...)
* Sending personalized emails to different groups of attendants

Basic Installation Instructions
-------------------------------
* Follow the [install instructions for CodeIgniter](https://ellislab.com/codeigniter/user-guide/installation/index.html)
* Init database using install/init_database.sql (Adjust table prefix if neccessary)
* Fill tables in database (For many tables there is no editing frontend available yet)
* Adjust application to your needs (Edit views, styles, scripts, etc. or add new functionality)

Usage
-----
...

Sending emails should be done via a cron task executing 'php $WEB_HOME/index.php cron sendmail'

Events at which this software has been used
-------------------------------------------
* ESEB 2011 Congress in Tübingen
* [ESEB 2013 Congress in Lisbon](http://www.eseb2013.com)
