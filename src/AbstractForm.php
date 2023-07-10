<?php declare(strict_types=1);

namespace VitesseCms\Form;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Form;
use Phalcon\Http\Request;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Helpers\ElementUiHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Language\Repositories\LanguageRepository;
use VitesseCms\User\Models\PermissionRole;

abstract class AbstractForm extends Form implements AbstractFormInterface
{
    /**
     * @var array
     */
    protected $labelCol;

    /**
     * @var array
     */
    protected $inputCol;

    /**
     * @var string
     */
    protected $ajaxFunction;

    /**
     * @var string
     */
    protected $formClass;

    /**
     * @var string
     */
    protected $formTemplate;

    /**
     * @var bool
     */
    protected $labelAsPlaceholder;

    /**
     * @var ElementUiHelper
     */
    protected $elementUiHelper;

    public function __construct($entity = null, array $userOptions = [])
    {
        parent::__construct($entity, $userOptions);

        $this->labelCol = ['xs' => 12, 'sm' => 12, 'md' => 4, 'lg' => 3, 'xl' => 3];
        $this->inputCol = ['xs' => 12, 'sm' => 12, 'md' => 8, 'lg' => 9, 'xl' => 9];
        $this->formTemplate = 'form';
        $this->labelAsPlaceholder = false;
        $this->elementUiHelper = new ElementUiHelper(new LanguageRepository());
    }

    public function addSubmitButton(string $label, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;

        $this->add($this->form->elementFactory->submitButton($label, (array)$attributes));

        return $this;
    }

