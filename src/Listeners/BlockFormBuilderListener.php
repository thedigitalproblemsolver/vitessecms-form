<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Block\Forms\BlockForm;
use VitesseCms\Block\Models\Block;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Media\Enums\AssetsEnum;

class  BlockFormBuilderListener {
    public function buildBlockForm(Event $event, BlockForm $form, Block $block): void
    {
        $form->addEditor(
            '%ADMIN_INTROTEXT%',
            'introtext',
            (new Attributes())->setMultilang(true)
        )->addEditor(
            '%ADMIN_FORM_THANKYOU_MESSAGE%',
            'pageThankyou',
            (new Attributes())->setRequired(true)->setMultilang(true)
        )->addText(
            '%ADMIN_SYSTEM_MESSAGE%',
            'systemThankyou',
            (new Attributes())->setMultilang(true)
        )->addText(
            '%ADMIN_FORM_INPUT_COLUMN_CSS_CLASS%',
            'inputColumns'
        )->addText(
            '%ADMIN_FORM_LABEL_COLUMN_CSS_CLASS%',
            'labelColumns'
        )->addUrl(
            'Alternative post url',
            'postUrl'
        )->addDropdown(
            '%ADMIN_DATAGROUP%',
            'datagroup',
            (new Attributes())
                ->setRequired(true)
                ->setOptions(ElementHelper::modelIteratorToOptions(
                    $block->getDI()->get('repositories')->datagroup->findAll(new FindValueIterator(
                        [new FindValue('component', 'form')]
                    ))
                ))
        )->addDropdown(
            'Add to newsletter',
            'newsletters',
            (new Attributes())
                ->setMultilang(true)
                ->setMultiple(true)
                ->setOptions(ElementHelper::modelIteratorToOptions(
                    $block->getDI()->get('repositories')->newsletter->findAll(new FindValueIterator(
                        [new FindValue('parentId', null)]
                    ))))
                ->setInputClass(AssetsEnum::SELECT2)
        )->addText(
            '%ADMIN_FORM_SUBMIT_BUTTON_TEXT%',
            'submitText',
            (new Attributes())->setRequired(true)->setMultilang(true)
        )->addToggle('use reCaptcha', 'useRecaptcha');
    }
}