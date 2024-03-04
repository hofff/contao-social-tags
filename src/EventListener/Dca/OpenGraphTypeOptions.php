<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Dca;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function array_pad;
use function explode;
use function str_contains;

#[AsCallback('tl_calendar_events', 'fields.hofff_st_og_type.options')]
#[AsCallback('tl_faq', 'fields.hofff_st_og_type.options')]
#[AsCallback('tl_news', 'fields.hofff_st_og_type.options')]
#[AsCallback('tl_page', 'fields.hofff_st_og_type.options')]
final class OpenGraphTypeOptions
{
    /** @param string[] $types */
    public function __construct(private readonly array $types)
    {
    }

    /** @return array<string, list<string>> */
    public function __invoke(): array
    {
        $options = [];
        $custom  = [];

        foreach ($this->types as $type) {
            if (! str_contains($type, ' ')) {
                /** @psalm-var string $group */
                [$group, $name] = array_pad(explode('.', $type), 2, null);

                if (! TypeUtil::isStringWithContent($name)) {
                    $group = 'general';
                }

                $options[$group][] = $type;
            } else {
                $custom[] = $type;
            }
        }

        if ($custom !== []) {
            $options['custom'] = $custom;
        }

        return $options;
    }
}
