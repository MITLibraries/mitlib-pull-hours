# MITlib Pull Hours

[![Build Status](https://travis-ci.org/MITLibraries/mitlib-pull-hours.svg)](https://travis-ci.org/MITLibraries/mitlib-pull-hours)
[![Code Climate](https://codeclimate.com/github/MITLibraries/mitlib-pull-hours/badges/gpa.svg)](https://codeclimate.com/github/MITLibraries/mitlib-pull-hours)
[![Issue Count](https://codeclimate.com/github/MITLibraries/mitlib-pull-hours/badges/issue_count.svg)](https://codeclimate.com/github/MITLibraries/mitlib-pull-hours)

This is a WordPress plugin which harvests the contents of a Google Sheet,
storing it locally in JSON format. This local cache is then read by the user's
browser in order to display current information about library hours.

This plugin is one part of the MIT Libraries' hours system. The complete
system is comprise of:

1. The Google Sheet (for data storage)
2. The Harvester plugin (this repo) which harvests the spreadsheet for local
   consumption by users' browsers
3. The Loader system (in [the MITlibraries-Parent theme](https://github.com/MITLibraries/MITlibraries-parent)) which displays
   hours information on relevant pages

## Deploying this plugin

This plugin is built with Composer, which requires a build step to be run on
the server in our current configuration.

**Failure to run these steps during a deploy will break WordPress!**

The steps of the deploy are:

1. Shell onto the target server, and check out the desired branch into your
   deploy directory.
2. Enable the PHP CLI using `scl enable rh-php72 /bin/bash`.
3. Ensure that Composer has the latest dependencies installed. **This is the
   step which, if skipped, will result in WordPress breaking.** The repair
   process is to go back and run this step, and then deploy again.
   a. First time deploy: run `composer install` from inside the plugin repo
   b. When dependencies update: run `composer update` from inside the plugin
      repo
4. Optimize the auto-loader by running `composer dumpautoload -o` inside the
   plugin repo. **If this step is not run, the plugin will disappear from the
   administrative interace.** The repair process is to re-start the deploy
   from this step.
5. Deploy the plugin using the existing script (`/deploy/deploy-plugin.sh`)

## Running the harvester

This plugin adds two things to the admin interface of WordPress:

1. A widget on the admin dashboard, showing the last time the harvester was
   run, as well as links to the plugin settings and to the hours spreadsheet.
2. A settings page for site builders to update its configuration and run a new
   harvest. This page appears under the Settings menu item.

Updating the plugin settings, and running the harvester itself, are both done
from the settings page. There are fields for the Google spreadsheet key, and
for the API key that you will use to read the spreadsheet contents.

The Hours spreadsheet key is found in the URL to your spreadsheet. The default
value for this field is the key used for the MIT Libraries hours, as an
example.

The API key can be created using the [Google Developers Console](https://console.developers.google.com/). Please check
the Google documentation for the [Sheets API](https://developers.google.com/sheets/api) for more information on this.

Submitting the settings form (via the "Update hours cache" button) runs the
harvester. The files will be saved in a directory on your webserver at
`/app/libhours-buildjson/` - changing this value can be done in the Harvester
object in this repo, inside the set_properties method.

## Troubleshooting

Here are a list of errors that we've encountered while running this plugin.
Most of the problems come down to mistakes in the deploy process.

### Problems visible in the browser

#### "There has been a critical error on your website."

If all of WordPress breaks, and all pages display the error message above,
then the plugin needs to be re-deployed after running `composer install`.

#### Plugin widget has disappeared

The autoloader has not been optimized (step 4 of deploy process). Run
`composer dumpautoload -o` in the plugin repository, and redeploy.

#### "Sorry, you are not allowed to access this page." when trying to access the settings page

The autoloader has not been optimized (step 4 of deploy process). Run
`composer dumpautoload -o` in the plugin repository, and redeploy.

### Messages appearing in the error log

The following error log messages may appear:

#### call_user_func_array() expects parameter 1 to be a valid callback

```
PHP Notice:  Type: ErrorException; Message:
call_user_func_array() expects parameter 1 to be a valid callback,
class 'Mitlib\\Pull_Hours_Admin_Widget' not found;
File: .../wp-includes/class-wp-hook.php; Line: 287
```

The autoloader has not been optimized (step 4 of deploy process). Run
`composer dumpautoload -o` in the plugin repository, and redeploy.

#### require() failed to open stream

Messages such as either of the following appear in the Apache error logs:

```
PHP Fatal error:  require(): Failed opening required
.../wp-content/plugins/mitlib-pull-hours/vendor/autoload.php in
.../wp-content/plugins/mitlib-pull-hours/mitlib-pull-hours.php on line 24
```

or

```
PHP Warning:  require(.../wp-content/plugins/mitlib-pull-hours/vendor/autoload.php):
failed to open stream: No such file or directory in
.../wp-content/plugins/mitlib-pull-hours/mitlib-pull-hours.php on line 24
```

This is an indication that the plugin was deployed without running `composer
install`.
