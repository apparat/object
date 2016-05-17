# apparat/object

Purpose of this module:

1. **Mapping between objects and their file resources**
	* Instantiation of objects from persisted resources
	* Creation of resources from new / unpersisted objects
		* FrontMark resources for text-based objects
		* Pairs of binary and meta data resources for binary objects
	* Object publication, un-publication and re-publication
	* [Object revision](doc/object-revisions.md) management
	* Object ID assignment & incrementation
	* Object localization (?)
2. **Object API**
    * Implementation of different [object types](doc/object-types.md)
	* [Object property](doc/object-properties.md) getters & setters
	* Object payload getters & setters
	* Handling of [object states](doc/object-states.md)
	* Handling of object privacy
	* [Object references & involvement handling](doc/object-revisions.md#object-cross-references)
		* Extraction of references from text-based resources
		* Involvement signalling to other objects
3. **Utility functions**
	* [Object URL](doc/object-url.md) handling (composition, decomposition)
	* `aprt://` and `aprts://` URL handling

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. I recommend [reading it](http://apparat-object.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP 5.6 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

## Quality

[![Build Status](https://secure.travis-ci.org/apparat/object.svg)](https://travis-ci.org/apparat/object)
[![Coverage Status](https://coveralls.io/repos/apparat/object/badge.svg?branch=master&service=github)](https://coveralls.io/github/apparat/object?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/apparat/object/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/apparat/object/?branch=master)
[![Code Climate](https://codeclimate.com/github/apparat/object/badges/gpa.svg)](https://codeclimate.com/github/apparat/object)
[![Documentation Status](https://readthedocs.org/projects/apparat-object/badge/?version=latest)](http://apparat-object.readthedocs.io/en/latest/?badge=latest)

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
