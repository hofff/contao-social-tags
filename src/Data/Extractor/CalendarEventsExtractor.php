<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\Extractor;

use Contao\CalendarEventsModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Events;
use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Contao\News;
use Contao\PageModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\Extractor;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphImageData;
use Hofff\Contao\SocialTags\Data\OpenGraph\OpenGraphType;
use Hofff\Contao\SocialTags\Util\TypeUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use function explode;
use function is_file;
use function method_exists;
use function str_replace;
use function strip_tags;
use function stripos;
use function trim;
use function ucfirst;

final class CalendarEventsExtractor implements Extractor
{
    /** @var ContaoFrameworkInterface */
    private $framework;

    /** @var RequestStack */
    private $requestStack;

    /** @var string */
    private $projectDir;

    public function __construct(ContaoFrameworkInterface $framework, RequestStack $requestStack, string $projectDir)
    {
        $this->framework    = $framework;
        $this->projectDir   = $projectDir;
        $this->requestStack = $requestStack;
    }

    public function supports(Model $reference, ?Model $fallback = null) : bool
    {
        if (! $reference instanceof CalendarEventsModel) {
            return false;
        }

        if (! $fallback instanceof PageModel) {
            return false;
        }

        return true;
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

    private function extractTwitterTitle(CalendarEventsModel $eventModel) : ?string
    {
        $title = $eventModel->hofff_st_twitter_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return $this->getEventTitle($eventModel);
    }

    private function extractTwitterSite(CalendarEventsModel $evemtModel) : ?string
    {
        return $evemtModel->hofff_st_twitter_site ?: null;
    }

    private function extractTwitterDescription(CalendarEventsModel $eventModel) : ?string
    {
        if (TypeUtil::isStringWithContent($eventModel->hofff_st_twitter_description)) {
            return $this->replaceInsertTags($eventModel->hofff_st_twitter_description);
        }

        return $this->getEventDescription($eventModel) ?: null;
    }

    private function extractTwitterImage(CalendarEventsModel $calendarEventsModel) : ?string
    {
        $file = $this->framework
            ->getAdapter(FilesModel::class)
            ->findByUuid($calendarEventsModel->hofff_st_twitter_image);

        if ($file && is_file($this->projectDir . '/' . $file->path)) {
            return $this->getBaseUrl() . $file->path;
        }

        return null;
    }

    private function extractTwitterCreator(CalendarEventsModel $calendarEventsModel) : ?string
    {
        return $calendarEventsModel->hofff_st_twitter_creator ?: null;
    }

    /**
     * @param string|resource $strImage
     */
    private function extractOpenGraphImageData(CalendarEventsModel $calendarEventsModel) : OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();

        $file = FilesModel::findByUuid($calendarEventsModel->hofff_st_og_image);

        if ($file && is_file(TL_ROOT . '/' . $file->path)) {
            $objImage = new File($file->path);
            $imageData->setURL($this->getBaseUrl() . $file->path);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    private function extractOpenGraphTitle(CalendarEventsModel $calendarEventsModel) : ?string
    {
        $title = $calendarEventsModel->hofff_st_og_title;
        if (TypeUtil::isStringWithContent($title)) {
            return $this->replaceInsertTags($title);
        }

        return $this->getEventTitle($calendarEventsModel) ?: null;
    }

    private function extractOpenGraphUrl(CalendarEventsModel $calendarEventsModel) : string
    {
        if (TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_url)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_url);
        }

        if ($calendarEventsModel->id === $GLOBALS['objPage']->id) {
            return $this->getBaseUrl() . $this->getRequestUri();
        }

        $eventUrl = Events::generateEventUrl($calendarEventsModel, true);

        // Prepend scheme and host if URL is not absolute
        if (stripos($eventUrl, 'http') !== 0) {
            $eventUrl = $this->getBaseUrl() . $eventUrl;
        }

        return $eventUrl;
    }

    private function extractOpenGraphDescription(CalendarEventsModel $calendarEventsModel) : ?string
    {
        if (TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_description)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_description);
        }

        return $this->getEventDescription($calendarEventsModel) ?: null;
    }

    private function extractOpenGraphSiteName(CalendarEventsModel $calendarEventsModel, PageModel $fallback) : string
    {
        if (TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_site)) {
            return $this->replaceInsertTags($calendarEventsModel->hofff_st_og_site);
        }

        return strip_tags($fallback->rootTitle);
    }

    private function extractOpenGraphType(CalendarEventsModel $calendarEventsModel) : OpenGraphType
    {
        if (TypeUtil::isStringWithContent($calendarEventsModel->hofff_st_og_type)) {
            [$namespace, $type] = explode(' ', $calendarEventsModel->hofff_st_og_type, 2);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return new OpenGraphType('website');
    }

    private function replaceInsertTags(string $content) : string
    {
        $controller = $this->framework->getAdapter(Controller::class);

        $content = $controller->__call('replaceInsertTags', [$content, false]);
        $content = $controller->__call('replaceInsertTags', [$content, true]);

        return $content;
    }

    private function getBaseUrl() : string
    {
        static $baseUrl;

        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '';
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . '/';

        return $baseUrl;
    }

    private function getRequestUri() : string
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request) {
            return '';
        }

        return $request->getRequestUri();
    }

    /**
     * Returns the meta description if present, otherwise the shortened teaser.
     */
    private function getEventDescription(CalendarEventsModel $model) : string
    {
        if (TypeUtil::isStringWithContent($model->description)) {
            return $this->replaceInsertTags(trim(str_replace(["\n", "\r"], [' ', ''], $model->description)));
        }

        // Generate the description from the teaser the same way as the event reader does
        $description = $this->replaceInsertTags($model->teaser, false);
        $description = strip_tags($description);
        $description = str_replace("\n", ' ', $description);
        $description = StringUtil::substr($description, 320);

        return $description;
    }

    /**
     * Returns the meta title if present, otherwise the title.
     */
    private function getEventTitle(CalendarEventsModel $model) : ?string
    {
        $title = $model->pageTitle ?: $model->title;
        if (TypeUtil::isStringWithContent($title)) {	
            return $this->replaceInsertTags($title);	
        }
        
        return null;
    }
}
