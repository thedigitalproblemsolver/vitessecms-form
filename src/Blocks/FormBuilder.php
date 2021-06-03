<?php declare(strict_types=1);

namespace VitesseCms\Form\Blocks;

use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Core\Services\ViewService;
use VitesseCms\Core\Utils\UiUtils;
use VitesseCms\Form\Forms\BaseForm;

class FormBuilder extends AbstractBlockModel
{
    /**
     * @var bool
     */
    public $useRecaptcha;

    /**
     * @var array
     */
    public $newsletters;

    /**
     * @var string;
     */
    public $datagroup;

    /**
     * @var array
     */
    public $name;

    /**
     * @var array
     */
    public $systemThankyou;

    public function __construct(ViewService $view)
    {
        parent::__construct($view);

        $this->useRecaptcha = false;
        $this->datagroup = '';
        $this->newsletters = [];
    }

    public function initialize()
    {
        parent::initialize();

        if ($this->di->session->get('blockSubmittedId')) :
            $this->excludeFromCache = true;
        endif;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        if ($this->di->session->get('blockSubmittedId') === (string)$block->getId()) :
            $block->set('form', $block->_('pageThankyou'));
            $this->di->session->set('blockSubmittedId', null);
        else :
            $datagroup = Datagroup::findById($block->_('datagroup'));
            if ($datagroup) :
                $form = new BaseForm();
                if ($block->_('labelColumns')) :
                    $form->setColumn((int)$block->_('labelColumns'), 'label', UiUtils::getScreens());
                endif;
                if ($block->_('inputColumns')) :
                    $form->setColumn((int)$block->_('inputColumns'), 'input', UiUtils::getScreens());
                endif;

                $datagroup->buildItemForm($form);
                $form->addHidden('block', (string) $block->getId());

                $submitText = 'Submit';
                if ($block->_('submitText')) :
                    $submitText = $block->_('submitText');
                endif;
                $form->addSubmitButton($submitText);
                //TODO handle recaptcha
                //'useRecaptcha' => $block->_('useRecaptcha'),
                //]);

                $postUrl = 'form/index/submit/';
                if ($block->_('postUrl')) :
                    $postUrl = $block->_('postUrl');
                endif;

                $block->set('form', $form->renderForm($postUrl));
            endif;
        endif;
    }

    public function isUseRecaptcha(): bool
    {
        return $this->useRecaptcha;
    }

    public function setUseRecaptcha(bool $useRecaptcha): FormBuilder
    {
        $this->useRecaptcha = $useRecaptcha;

        return $this;
    }

    public function getDatagroup(): string
    {
        return $this->datagroup;
    }

    public function setDatagroup(string $datagroup): FormBuilder
    {
        $this->datagroup = $datagroup;

        return $this;
    }

    public function getNewsletters(): array
    {
        return $this->newsletters;
    }

    public function setNewsletters(array $newsletters): FormBuilder
    {
        $this->newsletters = $newsletters;

        return $this;
    }

    public function setName(array $name): FormBuilder
    {
        $this->name = $name;

        return $this;
    }

    public function getSystemThankyouByShortCode(string $shortCode): ?string
    {
        if ($this->systemThankyou !== null && !empty($this->systemThankyou[$shortCode])) :
            return $this->systemThankyou[$shortCode];
        endif;

        return null;
    }

    public function hasSystemThankyouByShortCode(string $shortCode): bool
    {
        return $this->systemThankyou !== null && !empty($this->systemThankyou[$shortCode]);
    }

    public function setSystemThankyou(array $systemThankyou): FormBuilder
    {
        $this->systemThankyou = $systemThankyou;

        return $this;
    }
}
