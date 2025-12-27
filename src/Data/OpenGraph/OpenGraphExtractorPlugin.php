<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\File;
use Hofff\Contao\SocialTags\Util\TypeUtil;

use function array_pad;
use function explode;
use function strip_tags;

/**
 * @template TReference of object
 * @template TFallback of object
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait OpenGraphExtractorPlugin
{
    /** @use OpenGraphExtractorImagePlugin<TReference, TFallback> */
    use OpenGraphExtractorImagePlugin;

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphImageData(object $reference, object|null $fallback = null): OpenGraphImageData
    {
        $imageData = new OpenGraphImageData();
        $file      = $this->getImage('hofff_st_og_image', $reference, $fallback);
        $fileUrl   = $this->getFileUrl($file);

        if ($file && $fileUrl !== null) {
            $objImage = new File($file->path);
            $imageData->setURL($fileUrl);
            $imageData->setMIMEType($objImage->mime);
            $imageData->setWidth($objImage->width);
            $imageData->setHeight($objImage->height);
        }

        return $imageData;
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphTitle(object $reference, object|null $fallback = null): string
    {
        if (isset($reference->hofff_st) && TypeUtil::isStringWithContent($reference->hofff_st_og_title)) {
            return $this->replaceInsertTags($reference->hofff_st_og_title);
        }

        return $this->getContentTitle($reference);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphUrl(object $reference, object|null $fallback = null): string
    {
        if (isset($reference->hofff_st) && TypeUtil::isStringWithContent($reference->hofff_st_og_url)) {
            return $this->replaceInsertTags($reference->hofff_st_og_url);
        }

        if (isset($reference->canonicalLink)) {
            $canonical = $this->getCanonicalUrlForRequest();
            if ($canonical !== null) {
                return $canonical;
            }
        }

        return $this->getContentUrl($reference);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphDescription(object $reference, object|null $fallback = null): string|null
    {
        $description = isset($reference->hofff_st) && TypeUtil::isStringWithContent($reference->hofff_st_og_description)
            ? $reference->hofff_st_og_description
            : $this->getContentDescription($reference);

        if ($description === null) {
            return null;
        }

        return $this->replaceInsertTags($description);
    }

    /**
     * @param TReference     $reference
     * @param TFallback|null $fallback
     */
    public function extractOpenGraphSiteName(object $reference, object|null $fallback = null): string
    {
        if (isset($reference->hofff_st) && TypeUtil::isStringWithContent($reference->hofff_st_og_site)) {
            return $this->replaceInsertTags($reference->hofff_st_og_site);
        }

        return strip_tags((string) $fallback?->rootTitle);
    }

    public function extractOpenGraphType(object $reference, object|null $fallback = null): OpenGraphType
    {
        if (isset($reference->hofff_st) && TypeUtil::isStringWithContent($reference->hofff_st_og_type)) {
            [$namespace, $type] = array_pad(explode(' ', $reference->hofff_st_og_type, 2), 2, null);

            if ($type === null) {
                return new OpenGraphType($namespace);
            }

            return new OpenGraphType($type, $namespace);
        }

        return $this->defaultOpenGraphType();
    }

    /**
     * Get the content title from the reference.
     *
     * Insert tags does not have to be replaced as it has to be done by the caller of this method.
     *
     * @param TReference $reference
     */
    abstract protected function getContentTitle(object $reference): string;

    /**
     * Get the content description from the reference.
     *
     * Insert tags does not have to be replaced as it has to be done by the caller of this method.
     *
     * @param TReference $reference
     */
    abstract protected function getContentDescription(object $reference): string|null;

    /** @param TReference $reference */
    abstract protected function getContentUrl(object $reference): string;

    abstract protected function replaceInsertTags(string $value): string;

    abstract protected function getCanonicalUrlForRequest(): string|null;

    abstract protected function defaultOpenGraphType(): OpenGraphType;
}
