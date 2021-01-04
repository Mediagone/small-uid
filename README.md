# Common SmallUID

⚠️ _This project is in experimental phase, the API may change any time._

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

SmallUIDs are short unique identifiers especially designed to be used as efficient database _Primary Key_.

- Lexicographically sortable
- Half smaller than UUID / ULID (64-bit only)
- Encodable as a short user-friendly and URL safe string


## Installation

This package requires **PHP (64-bit) 7.4+**

Add it as Composer dependency:
```sh
$ composer require mediagone/common-smalluid
```


## Introduction

UUIDs are frequently used as database _Primary Key_ in software development. However, they aren't the best choice mainly due to their random sorting and the resulting fragmentation in databases indexes. Using [ULID](https://github.com/ulid/spec) is generally a very good alternative, solving most of UUID flaws.

_SmallUIDs_ are also an ideal alternative **when you do not need as much uniqueness and require shorter "user-friendly" encoded strings**. They are _64-bit_ integers (_44-bit_ timestamp followed by _20 random bits_):

    |-----------------------|  |------------|
            Timestamp            Randomness
             44 bits               20 bits


The random number suffix still guarantees a decent amount of uniqueness when many ids are created in the same millisecond (up to 1,048,576 different values).


## Examples of usage

### Generating SMUID

```php
$uid = SmallUid::random();  // e.g. 01AN4Z07BY79KA1307SR9X4MV3
$uid = SmallUid::fromString($base62String);
$uid = SmallUid::fromBinary($binaryString);
```



## Sorting

Because of the sequential timestamp, SMUIDs are naturally sorted chronologically. It **improves indexing** when inserting values in databases, new ids being appended to the end of the table without reshuffling existing data (see more [in this article](https://www.codeproject.com/Articles/388157/GUIDs-as-fast-primary-keys-under-multiple-database)).

However, **sort order within the same millisecond is not guaranteed** because of the random suffix.


## License

_Common SmallUID_ is licensed under MIT license. See LICENSE file.


[ico-version]: https://img.shields.io/packagist/v/mediagone/common-smalluid.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/common-smalluid.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-packagist]: https://packagist.org/packages/mediagone/common-smalluid
[link-downloads]: https://packagist.org/packages/mediagone/common-smalluid
