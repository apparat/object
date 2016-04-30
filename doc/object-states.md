Object states & modes
=====================

Object states
-------------

Each object has one of three states at a time:

1. `clean`: The **object hasn't changed** since its last persistence. This is also the state an object has immediately after its retrieval from a repository.
2. `dirty`: Each **significant change** (at least one property has to effectively change its value) will force the object into `dirty` state (at least, see below). The object needs to be persisted.
3. `mutated`: If one of the object's **content properties** gets altered, the object is forced into `mutated` state which will also trigger **draft mode** (see below).

The object content consists of

* the [Meta properties](object-properties.md#b-meta-properties),
* the [Domain properties](object-properties.md#c-domain-properties) and
* the object payload (if any).

Together these properties result in the [object hash](object-hash.md) which is part of the [System properties](object-properties.md#a-system-properties). Whenever the object hash changes, persistence will result in a new revision (draft or published mode).

Object modes
------------

Each object has one of two modes at a time:

1. `published`: An object is in **published mode** when it has been explicitly published (resulting in the `system.published` property being set to a timestamp) and there hasn't occured any content change since then.
2. `draft`: As soon as a content change occurs (see above), the object automatically falls into **draft mode**. The `system.published` property gets unset, persisting the object will result in a new [object revision](object-revisions.md). The draft mode can also be triggered explicitly.