# SSH

[![Build Status](https://travis-ci.org/LaravelCollective/remote.svg?branch=master)](https://travis-ci.org/LaravelCollective/remote)
[![Total Downloads](https://poser.pugx.org/LaravelCollective/html/downloads.svg)](https://packagist.org/packages/laravelcollective/html)
[![Latest Stable Version](https://poser.pugx.org/LaravelCollective/html/v/stable.svg)](https://packagist.org/packages/laravelcollective/html)
[![Latest Unstable Version](https://poser.pugx.org/LaravelCollective/html/v/unstable.svg)](https://packagist.org/packages/laravelcollective/html)
[![License](https://poser.pugx.org/LaravelCollective/html/license.svg)](https://packagist.org/packages/laravelcollective/html)

- [Installation](#installation)
- [Configuration](#configuration)
- [Basic Usage](#basic-usage)
- [Tasks](#tasks)
- [SFTP Downloads](#sftp-downloads)
- [SFTP Uploads](#sftp-uploads)
- [Tailing Remote Logs](#tailing-remote-logs)

<a name="installation"></a>
## Installation

> If you have changed the top-level namespace to something like 'MyCompany', then you would use the new namespace instead of 'App'.

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `laravelcollective/remote`.

    "require": {
        "laravelcollective/remote": "~5.0"
    }

Next, update Composer from the Terminal:

    composer update

Next, add your new provider to the `providers` array of `config/app.php`:

```php
  'providers' => [
    // ...
    'Collective\Remote\RemoteServiceProvider',
    // ...
  ],
```

Finally, add two class aliases to the `aliases` array of `config/app.php`:

```php
  'aliases' => [
    // ...
      'SSH' => 'Collective\Remote\RemoteFacade',
    // ...
  ],
```
<a name="configuration"></a>
## Configuration

Laravel includes a simple way to SSH into remote servers and run commands, allowing you to easily build Artisan tasks that work on remote servers. The `SSH` facade provides the access point to connecting to your remote servers and running commands.

The configuration file is located at `config/remote.php`, and contains all of the options you need to configure your remote connections. The `connections` array contains a list of your servers keyed by name. Simply populate the credentials in the `connections` array and you will be ready to start running remote tasks. Note that the `SSH` can authenticate using either a password or an SSH key.

> **Note:** Need to easily run a variety of tasks on your remote server? Check out the [Envoy task runner](http://laravel.com/docs/5.0/envoy)!

<a name="basic-usage"></a>
## Basic Usage

#### Running Commands On The Default Server

To run commands on your `default` remote connection, use the `SSH::run` method:

	SSH::run([
		'cd /var/www',
		'git pull origin master',
	]);

#### Running Commands On A Specific Connection

Alternatively, you may run commands on a specific connection using the `into` method:

	SSH::into('staging')->run([
		'cd /var/www',
		'git pull origin master',
	]);

#### Catching Output From Commands

You may catch the "live" output of your remote commands by passing a Closure into the `run` method:

	SSH::run($commands, function($line)
	{
		echo $line.PHP_EOL;
	});

## Tasks
<a name="tasks"></a>

If you need to define a group of commands that should always be run together, you may use the `define` method to define a `task`:

	SSH::into('staging')->define('deploy', [
		'cd /var/www',
		'git pull origin master',
		'php artisan migrate',
	]);

Once the task has been defined, you may use the `task` method to run it:

	SSH::into('staging')->task('deploy', function($line)
	{
		echo $line.PHP_EOL;
	});

<a name="sftp-downloads"></a>
## SFTP Downloads

The `SSH` class includes a simple way to download files using the `get` and `getString` methods:

	SSH::into('staging')->get($remotePath, $localPath);

	$contents = SSH::into('staging')->getString($remotePath);

<a name="sftp-uploads"></a>
## SFTP Uploads

The `SSH` class also includes a simple way to upload files, or even strings, to the server using the `put` and `putString` methods:

	SSH::into('staging')->put($localFile, $remotePath);

	SSH::into('staging')->putString($remotePath, 'Foo');

<a name="tailing-remote-logs"></a>
## Tailing Remote Logs

Laravel includes a helpful command for tailing the `laravel.log` files on any of your remote connections. Simply use the `tail` Artisan command and specify the name of the remote connection you would like to tail:

	php artisan tail staging

	php artisan tail staging --path=/path/to/log.file

