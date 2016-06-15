Apparat identifiers, URLs & selectors
=====================================

Apparat base URL
----------------

As an *apparat* instance is typically meant to be accessible over the web, it is assigned a **base URL** with the environment variable `APPARAT_BASE_URL`. The base URL uses the `http` or (preferably) `https` scheme and might optionally contain an authentication, port and path section. Seen from the webserver perspective, the path section (if present) *SHOULD* match an existing directory within a virtual host. 

### Typical example
```
http://apparat.example.com
```
### Full example
```
 https://john:doe@apparat.example.com:8080/blog
 \___/   \__/ \_/ \_________________/ \__/\___/
   |      |    |           |           |    |
Scheme    | Password      Host       Port  Path
        User
```

Repository URL
--------------

An *apparat* instance consists of one or more **object repositories** that map to specific locations on the file system. Each repository has a unique identifier that — in combination with the base URL,  separated by a slash `"/"` — makes up a globally unique **repository URL**. The identifier must follow the rules for URL path sections and may optionally be empty. Seen from the webserver perspective, the repository URL's path section *SHOULD* match an existing directory within a virtual host. 

### Typical example (with empty repository identifier)
```
http://apparat.example.com/
```
### Full example
```
https://apparat.example.com/blog
\___/   \_________________/ \__/
  |              |            |
Scheme          Host    Repository identifier
```

Object locator
--------------

An *apparat* object consists of potentially multiple file resources stored in a common container directory. An **object locator** identifies a [single object revision](object-revisions.md) and reflects its resource location on the file system (relative to the repository root directory). The locator is built as follows:

