# Apparat Objects

Purpose of this module
* Mapping between objects and their resources (e.g. file system)
	* Instantiate objects from object resources
	* Create new resources from unpersisted objects
		* FrontMark resources for text-based objects
		* Pairs of binary and meta data resources for binary objects
	* Object deletion
	* Handle [object versioning](https://github.com/apparat/apparat/blob/master/doc/VERSIONING.md) (creation of versioned object resources)
	* Handle object localization (creation of localized object resources)
	* Creation and handling of [object drafts](https://github.com/apparat/apparat/blob/master/doc/VERSIONING.md#drafts)
* Provide an abstract object interface
	* Encapsulate object resources with an abstract layer ("objects")
	* Object content getters & setters
	* Object meta data getters & setters
	* [Object references & involvement handling](https://github.com/apparat/apparat/blob/master/doc/VERSIONING.md#object-cross-references)
		* Extraction of references from text-based resources
		* Involvement signalling to other objects
* Utility function
	* [Object URL](https://github.com/apparat/apparat/blob/master/doc/VERSIONING.md#drafts) handling (composition, decomposition)
	* `apparat://` URL handling
	* Object ID assignment & incrementation

### Object repositories

Objects are stored in object repositories.

### Environment variables

Variable                       | Description
-------------------------------|------------------------------------------------------------
`APPARAT_BASE_URL`             | Absolute base URL of the apparat instance (including optional path component)
`OBJECT_DATE_PRECISION`        | Precision for creation date encoding in object URLs, ranging from `0` (no dates in URLs) to `6`q ("`Y/m/d/H/i/s`"). Typical would be `3` ("`Y/m/d`").
`OBJECT_RESOURCE_EXTENSION`    | File extension for object text resources (e.g. "`md`")

## Installation

This library requires PHP 5.6 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

## Quality

[![Build Status](https://secure.travis-ci.org/apparat/object.svg)](https://travis-ci.org/apparat/object)
[![Coverage Status](https://coveralls.io/repos/apparat/object/badge.svg?branch=master&service=github)](https://coveralls.io/github/apparat/object?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/apparat/object/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/apparat/object/?branch=master)
[![Code Climate](https://codeclimate.com/github/apparat/object/badges/gpa.svg)](https://codeclimate.com/github/apparat/object)

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
