# wordpress-memento-plugin

A plugin for Wordpress web sites to enable the Memento framework for time-based access

This is a work in progress and should not be expected to work.

[![Build Status](https://travis-ci.org/pastpages/wordpress-memento-plugin.svg?branch=master)](https://travis-ci.org/pastpages/wordpress-memento-plugin)

* Testing: [travis-ci.org/pastpages/wordpress-memento-plugin](https://travis-ci.org/pastpages/wordpress-memento-plugin)

## Installation from source code

Install the system requirements.

```bash
$ sudo apt-get install php5 php5-dev php-pear phpunit
```

Install Wordpress and its dependencies, however you might like. Here's [one guide](https://www.digitalocean.com/community/tutorials/how-to-install-wordpress-on-ubuntu-14-04).

Clone this repository on your computer.

```bash
$ git clone https://github.com/pastpages/wordpress-memento-plugin.git
```

Jump into the directory.

```bash
$ cd wordpress-memento-plugin
```

Create a symbolic link between the source code and the plugin directory in
your local Wordpress installation.

```bash
$ ln -s `pwd`/memento/ /path-to-your-wordpress/wp-content/plugins/memento
```

Open up the administration panel of your local Wordpress installation and you
should see Memento among the installed plugins.

## Running tests

Unittests are written for ``phpunit`` and executed like so:

```bash
$ make test
```