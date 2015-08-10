# wordpress-memento-plugin

A plugin for Wordpress web sites to enable the Memento framework for time-based access

This is a work in progress and should not be expected to work.

[![Build Status](https://travis-ci.org/pastpages/wordpress-memento-plugin.svg?branch=master)](https://travis-ci.org/pastpages/wordpress-memento-plugin)

* Testing: [travis-ci.org/pastpages/wordpress-memento-plugin](https://travis-ci.org/pastpages/wordpress-memento-plugin)

## Getting started

```bash
$ sudo apt-get install php5 php5-dev php-pear phpunit
```

Create a symbolic link between the source code and the plugin directory in
your local Wordpress installation.

```bash
$ ln -s `pwd`/memento/ /path-to-your-wordpress/wp-content/plugins/memento
```

## Running tests

Unittests are written for ``phpunit`` and executed like so:

```bash
$ make test
```