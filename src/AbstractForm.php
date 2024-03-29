<?php

declare(strict_types=1);

namespace VitesseCms\Form;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Form;
use Phalcon\Http\Request;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Helpers\ElementUiHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\User\Models\PermissionRole;

abstract class AbstractForm extends Form implements AbstractFormInterface
{
    /** @var array<string,int> */
    protected array $labelCol;
    /** @var array<string,int> */
    protected array $inputCol;
    protected ?string $ajaxFunction;
    protected ?string $formClass;
    protected string $formTemplate;
    protected bool $labelAsPlaceholder;
    protected ElementUiHelper $elementUiHelper;

    /**
     * @param AbstractCollection   $entity
     * @param array<string,string> $userOptions
     */
    public function __construct($entity = null, array $userOptions = [])
    {
        parent::__construct($entity, $userOptions);

        $this->labelCol = ['xs' => 12, 'sm' => 12, 'md' => 4, 'lg' => 3, 'xl' => 3];
        $this->inputCol = ['xs' => 12, 'sm' => 12, 'md' => 8, 'lg' => 9, 'xl' => 9];
        $this->formTemplate = 'form';
        $this->labelAsPlaceholder = false;
        $this->elementUiHelper = new ElementUiHelper(
            $this->eventsManager->fire(LanguageEnum::GET_REPOSITORY->value, new \stdClass())
        );
        $this->ajaxFunction = null;
        $this->formClass = null;
    }

    public function addSubmitButton(string $label, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }

        $this->add($this->form->elementFactory->submitButton($label, (array) $attributes));

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

    public function addToggle(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setTemplate('checkbox_toggle');

        $this->assets->loadBootstrapToggle();
        $this->add($this->form->elementFactory->checkbox($label, $name, (array) $attributes));

        return $this;
    }

    public function addNumber(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setInputType('number');

        $this->add($this->form->elementFactory->number($label, $name, (array) $attributes));

        return $this;
    }

    public function addPhone(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setInputType('tel');

        $this->add($this->form->elementFactory->text($label, $name, (array) $attributes));

        return $this;
    }

    public function addText(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->text($label, $name, (array) $attributes));

