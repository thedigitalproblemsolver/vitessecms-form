<?php
declare(strict_types=1);

namespace VitesseCms\Form\Listeners\Blocks;

use Phalcon\Events\Event;
use VitesseCms\Block\Forms\BlockForm;
use VitesseCms\Block\Models\Block;
use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class  BlockFormBuilderListener
{
    /**
     * @var DatagroupRepository
     */
    private $datagroupRepository;

    /**
     * @var NewsletterRepository
     */
    private $newsletterRepository;

    public function __construct(
        DatagroupRepository $datagroupRepository,
        NewsletterRepository $newsletterRepository
    ) {
        $this->datagroupRepository = $datagroupRepository;
        $this->newsletterRepository = $newsletterRepository;
    }

    public function buildBlockForm(Event $event, BlockForm $form): void
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
                ->setOptions(
                    ElementHelper::modelIteratorToOptions(
                        $this->datagroupRepository->findAll(
                            new FindValueIterator(
                                [new FindValue('component', 'form')]
                            )
                        )
                    )
                )
        //TODO seperate this field to own module
        )->addDropdown(
            'Add to newsletter',
            'newsletters',
            (new Attributes())
                ->setMultilang(true)
                ->setMultiple(true)
                ->setOptions(
                    ElementHelper::modelIteratorToOptions(
                        $this->newsletterRepository->findAll(
                            new FindValueIterator(
                                [new FindValue('parentId', null)]
                            )
                        )
                    )
                )
                ->setInputClass('select2')
        )->addText(
            '%ADMIN_FORM_SUBMIT_BUTTON_TEXT%',
            'submitText',
            (new Attributes())->setRequired(true)->setMultilang(true)
        )->addToggle('use reCaptcha', 'useRecaptcha');
    }

    public function loadAssets(Event $event, FormBuilder $formBuilder, Block $block): void
    {
        if ($block->getBool(('useRecaptcha'))) :
            $block->getDi()->get('assets')->loadRecaptcha();
        endif;
    }
}