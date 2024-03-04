
# Contao Social Tags

This extension provides support for social tags (Open Graph, Twitter Cards) for Contao CMS. It supports

- pages
- news
- events
- faqs

## Requirements

- Contao `^4.13 || ^5.3`
- PHP `^8.1`

## Changelog

See [CHANGELOG.md](CHANGELOG.md)

## Concepts

The goal of this extension is to provide a framework for common social tags for every entity being presented as a 
web page in Contao. To achieve this goal it separates the generation of the social tags into data factories and 
extractors.

### Data factories

This extension provides an abstraction for different social tags. They can be plugged in using the 
`Hofff\Contao\SocialTags\Data\DataFactory` interface which has to be tagged with the 
`Hofff\Contao\SocialTags\DataFactory` tag. The data factory is responsible to generate the social meta tags for a 
given object.

### Extractors

The extractors have to implement the interface `Hofff\Contao\SocialTags\Data\Extractor` and specifics child interfaces
for the supported data factories, e.g. `Hofff\Contao\SocialTags\Data\TwitterCards\TwitterCardsExtractor` for twitter
cards support or `Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphExtractor` for open graph support. Each extractor is
tagged with the `Hofff\Contao\SocialTags\Data\Extractor` tag.
