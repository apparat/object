# Apparat Objects
[![Build Status](https://secure.travis-ci.org/apparat/object.svg)](https://travis-ci.org/apparat/object)
[![Coverage Status](https://coveralls.io/repos/apparat/object/badge.svg?branch=master&service=github)](https://coveralls.io/github/apparat/object?branch=master)

Apparat object abstraction layer

# Purpose of this module

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

# Object repositories

Objects are stored in object repositories.