* (potentially) several nested directories reflecting the object's **[creation date](#object-creation-date "Object creation dates")**,
* the container directory named after the **[object ID](#object-id "Object IDs")** and its **[type](#object-type "Object types")**
* and finally the **[object instance](#object-instance "Object instances")**, containing the **[object revision](#object-revision "Object revisions")**.

Additionally, the locator may contain indicators for the **object visibility** and its **draft state**. A classic object locator looks like this:

```
/2016/06/14/238-article/238-1
\_________/ \_/ \_____/ \___/
     |       |     |      |
 Creation    |   Object  Object
   date   Object  type  instance
            ID
```

The indicators are applied like this:

```
/2016/06/14/.238-article/.238-3
            |            |
         Hidden        Draft
         object      indicator
```

As soon as an object has been published for the first time, there's always one revision called the ***current*** one, identifying the most recently published version. For the *current revision*, no revision identifier is used. This way, the following locator consistently maps to the most recent "official" version of an object (no matter how many revisions have been published earlier):

```
/2016/06/14/238-article/238
```

In fact, to unambigously address the current revision of an object, the following **canonical object locator** can be used:


```
/2016/06/14/238
```

The single locator components will be discussed in detail below.

### Object creation date

Using creation dates for structuring a large number of objects seems to be an **intuitive** and the **most widely accepted approach**. These dates are immutable, and unlike many other category systems the calendar is a pretty stable, predictable and commonly understood system. Although [ordinal dates](https://en.wikipedia.org/wiki/Ordinal_date) would be slightly shorter, *apparat* sticks to a [Gregorian date representation](https://en.wikipedia.org/wiki/Gregorian_calendar) as the large majority of users is not familiar with ordinal dates at all.

Depending on the environment variable `OBJECT_DATE_PRECISION`, *apparat* uses between three and six nested subdirectories for expressing an object's creation date (and time), following a pattern between `YYYY/MM/DD` and `YYYY/MM/DD/HH/II/SS`.

### Object ID

It is imaginable that multiple objects are created simultaneously (within the limits of accuracy). In order to unambiguously distinguish these objects they need to be **numbered sequentially** in some way. While the [creation date locator part](#creation-date) is easy to read and understand, any form of abstract numbering will be of little cognitive value to users. So instead of numbering the objects "locally" (within the scope of their particular creation date and time), *apparat* applies a **"global" numbering across all objects** belonging to that very repository, turning the necessity into a feature. The absolute order of object creation will always be comprehensible regardless of the concrete creation timestamps.

The object ID is both part of the **container directory name** as well as the **object resource filename**.

**Question**: Naturally sorting files by object ID?

### Object type

*Apparat* supports a list of native [object types](object-types.md) that aim to be largely compatible with [IndieWeb](http://indiewebcamp.com) / [Microformat2](http://microformats.org/wiki/microformats2) concepts.

#### Text objects

* [ ] address
* [x] article
* [ ] bookmark
* [ ] checkin
* [ ] cite
* [ ] code
* [x] contact
* [ ] event
* [ ] favourite
* [ ] geo
* [ ] item
* [ ] like
* [ ] note
* [ ] project
* [ ] reply
* [ ] repost
* [ ] review
* [ ] rsvp
* [ ] venue

#### Binary objects

* [ ] audio
* [ ] image
* [ ] video

Although the object type is not strictly required for identifying an object (the object ID would suffice), it is still part of the container directory name. This way, *apparat* can quickly find and select objects by type.


### Object instance

An object instance name consists the [object ID](#object-id "Object IDs") and a [revision identifier](#object-revision "Object revisions"). It corresponds to a like-named [flat file resource](#object-resource "Object resources") stored in the object's container directory.

```
238-1
\_/  \
 |  Revision identifier
 |
Object ID
```

An optional [draft indicator](#draft-indicator) (a leading dot `"."`) signals that the instance has not been published yet. There may only be one draft per object.


```
.238-3
|     \
|    Will be published as revision 3
|
Draft indicator
```

The *current revision* (i.e. the most recently published one) doesn't use a revision identifier and consists of the [object ID](#object-only "Object IDs") only:

```
238
```

*Apparat*'s revisioning strategy is [explained in detail here](object-revisions.md).


Object URL
----------

The combination of an [object locator](#object-locator) and the [repository URL](#repository-url) makes up an **object URL**, which is the address an object can be accessed at from the outer world. 

### Canonical object URL

```
http://apparat.example.com/2016/06/14/238
```

### Full object URL (including type and revision)

```
http://apparat.example.com/2016/06/14/238-article/238-3
```

To explicitly express that a URL references a foreign *apparat* object, *apparat* internally uses the (non-registered) `aprt` or `aprts` scheme, which transparently maps to `HTTP` respectively `HTTPS` as network access protocol.

```
aprts://apparat.example.com/2016/06/14/238
```

## Object resource

Object resources — the flat files containing object revision data — use the [object instance](#object-instance) as first part of their file name, followed by a [lower-case file extension](#file-extensions).

```
238-1.md
\___/ \/
  |    \
  |    Lower case file extension
Object
instance
```

### File extension

As [recommended](http://www.w3.org/Provider/Style/URI.html#hmap-4), [object URLs](#object-url "Object URLs") don't use a file extension since the file format (which is what file extensions usually indicate) is an implementation detail that shouldn't be required to know for accessing and retrieving an object. When an object URL is resolved manually, it will be easy to figure out the corresponding file as there will be no other file in the container directory with the very same name part. When an object is accessed via a web server, the repository API implementation will take care of dealing with the file extension. 


Repository selector
-------------------

Repository selectors are strings used for selecting one or more objects from a repository. They share the structure of [object locators](#object-locator) but may contain **wildcards** (`"*"`) for most of their parts. There is a special syntax for dealing with the visibility and draft status of objects (see below). Selector components can be omitted from the right to the left and are treated like wildcards in case they're missing. Parts in the middle of a selector may not be ommited but can be given as wildcard instead. If no revision identifier is given, the *current revision* is assumed and returned (exception if the draft indicator is used).

Using a web interface, repository selectors are appended to the [repository URL](#repository-url). Internally, *apparat* slightly rewrites the selectors in some cases and then use them as [glob patterns](https://en.wikipedia.org/wiki/Glob_(programming) for specifying sets of objects (respectively object resources):

Selector                                             | Description
-----------------------------------------------------|---------------------------------------
`/*`                                                 | Matches all objects in a repository   
`/2016/*`                                            | Matches all objects initially created in 2016
`/2016/06/15/*`                                      | Matches all objects initially created in June 2016
`/2016/06/15/238`                                    | Matches the object with the ID 238 (equivalent to `/2016/06/15/238-*`, `/2016/06/15/238-article`, `/2016/06/15/238-*/238`, `/2016/06/15/238-article/238`)
`/2016/*/*/*-article`                                | Matches all articles created in 2016
`/2016/*/*/238-article/238-3`                        | Matches revision 3 of object 238
`/2016/06/15/.238`                                   | Matches the object 238 in case it's hidden
`/2016/06/15/~238`                                   | Matches the object 238 no matter if it's hidden or not
`/2016/06/15/238-article/.238`                       | Matches the draft of object 238 if present
`/2016/06/15/238-article/.238-2`                     | Matches the draft of object 238 only if it's revision 2
`/2016/06/15/238-article/~238`                       | Matches the draft (preferred) or the current revision of object 238

Be aware that matching a hidden object may not automatically result in the object being accessible. Whether you're allowed to access the object or not may depend on further aspects, e.g. your login status. 


A word on ...
=============

General reasoning
-----------------

First of all, there's no really compelling reason to distribute the entirety of objects over a multi-level directory structure. Doing so, however,

* makes it easier to manually find a specific object in the file system,
* keeps up the file system performance and
* helps avoiding troubles with file and directory name length limitations under certain file systems.

As *apparat* aims to impose as few requirements as possible, object URLs need to be designed deliberately. In particular, they SHOULD NOT depend on

* a complex routing / rewriting mechanism,
* the web server's index document feature¹,
* symbolic links or
* interpreters introducing a non-straightforward logic.

Object URLs widely adhere to the underlying file system and should be easily resolvable even without a web server.

1. Alternative approach: File formats and extensions [are implementation details](http://www.w3.org/Provider/Style/URI.html#hmap-4) that don't have to be transparent to the client. It is OK to use the web server layer to abstract away these details (and rely on the web server's rewrite features).


Object localizations
--------------------

In general, localized object variants are treated as completely separate objects with independent URLs, creation dates and revisions. There should be cross-references between localizations that preferably support content negotiation (TBD).


Object references
-----------------

Some object properties support *apparat* object references as values (e.g. `meta.authors`). References to objects

* within the same *apparat* instance take the form of root relative URLs, e.g.
  * `/2015/10/01/36704-event/36704` for an object in the same repository or
  * `/repo/2015/10/01/36704-event/36704` for an object in another registered repository (with the **repository identifier** `repo` in this case)
* of remote *apparat* instances use the non-registered protocol `aprt` (respectively `aprts`) to distiguish them from regular HTTP / HTTPS URLs and trigger object instantiation on loading (e.g. `aprts://apparat.example.com/2015/10/01/36704`).
