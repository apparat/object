Apparat URIs, URLs & Selectors
==============================

Apparat base URL
----------------

As an *apparat* instance is typically meant to be accessible over the web, it is assigned a **base URL** using the environment variable `APPARAT_BASE_URL`. The base URL uses the `http` or `https` scheme and might optionally contain an authentication, port and path section. In general, the [HTTPS scheme](https://en.wikipedia.org/wiki/HTTPS) *SHOULD* be preferred. Seen from a webserver perspective, the path section (if present) *SHOULD* match an existing directory within a virtual host. 

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

An *apparat* instance consists of one or more **object repositories** that map to specific locations in the file system. Each repository has a unique identifier that — in combination with the base URL,  separated by a slash `"/"` — makes up a globally unique **repository URL**. The identifier must follow the rules for URL path sections and may optionally be empty. Seen from a webserver perspective, the repository URL's path section *SHOULD* match an existing directory within a virtual host. 

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

* (potentially) several nested directories reflecting the object's **creation date**,
* the container directory named after the **object ID** and its **type**,
* the object itself, named after its ID and the **object revision**.

Additionally, the locator may contain indicators for the **object visibility** and its **draft state**. An classic object locator looks like this:

```
/2016/06/14/238-article/238-1
\_________/ \_/ \_____/ \_/  \
     |       |     |     |    \
 Creation    |   Object  |   Object
   date   Object  type   |  revision
            ID ----------/
```

The indicators are applied like this:

```
/2016/06/14/.238-article/.238-3
            |            |
         Hidden        Draft
         object       revision
```

As soon as an object has been published for the first time, there's always one revision called the ***current*** one, identifying the most recently published state. For the *current revision*, no revision identifier is used. This way, the following locator consistently maps to the most recent "official" version of an object (no matter how many revisions have been published):

```
/2016/06/14/238-article/238
```

In fact, to unambigously address the current revision of an object, the following **canonical object locator** is just enough:


```
/2016/06/14/238
```

### Creation Date

Using creation dates for structuring a large number of objects seems to be an **intuitive** and the **most widely accepted approach**. These dates are immutable, and unlike many other category systems the calendar is a pretty stable, predictable and commonly understood system. Although [ordinal dates](https://en.wikipedia.org/wiki/Ordinal_date) would be slightly shorter, *apparat* sticks to a [Gregorian date representation](https://en.wikipedia.org/wiki/Gregorian_calendar) as the large majority of users is not familiar with ordinal dates at all.

Depending on the environment variable `OBJECT_DATE_PRECISION`, *apparat* uses between three and six nested subdirectories for expressing an object's creation date (and time), following a pattern from `YYYY/MM/DD` to `YYYY/MM/DD/HH/II/SS`.

### Object IDs

It is imaginable that multiple objects are created simultaneously (within the limits of accuracy). In order to unambiguously distinguish these objects they need to be **numbered sequentially** in some way. While it's easy to read and understand the [creation date locator part](#creation-date), any form of abstract numbering will be of little cognitive value to users. So instead of numbering the objects "locally" (within the scope of their particular creation date and time), *apparat* applies a **"global" numbering across all objects** belonging to a particular repository, turning the necessity into a feature. The absolute order of object creation will always be comprehensible regardless of the concrete creation timestamps. The object ID is used to name both the **object container directory** as well as the single object revisions.

**Question**: Naturally sorting files by object ID?

### Object types

Please see the [object summary](object-types.md) for a list of known object types.

### Object names

#### Text objects

A [text based object](object-types.md#text-objects) (e.g. an article, note, etc.) results from a raw text submission, so the object resource is created from scratch. The object name is built from

1. the automatically assigned **[object ID](#object-ids)** and
2. an optional [revision number](#object-revision), separated by a dash.

An example could be `36704-1`. The object resource will be saved using a lower-case `.md` (Markdown) file extension and also include a [language indicator](#language-indicator).

#### Media objects

In contrast, a [media object](object-types.md#media-objects) (e.g. an image, video, etc.) derives from an existing file that is submitted during publication. As the original file name might describe the object's contents and thus be of a certain value to users, it is preserved, yet normalized to comply with general URL requirements. The object name is built from

1. the **normalized original file name** (without the file extension),
2. the automatically assigned **[object ID](#object-ids)** and finally
3. an optional [revision number](#object-revision), separated by a dash.

An example could be `myphoto.36704-2`. The media file will be saved with its **original lower-case file extension** and also include a [language indicator](#language-indicator).

#### Object revision

When an object gets modified and re-published, *apparat* saves a copy of the previous instance instead of simply overwriting it with the updated revision. The latest instance will always be accessible under the canonical object URL, with the current revision number being part of the object system properties (this way, it's a very straightforward task to find out the number of revisions available). Previous revisions may be explicitly retrieved by appending a **revision number** into the object URL (as a suffix to the object ID, separated by a minus):

```
https://apparat.tools/2015/10/01/36704-event/36704-1.md
                        Identifier for revision 1 ^^
```

Also, an object may have **[draft status](object-states.md)**, which will also result in a different URL. *Apparat*'s revisioning system is [explained in detail here](object-revisions.md).

- aprt / aprts scheme


Object URL
----------

- Apparat object URL
- Full object URL


Repository selector
-------------------

- Resource selectors

___

Each object associated with an *apparat* instance is assigned a [URI](https://en.wikipedia.org/wiki/Uniform_Resource_Identifier) that basically expresses the object's **resource location within the file system**.

As *apparat* aims to impose as few requirements as possible, object URLs need to be designed deliberately. In particular, they SHOULD NOT depend on

* a routing mechanism,
* the web server's index document feature¹,
* symbolic links or
* interpreters of any kind.

Canonical object URLs widely adhere to the underlying file system and should be easily resolvable even without a web server.

1. Alternative approach: File formats and extensions [are implementation details](http://www.w3.org/Provider/Style/URI.html#hmap-4) that don't have to be transparent to the client. It is OK to use the web server layer to abstract away these details (and rely on the web server's rewrite features).

[permanent URL](https://en.wikipedia.org/wiki/Permalink)

Object URLs
-----------

There's no really compelling reason to distribute the entirety of objects over a multi-level directory structure. Doing so, however,

* makes it easier to manually find a specific object in the file system,
* keeps up the file system performance and
* helps avoiding troubles with file and directory name length limitations under certain file systems.

A typical *apparat* object URL looks like this:

	https://apparat.tools/2015/10/01/36704-event/36704

It consists of

1. a [base URL](#base-url) associated with the *apparat* instance as a whole (`https://apparat.tools/`),
2. a [repository URL](#repository-url) identifying the repository the object belongs to (might be empty),
3. up to six nested subdirectories denoting the object's [creation date (and time)](#creation-date), configurable from `YYYY/MM/DD` to `YYYY/MM/DD/HH/II/SS` and consisting of digits only,
4. a directory named after the [object ID](#object-ids) and the [object type](#object-types.md) (separated by a dash), serving as container for all object related resources,
5. and finally the [object name](#object-names) itself, consisting of the original **object file name** ([media objects](#media-objects) only), the **object ID** and potentially the [object revision](#object-revisioning) number.

```
https://apparat.tools  /  blog  /  2015/10/01  /  36704  -  image  /  36704  -  2
           ^               ^           ^            ^         ^         ^       ^
        base URL      repository    creation     object    object    object   object
                          URL         date         ID       type       ID    revision
```

### Base URL

The base URL associated with an *apparat* instance MAY inlude login credentials, a port number and / or a path component (e.g. `http://user:password@example.com:80/objects/`). 



## Object resources

Object resources use the [object name](#object-names) as first part of their file name, followed by a [lower-case file extension](#file-extensions).

```
/2015/10/01/36704-image/  36704  -  2  .  md
                            ^       ^     ^
                         object  object   file
                           ID   revision  extension
```

### File extensions

As [recommended](http://www.w3.org/Provider/Style/URI.html#hmap-4) a **canonical object URL** doesn't use a file extension since the file format (which is what file extensions usually indicate) is an implementation detail that shouldn't be mandatory for accessing and retrieving the object. When manually resolving an object URL, it will be easy to find the corresponding file as there will be no other file in the same directory with the very same name part. When resolving the URL via a web server, [content negotiation should be used](http://www.w3.org/Provider/Style/URI.html#hmap-8). The object type (also part of the object URL) will support the negotiation process.


A word on ...
-------------

### Object localizations

In general, localized object versions are considered as completely separate objects with independent URLs, creation dates and revisions. There should be cross-references between localizations that preferably support content negotiation (TBD).


### Object references

Some properties support *apparat* object references as values (e.g. `meta.authors`). References to objects

* within the same *apparat* instance take the form of root relative URLs, e.g.
  * `/2015/10/01/36704-event/36704` for an object in the same repository or
  * `/repo/2015/10/01/36704-event/36704` for an object in another registered repository (with the repository URL `repo` in this case)
* of remote *apparat* instances use the custom protocol `aprt` (respectively `aprts`) to distiguish them from regular HTTP URLs and trigger object instantiation (e.g. `aprts://apparat.tools/2015/10/01/36704-event/36704`).
