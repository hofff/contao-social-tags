<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\CalendarEventsModel;
use Contao\Events;
use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\PageModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function explode;
use function is_file;
use function method_exists;
use function str_replace;
use function strip_tags;
use function stripos;
use function trim;
use function ucfirst;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class CalendarEventsExtractor extends AbstractExtractor
{
    public function supports(Model $reference, ?Model $fallback = null): bool
    {
        if (! $reference instanceof CalendarEventsModel) {
            return false;
        }

        return $fallback instanceof PageModel;
    }

    /** @return mixed */
    public function extract(string $type, string $field, Model $reference, ?Model $fallback = null)
    {
        $methodName = 'extract' . ucfirst($type) . ucfirst($field);

        if ($methodName !== __FUNCTION__ && method_exists($this, $methodName)) {
            return $this->$methodName($reference, $fallback);
        }

        return null;
    }

    private function extractTwitterTitle(CalendarEventsModel $eventModel): ?string
    {
        if ($eventModel->hofff_st && TypeUtil::isStringWithContent($eventModel->hofff_st_twitter_title)) {
            return $this->replaceInsertTags($eventModel->hofff_st_twitter_title);
        }

        return $this->getEventTitle($eventModel);
    }

    private function extractTwitterSite(CalendarEventsModel $eventModel, PageModel $referencePage): ?string
    {
        if ($eventModel->hofff_st && $eventModel->hofff_st_twitter_site) {
            return $eventModel->hofff_st_twitter_site;
        }

        return $referencePage->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(CalendarEventsModel $eventModel): ?string
    {
        if ($eventModel->hofff_st && TypeUtil::isStringWithContent($eventModel->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($eventModel->hofff_st_twitter_description);
        }

        return $this->getEventDescription($eventModel) ?: null;
    }

    private function extractTwitterImage(CalendarEventsModel $calendarEventsModel, PageModel $referencePage): ?string
    {
        $file = $this->getImage('hofff_st_twitter_image', $calendarEventsModel, $referencePage);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(CalendarEventsModel $eventModel, PageModel $referencePage): ?string
    {
        if ($eventModel->hofff_st && $eventModel->hofff_st_twitter_creator) {
            return $eventModel->hofff_st_twitter_creator;
        }

        return $referencePage->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(
        CalendarEventsModel $calendarEventsModel,
        PageModel $referencePage
    ): OpenGraphImageData {
        $imageData = new OpenGraphImageData();
        $file      = $this->getImage('hofff_st_og_image', $calendarEventsModel, $referencePage);

        if ($file && is_file(TL_ROOT . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(CalendarEventsModel $calendarEventsModel): ?string
    {
        if ($calendarEventsModel->hofff_st && TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_title)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_title);
        }

        return $this->getEventTitle($calendarEventsModel) ?: null;
    }

    private function extractOpenGraphUrl(CalendarEventsModel $calendarEventsModel): string
    {
        if ($calendarEventsModel->hofff_st && TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_url)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_url);
        }

        $eventUrl = Events::generateEventUrl($calendarEventsModel, true);

        // Prepend scheme and host if URL is not absolute
        if (stripos($eventUrl, 'http') !== 0) {
            $eventUrl = $this->getBaseUrl() . $eventUrl;
        }

        return $eventUrl;
    }

    private function extractOpenGraphDescription(CalendarEventsModel $calendarEventsModel): ?string
    {
        if (
            $calendarEventsModel->hofff_st
            && TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_description)
        ) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_description);
        }

        return $this->getEventDescription($calendarEventsModel) ?: null;
    }

    private function extractOpenGraphSiteName(CalendarEventsModel $calendarEventsModel, PageModel $fallback): string
    {
        if ($calendarEventsModel->hofff_st && TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_site)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_site);
        }

        return strip_tags($fallback->rootTitle);
    }

    private function extractOpenGraphType(CalendarEventsModel $calendarEventsModel): OpenGraphType
    {
        if ($calendarEventsModel->hofff_st && TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_type)) {
            [$namespace, $type] = array_pad(explode(' ', $calendarEventsModel->hofff_st_og_type, 2), 2, null);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('article');
    }

    /**
     * Returns the meta description if present, otherwise the shortened teaser.
     */
    private function getEventDescription(CalendarEventsModel $model): ?string
    {
        if (TypeUtil::isStringWithContent($model->description)) {
            return $this->replaceInsertTags(trim(str_replace(["\n", "\r"], [' ', ''], $model->description)));
        }

        if (! TypeUtil::isStringWithContent($model->teaser)) {
            return null;
        }

        // Generate the description from the teaser the same way as the event reader does
        $description = $this->replaceInsertTags($model->teaser ?? '', false);
        $description = strip_tags($description);
        $description = str_replace("\n", ' ', $description);
        $description = StringUtil::substr($description, 320);

        return $description;
    }

    /**
     * Returns the meta title if present, otherwise the title.
     */
    private function getEventTitle(CalendarEventsModel $model): ?string
    {
        $title = $model->pageTitle ?: $model->title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return null;
    }

    /**
     * Retrieves an image from the event for a given key. It fallbacks to the event image or page image if not defined.
     */
    private function getImage(string $key, CalendarEventsModel $eventsModel, PageModel $referencePage): ?FilesModel
    {
        $image = null;
        if ($eventsModel->hofff_st && $eventsModel->{$key}) {
            $image = $eventsModel->{$key};
        } elseif ($eventsModel->addImage && $eventsModel->singleSRC) {
            $image = $eventsModel->singleSRC;
        } elseif ($referencePage->{$key}) {
            $image = $referencePage->{$key};
        } else {
            return null;
        }

        return $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($image);
    }
}
