Object properties
=================

[Object properties](object-properties.ods) (ODS table) are aggregated into 5 property collections, of which only the first one ([system properties](#system-properties)) is mandatory:

### Property collections

#### A. System properties

* UID
* Type
* Revision
* Creation date
* Publication date
* [Hash](object-hash.md)

#### B. Meta properties

* Keywords
* Categories
* Authors
* Summary

#### C. Domain properties

Object type dependent properties.

#### D. Resource relations

Resource relations need to carry the following characteristics:

* Relation type (active / passive)
  - **refers-to / referred-by**: Regular links from / to resources (both *apparat* objects and regular HTTP links)
  - **embeds / embedded-by**: Inclusive associations like media objects (images, audio, video)
  - **replies-to / replied-by**: Responses between resources
  - **likes / liked-by**: Relationships between resources expressing sympathy or approval ("Likes")
  - **reposts / reposted-by**: Reationships between a resource reposting another resource
* Relation target
	* local (apparat) URL
	* absolute apparat URL (remote object)
	* arbitrary URL (no apparat object)
* Coupling
	* Coupled objects (only valid for apparat objects)
	* Loosely coupled

#### E. Processing instructions

* Templating variables
* Miscellaneous rendering instructions
	* Additional styles?
	* JavaScript libraries?
