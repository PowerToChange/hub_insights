# pulse_beta

## Setup
### Server Reqs
php mysqli
apache2 mod_rewrite
  
### Configuration
Four config files are required

    config/civi_constants.php
    config/columnnames.php
    config/dbconstants.php
    config/pulse_constants.php
    
Example files are not provided, please copy files from the staging or production server.


## Site Structure
### Folder overview
* CAS: login services
* config: pulse, civicrm, and db information
* css, fonts, images, js: what it says
* discover: code for discover section of site, more below
* insights: code for discover section of site, more below
* extras: import scripts and single use code
* base folder: login, header, footer, and shortener code

### Discover
Used for friendship contact follow up and reporting.
Contains blackbox.php file that handles civicrm api calls and dbcalls.php for mysql server calls.
Ajax folder contains php files called by jquery ajax for live updating and fetching.

### Insights
Used to enter and report on ministry information
Contains blackbox.php file that handles civicrm api calls and dbcalls.php for mysql server calls.
Ajax folder contains php files called by jquery ajax for live updating and fetching