        return $this;
    }

    public function addColorPicker(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setInputClass('colorpicker');
        $this->assets->loadBootstrapColorPicker();

        $this->add($this->form->elementFactory->text($label, $name, (array) $attributes));

        return $this;
    }

    public function addUrl(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->url($label, $name, (array) $attributes));

        return $this;
    }

    public function addEditor(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setInputClass('editor');
        $this->add($this->form->elementFactory->textarea($label, $name, (array) $attributes));

        return $this;
    }

    public function addTextarea(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->textarea($label, $name, (array) $attributes));

        return $this;
    }

    public function addEmail(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->email($label, $name, (array) $attributes));

        return $this;
    }

    public function addPassword(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->password($label, $name, (array) $attributes));

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
                ->setOptions(
                    ElementHelper::arrayToSelectOptions(PermissionRole::findAll())
                )
        );

        return $this;
    }

    public function addDropdown(string $label, string $name, Attributes $attributes): AbstractFormInterface
    {
        if ('select2' === $attributes->getInputClass()) {
            $this->assets->loadSelect2();
        }
        $this->add($this->form->elementFactory->dropdown($label, $name, $attributes));

        return $this;
    }

    public function addHidden(string $name, string|array $value = null): AbstractFormInterface
    {
        $attributes = (new Attributes())->setDefaultValue($value);
        $this->add($this->form->elementFactory->hidden($name, (array) $attributes));

        return $this;
    }

    public function addFilemanager(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setTemplate('filemanager')->setFilemanager(true);

        $this->add($this->form->elementFactory->file($label, $name, (array) $attributes));

        return $this;
    }

    public function addFile(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->file($label, $name, (array) $attributes));

        return $this;
    }

    public function addUpload(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }

        $this->add($this->form->elementFactory->file($label, $name, (array) $attributes));

        return $this;
    }

    public function addDate(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        $this->add($this->form->elementFactory->date($label, $name, $attributes));

        return $this;
    }

    public function addTime(string $label, string $name, Attributes $attributes = null): AbstractFormInterface
    {
        if (null === $attributes) {
            $attributes = new Attributes();
        }
        $attributes->setInputType('time');
        $this->add($this->form->elementFactory->text($label, $name, (array) $attributes));

        return $this;
    }

    public function renderForm(
        string $action,
        string $formName = null,
        bool $noAjax = false,
        bool $newWindow = false
    ): string {
        $extra = [];
        $request = new Request();

        if (1 == $request->get('embedded')) {
            $action .= '?embedded=1';
        }

        if (0 === substr_count($action, 'http')) {
            $action = $this->url->getBaseUri().$action;
        }

        $this->addCsrf();

        if (null !== $this->ajaxFunction) {
            $extra[] = 'data-ajaxFunction="'.$this->ajaxFunction.'"';
        }

        if ($formName) {
            $extra[] = 'name="'.$formName.'"';
        }

        if ($newWindow) {
            $extra[] = 'target="_blank"';
        }

        if ($noAjax) {
            $extra[] = 'data-ajax="false"';
        }

        if (substr_count(strtolower($this->formTemplate), 'horizontal')) {
            $this->formClass .= ' form-inline';
        }

        if ($this->formClass) {
            $extra[] = 'class="'.$this->formClass.'"';
        }

        $formElements = [];
        foreach ($this->getElements() as $element) {
            if (
                '' === $this->view->getVar('formRequiredText')
                && 'required' === $element->getAttribute('required')
            ) {
                $this->view->set('formRequiredText', $this->view->getVar('REQUIRED_FIELDS'));
            }

            $formElement = [];
            $formElement['attributes'] = $element->getAttributes();
            $formElement['element'] = $element;

            // TODO render element naar mustache?
            // TODO verder refactoren
            switch (get_class($element)) {
                case Submit::class:
                    $formElement['inputColumns'] = $this->getColumns(
                        'label',
                        'offset'
                    ).' '.$this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
                case Hidden::class :
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    if (substr_count($element->getLabel() ?? '', 'html_') > 0) {
                        $formElement['inputColumns'] = 'col-12';
                        $formElement['input'] = $element->getValue();
                    }
                    if (substr_count($element->getLabel() ?? '', 'htmlraw_') > 0) {
                        $formElement['input'] = $element->getValue();
                    }
                    break;
                case Check::class:
                    if (
                        'agreedTerms' === $element->getName()
                        && $this->setting->has('SHOP_PAGE_AGREEDTERMS')
                    ) {
                        $element->setLabel(
                            '<a 
                            href="page:'.$this->setting->get('SHOP_PAGE_AGREEDTERMS').'"
                            class="openmodal"
                             >'.$element->getLabel().'<a/>'
                        );
                    }

                    $formElement['labelColumns'] = $this->getColumns('label');
                    $formElement['label'] = $element->getLabel();
                    $formElement['inputColumns'] = $this->getColumns('input');
                    $formElement['input'] = $this->elementUiHelper->renderElement($element, $this);
                    break;
                case Select::class :
                    $options = $element->getAttribute('options');
                    foreach ($options as $key => $option) {
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
                        ) {
                            $options[$key]['selected'] = true;
                        }
                    }
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
            }
            unset($formElement['element']);
            $formElements[] = $formElement;
        }

        $form = $this->view->renderTemplate(
            $this->formTemplate,
            $this->configuration->getCoreTemplateDir().'views/partials/form/',
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
        switch ($type) {
            case 'label':
                $cols = $this->labelCol;
                break;
            case 'input':
                $cols = $this->inputCol;
                break;
        }

        $colClasses = [];
        foreach ($cols as $screen => $size) {
            $colClasses[] = $columnType.'-'.$screen.'-'.$size;
        }

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

    public function bind(array $data, $entity = null, array $whitelist = []): Form
    {
        parent::bind($data, $entity, $whitelist);
        if (null !== $this->entity) {
            $parsed = [];
            foreach ($this->getElements() as $key => $value) {
                if (
                    substr_count($key, '[') > 0
                    && substr_count($key, ']') > 0
                    && !in_array($key, $parsed)
                ) {
                    $field = explode('[', $key)[0];
                    if (!isset($model->$field)) {
                        $this->entity->set($field, $this->request->getPost($field));
                    }
                    $parsed[] = $key;
                }
            }
        }

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
                $this->flash->setError((string) $message);
            }

            return false;
        }

        return true;
    }

    public function isValid($data = null, $entity = null, array $whitelist = []): bool
    {
        if (true === $this->request->hasFiles()) {
            $data = array_merge($data, $_FILES);
        }

        return parent::isValid($data, $entity);
    }

    public function setColumn(int $column, string $type, array $screens): AbstractFormInterface
    {
        foreach ($screens as $size => $name) {
            switch ($type) {
                case 'label':
                    $this->labelCol[$size] = $column;
                    break;
                case 'input':
                    $this->inputCol[$size] = $column;
                    break;
            }
        }

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
