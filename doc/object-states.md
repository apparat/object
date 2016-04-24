Object states
=============

Each object has one of three states at a time:

* `clean`: The object hasn't changed since its last persistence.
* `dirty`: At least one property of the object (or the payload) has been set and the object has potentially been altered.
* `mutated`: The object's contents have been altered and needs to be persisted.

As soon as an object reaches the `mutated` state, it's automatically converted into a [draft revision](object-revisions.md#drafts) (if it's not already a draft) and will result in a new object resource when persisted.

It's important to understand that new revisions are only created when the **content of an object is altered** (i.e. when the [object hash](object-hash.md) changes). Not all kinds of changes affect the object hash though. Examples:

* Adding an object author does't change the object's content (as long as the author is not somehow included in the object's payload, which would be a content change)
* Changes in external involvements don't affect the object's content.

