### Object hash

A SHA1 hash is used as object checksum to clearly identify particular objects and its revisions. It's generated from normalized versions of

* the [meta properties](#b-meta-properties),
* the [domain properties](#c-domain-properties) and
* the object payload.

Other properties aren't used for the hash for different reasons:

* The [system properties](#a-system-properties) contain descriptive properties including the hash that are not related to the objects content.
* The [resource relations](#d-resource-relations) are either incoming (which doesn't have any effect on the object) or already covered by the object payload.
* The [processing instructions](#e-processing-instructions) are serving display purposes only and don't tell anything about the object content.
