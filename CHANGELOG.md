# Changelog

## Unreleased

## [1.1.2] - 2021-08-21

### Fixed

 - Strip meta tags for tags ([#21](https://github.com/hofff/contao-social-tags/pull/21)) [@fritzmg](https://github.com/fritzmg)

## [1.1.1] - 2021-06-10

### Fixed

 - Fix PHP 8 support ([#17](https://github.com/hofff/contao-social-tags/pull/17)) [@rabauss](https://github.com/rabauss)

## [1.1.0] - 2021-03-14

### Added

 - Add english translation thanks to ([#15](https://github.com/hofff/contao-social-tags/pull/8)) [@fritzmg](https://github.com/fritzmg)
 - Add an abstract extractor class
 - Fallback to news/faq/event image if non provided for og:image and twitter:image
 - Fallback to reference page image if no news/faq/event image if no og:image and twitter:image and no news/faq/event image exists
 - Fallback to twitter:site and twitter:creator from the reference page of news/faq/event if not defined 

### Changed

 - Use reference page for non page content ([#14](https://github.com/hofff/contao-social-tags/pull/14)) [@fritzmg](https://github.com/fritzmg)
 - Do not require twitter site ([#13](https://github.com/hofff/contao-social-tags/pull/13)) [@fritzmg](https://github.com/fritzmg)

### Fixed

 - Fix twitter cards default type  ([#12](https://github.com/hofff/contao-social-tags/pull/12)) [@fritzmg](https://github.com/fritzmg)
 - Fix broken url for news, events, faqs having the same id like the current page
 - Correctly detect if twitter cards are enabled

## [1.0.6] - 2020-09-11

### Fixed

 - Recognize `hofff_st` checkbox before evaluating field content for news, events and calendars
 - Use `article` as default `og:type` for news, events and calendars

## [1.0.5] - 2020-09-11

### Fixed

 - Fix type error ([#8](https://github.com/hofff/contao-social-tags/pull/8)) thanks to [@fritzmg](https://github.com/fritzmg)

### Changed

 - Do not generate xhtml ([#7](https://github.com/hofff/contao-social-tags/pull/7)) thanks to [@fritzmg](https://github.com/fritzmg)


## [1.0.4] - 2020-09-09

### Fixed

 - Fix url generation for faqs, news and events for Contao 4.4 ([#6](https://github.com/hofff/contao-social-tags/pull/6)) thank to [@fritzmg](https://github.com/fritzmg)
 - Fix cross dependencies to the news bundle in the faq and event integration ([#6](https://github.com/hofff/contao-social-tags/pull/6)) thanks to[@fritzmg](https://github.com/fritzmg)
 - Recognize `pageTitle` fields for news and events, fallback to teaser as the description ([#5](https://github.com/hofff/contao-social-tags/pull/5)) thanks to [@fritzmg](https://github.com/fritzmg)

[1.1.1]: https://github.com/hofff/contao-social-tags/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/hofff/contao-social-tags/compare/1.0.6...1.1.0
[1.0.6]: https://github.com/hofff/contao-social-tags/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/hofff/contao-social-tags/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/hofff/contao-social-tags/compare/1.0.3...1.0.4