    public function addEmptyButton(string $label): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->emptyButton($label));

        return $this;
    }

    public function addButton(string $label, string $name): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->button($label, $name));

        return $this;
    }

    public function addToggle(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setTemplate('checkbox_toggle');

        $this->assets->loadBootstrapToggle();
        $this->add($this->form->elementFactory->checkbox($label, $name, (array)$attributes));

        return $this;
    }

    public function addNumber(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputType('number');

        $this->add($this->form->elementFactory->number($label, $name, (array)$attributes));

        return $this;
    }

    public function addPhone(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputType('tel');

        $this->add($this->form->elementFactory->text($label, $name, (array)$attributes));

        return $this;
    }

    public function addText(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->text($label, $name, (array)$attributes));

        return $this;
    }

    public function addColorPicker(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputClass('colorpicker');
        $this->assets->loadBootstrapColorPicker();

        $this->add($this->form->elementFactory->text($label, $name, (array)$attributes));

        return $this;
    }

    public function addUrl(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->url($label, $name, (array)$attributes));

        return $this;
    }

    public function addEditor(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputClass('editor');
        $this->add($this->form->elementFactory->textarea($label, $name, (array)$attributes));

        return $this;
    }

    public function addTextarea(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->textarea($label, $name, (array)$attributes));

        return $this;
    }

    public function addEmail(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->email($label, $name, (array)$attributes));

        return $this;
    }

    public function addPassword(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->password($label, $name, (array)$attributes));

        return $this;
    }

    public function addHtml(string $html): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->html(['html' => $html]));

        return $this;
    }

    public function addAcl(string $label, string $name): AbstractFormInterface
    {
        $this->assets->loadSelect2();
        $this->addDropdown(
            $label,
            $name,
            (new Attributes())
                ->setMultiple(true)
                ->setOptions(ElementHelper::arrayToSelectOptions(PermissionRole::findAll())
                )
        );

        return $this;
    }

    public function addDropdown(string $label, string $name, Attributes $attributes): AbstractFormInterface
    {
        if ($attributes->getInputClass() === 'select2') :
            $this->assets->loadSelect2();
        endif;
        $this->add($this->form->elementFactory->dropdown($label, $name, $attributes));

        return $this;
    }

    public function addHidden(string $name, ?string $value = null): AbstractFormInterface
    {
        $attributes = (new Attributes())->setDefaultValue($value);
        $this->add($this->form->elementFactory->hidden($name, (array)$attributes));

        return $this;
    }

    public function addFilemanager(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setTemplate('filemanager')->setFilemanager(true);

        $this->add($this->form->elementFactory->file($label, $name, (array)$attributes));

        return $this;
    }

    public function addFile(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->file($label, $name, (array)$attributes));

        return $this;
    }

    public function addUpload(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;

        $this->add($this->form->elementFactory->file($label, $name, (array)$attributes));

        return $this;
    }

    public function addDate(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->date($label, $name, $attributes));

        return $this;
    }

    public function addTime(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputType('time');
        $this->add($this->form->elementFactory->text($label, $name, (array)$attributes));

        return $this;
    }

    public function renderForm(
        string $action,
        string $formName = null,
        bool   $noAjax = false,
        bool   $newWindow = false
    ): string
    {
        $extra = [];
        $request = new Request();

        if ($request->get('embedded') == 1) :
            $action .= '?embedded=1';
        endif;

        if (substr_count($action, 'http') === 0) :
            $action = $this->url->getBaseUri() . $action;
        endif;

        $this->addCsrf();

        if ($this->ajaxFunction !== null) :
            $extra[] = 'data-ajaxFunction="' . $this->ajaxFunction . '"';
        endif;

        if ($formName) :
            $extra[] = 'name="' . $formName . '"';
        endif;

        if ($newWindow) :
            $extra[] = 'target="_blank"';
        endif;

        if ($noAjax) :
            $extra[] = 'data-ajax="false"';
        endif;

        if (substr_count(strtolower($this->formTemplate), 'horizontal')) :
            $this->formClass .= ' form-inline';
        endif;

        if ($this->formClass) :
            $extra[] = 'class="' . $this->formClass . '"';
        endif;

        $formElements = [];
        foreach ($this->getElements() as $element) :
            if (
                $this->view->getVar('formRequiredText') === ''
                && $element->getAttribute('required') === 'required'
            ) :
                $this->view->set('formRequiredText', $this->view->getVar('REQUIRED_FIELDS'));
            endif;

            $formElement = [];
            $formElement['attributes'] = $element->getAttributes();
            $formElement['element'] = $element;

            //TODO render element naar mustache?
            //TODO verder refactoren
            switch (get_class($element)):
                case Submit::class;
                    $formElement['inputColumns'] = $this->getColumns(
                            'label',
                            'offset'
                        ) . ' ' . $this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
                case Hidden::class :
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    if (substr_count($element->getLabel() ?? '', 'html_') > 0) :
                        $formElement['inputColumns'] = 'col-12';
                        $formElement['input'] = $element->getValue();
                    endif;
                    if (substr_count($element->getLabel() ?? '', 'htmlraw_') > 0) :
                        $formElement['input'] = $element->getValue();
                    endif;
                    break;
                case Check::class:
                    if (
                        $element->getName() === 'agreedTerms'
                        && $this->setting->has('SHOP_PAGE_AGREEDTERMS')
                    ) :
                        $element->setLabel('<a 
                            href="page:' . $this->setting->get('SHOP_PAGE_AGREEDTERMS') . '"
                            class="openmodal"
                             >' . $element->getLabel() . '<a/>'
                        );
                    endif;

                    $formElement['labelColumns'] = $this->getColumns('label');
                    $formElement['label'] = $element->getLabel();
                    $formElement['inputColumns'] = $this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
                case Select::class :
                    $options = $element->getAttribute('options');
                    foreach ($options as $key => $option) :
                        if (
                            (
                                is_string($element->getValue())
                                && $element->getValue() === $option['value']
                            )
                            || (
                                is_array($element->getValue())
                                && in_array($option['value'], $element->getValue())
                            )
                            || (
                                is_string($element->getDefault())
                                && $element->getDefault() === $option['value']
                            )
                            || (
                                is_array($element->getDefault())
                                && in_array($option['value'], $element->getDefault())
                            )
                        ) :
                            $options[$key]['selected'] = true;
                        endif;
                    endforeach;
                    $element->setAttribute('options', $options);

                    $formElement['labelColumns'] = $this->getColumns('label');
                    $formElement['label'] = $element->getLabel();
                    $formElement['inputColumns'] = $this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
                default:
                    $formElement['labelColumns'] = $this->getColumns('label');
                    $formElement['label'] = $element->getLabel();
                    $formElement['inputColumns'] = $this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
            endswitch;
            unset($formElement['element']);
            $formElements[] = $formElement;
        endforeach;

        $form = $this->view->renderTemplate(
            $this->formTemplate,
            $this->configuration->getCoreTemplateDir() . 'views/partials/form/',
            [
                'formElements' => $formElements,
                'formId' => uniqid('form_', false),
                'formAction' => $action,
                'formExtra' => implode(' ', $extra),
                'formLabelAsPlaceholder',
                $this->getLabelAsPlaceholder(),
            ]
        );

        return str_replace('[multiple]', '[]', $form);
    }

    public function addCsrf(): AbstractFormInterface
    {
        $this->add((new Hidden('csrf'))->setAttribute('template', 'csrf'));

        return $this;
    }

    public function getColumns(string $type, $columnType = 'col'): string
    {
        $cols = [];
        switch ($type) :
            case 'label' :
                $cols = $this->labelCol;
                break;
            case 'input' :
                $cols = $this->inputCol;
                break;
        endswitch;

        $colClasses = [];
        foreach ($cols as $screen => $size) :
            $colClasses[] = $columnType . '-' . $screen . '-' . $size;
        endforeach;

        return str_replace(
            ['offset-12', 'offset-sm-12', 'offset-md-12', 'offset-lg-12', 'offset-xl-12'],
            '',
            implode(' ', $colClasses)
        );
    }

    public function getLabelAsPlaceholder(): bool
    {
        return $this->labelAsPlaceholder;
    }

    public function setLabelAsPlaceholder(bool $labelAsPlaceholder): AbstractFormInterface
    {
        $this->labelAsPlaceholder = $labelAsPlaceholder;

        return $this;
    }

    public function getCsrf(): string
    {
        return $this->security->getToken();
    }

    public function validate(): bool
    {
        if (!$this->isValid($this->request->getPost())) {
            $messages = $this->getMessages();

            foreach ($messages as $message) {
                $this->flash->setError((string)$message);
            }

            return false;
        }

        return true;
    }

    public function isValid($data = null, $entity = null, array $whitelist = []): bool
    {
        if ($this->request->hasFiles() === true) :
            $data = array_merge($data, $_FILES);
        endif;

        return parent::isValid($data, $entity);
    }

    public function setColumn(int $column, string $type, array $screens): AbstractFormInterface
    {
        foreach ($screens as $size => $name) :
            switch ($type) :
                case 'label' :
                    $this->labelCol[$size] = $column;
                    break;
                case 'input' :
                    $this->inputCol[$size] = $column;
                    break;
            endswitch;
        endforeach;

        return $this;
    }

    public function setAjaxFunction(string $function): AbstractFormInterface
    {
        $this->ajaxFunction = $function;

        return $this;
    }

    public function setFormClass(string $class): AbstractFormInterface
    {
        $this->formClass = $class;

        return $this;
    }

    public function getFormTemplate(): string
    {
        return $this->formTemplate;
    }

    public function setFormTemplate(string $template): AbstractFormInterface
    {
        $this->formTemplate = $template;

        return $this;
    }
}
