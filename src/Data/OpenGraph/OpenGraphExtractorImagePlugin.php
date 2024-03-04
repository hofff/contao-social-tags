<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\Data\OpenGraph;

use Contao\File;
use Contao\FilesModel;
use Contao\PageModel;
use Hofff\Contao\SocialTags\Data\Extractor\AbstractExtractor;

/**
 * @template TReference of object
 * @template TFallback of object
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait OpenGraphExtractorImagePlugin
{
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

    abstract protected function getFileUrl(FilesModel|null $file): string|null;

    /**
     * @see AbstractExtractor::getImage()
     *
     * @param TReference $reference
     */
    abstract protected function getImage(
        string $key,
        object $reference,
        PageModel|null $fallback = null,
    ): FilesModel|null;
}
