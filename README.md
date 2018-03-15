# Open Event Manager
Open Event Manager is a website meant to be used by groups that want to organize larger events that use an online website as the main tool for sharing and organizing information at the event. It includes or will include things such as a live schedule (automatically updates on the page when changes are made), a team organization system, a tournament system, and systems for theming and changing the website to best fit the event's needs, along with link distribution on the main page of the site.

## Installation
You will need to set up a web server and install PHP7, mysql, and composer in order for this site to work.

The files from the project should be cloned into a web directory on your server. 

Once you have the files cloned onto your server, you should run `composer install` in the base directory of the project to install all of the libraries

Then, you should update your apache or nginx files in line with https://symfony.com/doc/master/setup/web_server_configuration.html. We plan to provide an easier way to do this in the future.

## Configuration
All configuration can be done in the .env file, which should be created from the .env.dist file. Currently, the only value to add/update there is the DATABASE_URL that points to your MySQL database

## License
MIT License

## Credits
[Bootstrap Colorpicker](https://github.com/farbelous/bootstrap-colorpicker) - Color picker used on theme page by Farbelous

[Bootstrap Slider](https://github.com/seiyria/bootstrap-slider) - Slider used on settings pages by Kyle Kemp

[JSON-RPC-Simple](https://github.com/datto/php-json-rpc-simple) - RPC library used for API by Datto Inc.
