<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\FaqModel;
use Contao\Input;
use Contao\Model;
use Contao\ModuleModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\SocialTagsFactory;
use Symfony\Component\HttpFoundation\RequestStack;

final class FaqReaderListener extends SocialTagsDataAwareListener
{
    public function __construct(
        RequestStack $requestStack,
        private readonly SocialTagsFactory $factory,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly ContaoFramework $framework,
    ) {
        parent::__construct($requestStack);
    }

    public function onGetContentElement(Model $model, string $result): string
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

    public function onGetFrontendModule(ModuleModel $model, string $result): string
    {
        $request = $this->requestStack->getMainRequest();
        if (! $request || ! $this->scopeMatcher->isFrontendRequest($request)) {
            return $result;
        }

        $model = $this->determineModuleModel($model);

        if (! $this->supports($model) || $this->getSocialTagsData()) {
            return $result;
        }

        $newsModel = $this->getFaqModel($model);
        if ($newsModel) {
            $this->setSocialTagsData($this->factory->generateByModel($newsModel));
        }

        return $result;
    }

    private function supports(ModuleModel $model): bool
    {
        return $model->type === 'faqreader';
    }

    private function getFaqModel(ModuleModel $model): FaqModel|null
    {
        return FaqModel::findPublishedByParentAndIdOrAlias(
            $this->framework->getAdapter(Input::class)->get('items'),
            StringUtil::deserialize($model->faq_categories, true),
        );
    }

    private function determineModuleModel(ModuleModel $model): ModuleModel
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (
            $model->type === 'faqlist'
            && $model->faq_readerModule > 0
            && $this->framework->getAdapter(Input::class)->get('items')
        ) {
            $readerModel = ModuleModel::findByPk($model->faq_readerModule);
            if ($readerModel) {
                return $readerModel;
            }
        }

        return $model;
    }
}
