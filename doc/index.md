# About apparat/object

Purpose of this module:

1. **Mapping between objects and their file resources**
	* Instantiation of objects from persisted resources
	* Creation of resources from new / unpersisted objects
		* FrontMark resources for text-based objects
		* Pairs of binary and meta data resources for binary objects
	* Object deletion and un-deletion
	* [Object revision](object-revisions.md) management
	* Object ID assignment & incrementation
	* [Object hash](object-hash.md) management
	* Object localization (?)
2. **Object API**
    * Implementation of different [object types](object-types.md)
	* [Object property](object-properties.md) getters & setters
	* Object payload getters & setters
	* Handling of [object states](object-states.md)
	* Handling of object privacy
	* [Object references & involvement handling](object-revisions.md#object-cross-references)
		* Extraction of references from text-based resources
		* Involvement signalling to other objects
3. **Utility functions**
	* [Object URL](object-url.md) handling (composition, decomposition)
	* `aprt://` and `aprts://` URL handling

# Object repositories

Objects are stored in object repositories.

# Environment variables

Variable                       | Description
-------------------------------|------------------------------------------------------------
`APPARAT_BASE_URL`             | Absolute base URL of the apparat instance (including optional path component)
`APPARAT_DOCUMENT_ROOT`        | Absolute root directory in the file system for apparat repositories
`OBJECT_DATE_PRECISION`        | Precision for creation date encoding in object URLs, ranging from `0` (no dates in URLs) to `6`q ("`Y/m/d/H/i/s`"). Typical would be `3` ("`Y/m/d`").
`OBJECT_RESOURCE_EXTENSION`    | File extension for object text resources (e.g. "`md`")

# Documentation

I recommend reading [the project documentation](http://apparat-object.readthedocs.io/) on *Read the Docs*.

[![Documentation Status](https://readthedocs.org/projects/apparat-object/badge/?version=latest)](http://apparat-object.readthedocs.io/en/latest/?badge=latest)
