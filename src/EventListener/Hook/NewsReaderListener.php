<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Input;
use Contao\Model;
use Contao\ModuleModel;
use Contao\NewsModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\SocialTagsFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use const VERSION;
use function version_compare;

final class NewsReaderListener extends SocialTagsDataAwareListener
{
    /** @var SocialTagsFactory */
    private $factory;

    /** @var ScopeMatcher */
    private $scopeMatcher;

    /** @var ContaoFrameworkInterface */
    private $framework;

    public function __construct(
        RequestStack $requestStack,
        SocialTagsFactory $factory,
        ScopeMatcher $scopeMatcher,
        ContaoFrameworkInterface $framework
    ) {
        parent::__construct($requestStack);

        $this->factory      = $factory;
        $this->scopeMatcher = $scopeMatcher;
        $this->framework    = $framework;
    }

    public function onGetContentElement(Model $model, string $result) : string
    {
        if ($model->type !== 'module') {
            return $result;
        }

        $module = ModuleModel::findByPk($model->module);
        if (! $module) {
            return $result;
        }

        return $this->onGetFrontendModule($module, $result);
    }

    public function onGetFrontendModule(ModuleModel $model, string $result) : string
    {
        $request = $this->requestStack->getMasterRequest();
        if (! $request || ! $this->scopeMatcher->isFrontendRequest($request)) {
            return $result;
        }

        $model = $this->determineModuleModel($model);

        if (! $this->supports($model) || $this->getSocialTagsData()) {
            return $result;
        }

        $newsModel = $this->getNewsModel($model);
        if ($newsModel) {
            $this->setSocialTagsData($this->factory->generateByModel($newsModel));
        }

        return $result;
    }

    private function supports(ModuleModel $model) : bool
    {
        return $model->type === 'newsreader';
    }

    private function getNewsModel(ModuleModel $model) : ?NewsModel
    {
        return NewsModel::findPublishedByParentAndIdOrAlias(
            $this->framework->getAdapter(Input::class)->get('items'),
            StringUtil::deserialize($model->news_archives, true)
        );
    }

    private function determineModuleModel(ModuleModel $model): ModuleModel
    {
        if (($model->type === 'newsarchive' || (version_compare(VERSION, '4.7', '>=') && $model->type === 'newslist'))
            && $model->news_readerModule > 0
            && $this->framework->getAdapter(Input::class)->get('items')
        ) {
            $readerModel = ModuleModel::findByPk($model->news_readerModule);
            if ($readerModel) {
                return $readerModel;
            }
        }

        return $model;
    }
}
