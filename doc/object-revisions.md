Object revisions
================

Instead of simply overwriting objects during re-publication, *apparat* uses a revisioning strategy that is best explained with the help of some examples.


Revisioning basics — a simple text object
------------------------------------------

Imagine the **initial publication** of a note. An object file `123.md` is created with its `revision` system property set to `1`. Because it's published, this revision is considered **immutable**.

When the note gets updated afterwards, it depends on the affected properties whether the object falls into **draft mode** or not (see [object states](object-states.md) for details). When a draft gets spawned and persisted without being published at the same time, a `+` symbol will be added to the object resource name to indicate that this is an unpublished new revision. The draft will be saved as `123+.md`, with its `revision` system property incremented to `2`, while the canonical object URL will still reference the latest published revision.

When the draft gets finally published,

1. a revision number is added to the resource name of the latest published revision (e.g. `123-1.md`) and the resource file is renamed accordingly.
2. the draft resource gets renamed to `123.md`, so that it's now referenced by the canonical object URL. It looses it's draft state and is considered immutable again.

This way the most recent published object revision is always accessible under the very same canonical URL, with each instance having a complete list of its predecessors stored in its meta data.

A specific object revision may be retrieved by inserting the desired revision number into the canonical URL. By inserting the `+` symbol one can explicitly create-retrieve the current draft revision.


Object cross references
-----------------------

As soon as there are multiple objects with references to each other (e.g. an article that embeds images), the situation gets more complex. The revisioning strategy in this case partly depends on whether the objects have been created in one go.


### Independent objects

Imagine you create an object, say an image, and then some time later you create an article that embeds this image. In this case the objects are considered **"independent"** or **"loosely coupled"** as the image existed prior to and independently from the article.

Except the article explicitely references a particular revision of the image (by using a revision identifier in the image URL), a **dynamic reference** between the article and the most recent image revision will be established. When the image gets updated, the article will automatically point to the updated image revision. The article's list of referenced objects doesn't have to be updated. However, the article has to be removed from the second last image's list of referencing objects as the article doesn't point to this instance anymore.

### Coupled objects

In case several linked objects are created in one go, say an article and some embedded images, these objects are considered **tightly coupled**. A separate object will be created for each of them, but their special connection will be recorded in their meta data. Initially they are pointing to each others' most recent revisions, but as soon as one of them gets updated, the referencing / referenced objects will be hard-wired to the shelved revision.

For instance, if an newly created article embeds image `/2015/10/02/12345/image.12345.jpg`, and the  image gets updated later, the articles will be rewritten to reference `/2015/10/02/12345/image.12345-1.jpg` instead, loosing the dynamic coupling with the latest image revision. Also, if the article itself gets updated, the images will be informed that the are no longer being referenced by the most recent article revision but its revision `1` instead.


Drafts
------

It is possible to modify an object without immediately publishing it. For this purpose, exactly one **unpublished draft** of the object may exist. A draft's revision number is always one higher than the last published object revision and the draft may be accessed with `+` as revision identifier. Every time an object is updated, *apparat* is looking for an existing draft and updates that on in case it exists.


Revision flow
-------------

![Object revisions & paths](object-revisions.png)

https://www.websequencediagrams.com/

```
note left of Draft
    Object path has
    draft indicator
end note
loop
    Draft-->Draft: Modification\nPersisting
end
Draft->Draft: Publication
Draft->Current: Persisting
note over Current
    Object path is current
    (skips revision number)
end note
loop
    Current-->Current: Modification\nPersisting
end
Current->Current:Mutation
```