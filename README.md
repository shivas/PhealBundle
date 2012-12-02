# PhealBundle

[![Build Status](https://secure.travis-ci.org/shivas/PhealBundle.png?branch=master)](https://travis-ci.org/shivas/PhealBundle)

Copyright (C) 2012 by Audrius Karabanovas
All rights reserved.

PhealBundle is factory/wrapper for pheal/pheal library available on Packagist

## LICENSE
PhealBundle is licensed under a MIT style license, see LICENSE
for further information

## FEATURES
- fully transparent, doesn't change Pheal work in any way
- defines Symfony2 service called "shivas.pheal.factory" for Pheal objects creation
- defines configuration rules for bundle to automatically configure new Pheal instances directly from your application configuration file
- configuration options named exactly same as in PhealConfig class, expect there is additional "reconfigure: boolean" option for factory itself.
- uses Reflection to build/configure object, this means bundle doesn't have to change if pheal/pheal change unless there is drastic changes in configuration

## REQUIREMENTS
- PHP 5.3.3

## INSTALLATION

### composer
PhealBundle is available as package shivas/pheal-bundle through packagist on composer http://getcomposer.org

Add to composer.json

    "shivas/pheal-bundle": "dev-master"

Run composer update to install

    php composer.phar update

Add Bundle to your AppKernel.php

    new shivas\PhealBundle\shivasPhealBundle(),

## USAGE

### Create Pheal API object

    // controller action
    $pheal = $this->get('shivas.pheal.factory')->getInstance("keyID", "vCode"[, "scope for request"]);

### Configuration options
Configuration options reference is available in Symfony console running:

    ./app/console config:dump-reference shivasPhealBundle

    or

    php app/console config:dump-reference shivasPhealBundle

All options except "reconfigure" is exact copy of Pheal

    reconfigure: true|false

Reconfigure option is used for factory itself, default to False meaning there is no changes in default behavior of Pheal.
Setting this option to True makes factory to reconfigure PhealConfig singleton with default settings from your application configuration on each object creation.

## TODO
- more documentation
- UNIT tests

## LINKS
- [Github](http://github.com/ppetermann/pheal)

## CONTACT
- Audrius Karabanovas <audrius.karabanovas@gmail.com>

