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
* Location
* [Hash](object-hash.md)

#### B. Meta properties

* Keywords
* Categories
* Summary
* License
* Privacy

#### C. Domain properties

Object type dependent properties.

#### D. Resource relations

Resource relations need to carry the following characteristics:

* Relation type (active / passive)
    1. **refers-to / referred-by**: Regular links from / to resources (both *apparat* objects and regular HTTP links)
    2. **embeds / embedded-by**: Inclusive associations like media objects (images, audio, video)
    3. **contributes-to** / **contributed-by**: Author / contributor relation
    4. **replies-to / replied-by**: Responses between resources
    5. **likes / liked-by**: Relationships between resources expressing sympathy or approval ("Likes")
    6. **reposts / reposted-by**: Reationships between a resource reposting another resource
* Relation target
	* local (apparat) URL
	* absolute apparat URL (remote object)
	* arbitrary URL (no apparat object)
* Coupling
	* Coupled objects (only valid for apparat objects)
	* Loosely coupled

The relation types **refers-to** and **embeds** cannot be set intentionally but are derived from the object payload. They may be extracted from the object content at any time and are listed here only for convenience reasons.

The relation type **contributed-by** may be an extended author relation including a name and email address

#### E. Processing instructions

* Templating variables
* Miscellaneous rendering instructions
	* Additional styles?
	* JavaScript libraries?
