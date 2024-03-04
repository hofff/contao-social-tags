<?php

declare(strict_types=1);

namespace Hofff\Contao\SocialTags\EventListener\Hook;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Input;
use Contao\Model;
use Contao\ModuleModel;
use Contao\StringUtil;
use Hofff\Contao\SocialTags\Data\SocialTagsFactory;
use Symfony\Component\HttpFoundation\RequestStack;

final class CalendarEventReaderListener extends SocialTagsDataAwareListener
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

        $eventModel = $this->getEventModel($model);
        if ($eventModel) {
            $this->setSocialTagsData($this->factory->generate($eventModel));
        }

        return $result;
    }

    private function supports(ModuleModel $model): bool
    {
        return $model->type === 'eventreader';
    }

    private function getEventModel(ModuleModel $model): CalendarEventsModel|null
    {
        return CalendarEventsModel::findPublishedByParentAndIdOrAlias(
            $this->framework->getAdapter(Input::class)->get('auto_item'),
            StringUtil::deserialize($model->cal_calendar, true),
        );
    }

    private function determineModuleModel(ModuleModel $model): ModuleModel
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (
            $model->type === 'eventlist'
            && $model->cal_readerModule > 0
            && $this->framework->getAdapter(Input::class)->get('auto_item')
        ) {
            $readerModel = ModuleModel::findByPk($model->cal_readerModule);
            if ($readerModel) {
                return $readerModel;
            }
        }

        return $model;
    }
}
