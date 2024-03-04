# Update 

## Upgrade from 1.1.3 to 2.0.0

## Changes

### Rework of extractors

The interface `Hofff\Contao\SocialTags\Data\Extractor` got changed. It only provides a supports() method. For every data
type, there has to be implemented interface. Right now there are the interfaces `\Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor` and
`\Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor`.


```php

// Hofff\Contao\SocialTags\Data\Extractor

public function supports(object $reference, object|null $fallback = null): bool;

public function extract(
    string $type,
    string $field,
    object $reference,
    object|null $fallback = null,
): Data|string|null;

```

### Changed signature for data factories

Data factories signature has changed to `public function generate(object $reference, object|null $fallback = null): Data;`.

Each method required for a specific social tag is part of the interface now which provides more robust type safe 
implementations. 

### Changed signature of the social tags factory

The social tags factory method `generateByModel` got replaced by a generic `generate` method to reflect the new 
option to generate social tags for any type of object.

## Removed

 - The `Hofff\Contao\SocialTags\Data\Extractor\CompositeExtractor` got removed. Use the new introduced
   `Hofff\Contao\SocialTags\Data\ExtractorResolver` instead
