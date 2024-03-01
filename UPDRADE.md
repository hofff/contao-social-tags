# Update 

## Upgrade from 1.1.3 to 2.0.0

## Changes

### Rework of extractors

The interface `Hofff\Contao\SocialTags\Data\Extractor` got changed. It only provides supports() method. For every data
type, there has to be implemented another `\Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor` or
`\Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor` interface.


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

Each method required for a specific social tag is part of the interface now which provides more robust type safe 
implementations. 

## Removed

 - The `Hofff\Contao\SocialTags\Data\Extractor\CompositeExtractor` got removed. Use the new introduced
   `Hofff\Contao\SocialTags\Data\ExtractorResolver` instead
