<?php declare(strict_types=1);

namespace VitesseCms\Form\Factories;

use Phalcon\Di\Di;
use Phalcon\Forms\Element;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\ElementInterface;
use Phalcon\Validation\Validator\File as FileValidator;
use Phalcon\Validation\Validator\PresenceOf;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Form\Utils\ElementUiUtil;
use VitesseCms\Language\Helpers\LanguageHelper;
use VitesseCms\Language\Services\LanguageService;
use function count;
use function is_object;
use function is_string;

class ElementFactory
{
    /**
     * @var LanguageService
     */
    protected $language;

    public function __construct(LanguageService $languageService)
    {
        $this->language = $languageService;
    }

    public function submitButton(string $label, array $attributes = []): ElementInterface
    {
        $attributes['inputClass'] = 'btn btn-success btn-block';
        $attributes['template'] = 'button';
        $attributes['buttonType'] = 'submit';

        return new Submit($label, $attributes);
    }

    public function emptyButton(string $label): ElementInterface
    {
        return new Submit($label, [
            'inputClass' => 'btn btn-outline-danger btn-block btn-form-emtpy',
            'template' => 'button',
            'buttonType' => 'reset',
        ]);
    }

    public function resetButton(string $label): ElementInterface
    {
        new Submit($label, [
            'inputClass' => 'btn btn-outline-danger btn-block',
            'template' => 'button',
            'buttonType' => 'reset'
        ]);
    }

    public function button(string $label, string $name = ''): ElementInterface
    {
        return new Submit($label, [
            'inputClass' => 'btn btn-outline-info btn-block',
            'template' => 'button',
            'buttonType' => 'button',
            'elementId' => $name,
        ]);
    }

    public function checkbox(string $label, string $name, array $attributes = []): Check
    {
        $element = new Check($name, $attributes);
        $this->parseDefaults($element, $label, 'checkbox');

        if ($element->getDefault() === '') :
            $element->setDefault(true);
        endif;

        return $element;
    }

    public function parseDefaults(Element $element, string $label, string $template = ''): void
    {
        ElementHelper::setDefaults($element, $label);
        $this->setRequired($element);
        ElementHelper::setValue($element);

        if (!empty($template)) :
            $element = ElementUiUtil::setTemplate($element, $template);
        endif;
    }

    public function setRequired(ElementInterface $element): void
    {
        if ($element->getAttribute('required')) :
            $element->addValidators([
                new PresenceOf([
                    'message' => $this->language->get(
                        'FORM_REQUIRED_MESSAGE',
                        ['"' . $element->getLabel() . '"']
                    ),
                ]),
            ]);
        endif;
    }

    public function email(string $label, string $name, array $attributes = []): Email
    {
        $element = new Email($name, $attributes);
        $this->parseDefaults($element, $label, 'email');

        return $element;
    }

    public function file(string $label, string $name, array $attributes = []): File
    {
        if (isset($attributes['filemanager']) && $attributes['filemanager'] === true) :
            $attributes['readonly'] = 'readonly';
        endif;

        $element = new File($name, $attributes);
        ElementHelper::setDefaults($element, $label);
        ElementHelper::setValue($element);

        if (
            isset($attributes['allowedTypes'])
            && is_array($attributes['allowedTypes'])
            && (
                Di::getDefault()->get('request')->getPost() === 0
                || (
                    isset($_FILES[$name])
                    && $_FILES[$name]['name'] !== ''
                )
            )
        ) :
            $element->addValidator(
                new FileValidator([
                    'allowedTypes' => $attributes['allowedTypes'],
                    'messageType' => 'Allowed file types are :types',
                ])
            );
        endif;

        $element = ElementUiUtil::setTemplate($element, 'file');

        return $element;
    }

    public function hidden(string $name, array $attributes = []): Hidden
    {
        $element = new Hidden($name, $attributes);
        ElementHelper::setValue($element);
        $element = ElementUiUtil::setTemplate($element, 'hidden');

        return $element;
    }

    public function html(array $attributes, string $prefix = 'html'): Hidden
    {
        $name = $prefix . '_' . uniqid('', true);

        $element = new Hidden($name);
        if (isset($attributes['html'])) :
            $element->setDefault($attributes['html']);
        endif;
        $element->setLabel($name);

        return $element;
    }

    public function number(string $label, string $name, array $attributes = []): Numeric
    {
        $element = new Numeric($name, $attributes);
        $this->parseDefaults($element, $label, 'number');

        return $element;
    }

    public function password(string $label, string $name, array $attributes = []): Password
    {
        $element = new Password($name, $attributes);
        $this->parseDefaults($element, $label, 'password');

        return $element;
    }

    /**
     * @deprecated should us addDropdown from Form
     */
    public function select(string $label, string $name, array $attributes = []): Select
    {
        $options = [];
        if (isset($attributes['options'])) :
            $options = $attributes['options'];
            unset($attributes['options']);
        endif;

        $newOptions = [];
        if (!isset($attributes['noEmptyText'])) :
            $newOptions[] = [
                'value' => '',
                'label' => '%ADMIN_NO_SELECTION%',
                'selected' => false,
            ];
        endif;

        if (is_string($options)) :
            $options::addFindOrder('name');
            $items = $options::Findall();
            /** @var AbstractCollection $item */
            foreach ($items as $item) :
                $newOptions[] = [
                    'value' => (string)$item->getId(),
                    'label' => ucfirst($item->_('name')),
                    'selected' => false,
                ];
            endforeach;
        elseif (
            count($options) > 0
            && isset($options[0])
            && is_object($options[0])
            && substr_count(get_class($options[0]), 'Models') > 0
        ) :
            /** @var AbstractCollection $option */
            foreach ($options as $option) :
                $newOptions[] = [
                    'value' => (string)$option->getId(),
                    'label' => $option->_('name'),
                    'selected' => false,
                ];
            endforeach;
        else :
            $newOptions = $options;
        endif;
        $options = $newOptions;

        if (isset($attributes['value'])) :
            foreach ($options as $key => $option) :
                if ($option['value'] === $attributes['value']) :
                    $options[$key]['selected'] = true;
                endif;
            endforeach;
        endif;

        $element = new Select($name, $options, $attributes);
        $element->setAttribute('options', $options);
        $this->parseDefaults($element, $label, 'select');

        return $element;
    }

    public function dropdown(string $label, string $name, Attributes $attributes): Select
    {
        $element = new Select($name, $attributes->getOptions(), (array)$attributes);
        $element->setAttribute('options', $attributes->getOptions());
        $this->parseDefaults($element, $label, 'select');

        return $element;
    }

    public function tel(string $label, string $name, array $attributes = []): Text
    {
        $element = new Text($name, $attributes);
        $this->parseDefaults($element, $label, 'tel');

        return $element;
    }

    public function date(string $label, string $name, ?Attributes $attributes = null): Date
    {
        if ($attributes === null) :
            $attributes = new Attributes();
        endif;
        $attributes->setInputType('date');

        $element = new Date($name, (array)$attributes);
        $this->parseDefaults($element, $label, 'text');

        return $element;
    }

    public function text(string $label, string $name, array $attributes = []): Text
    {
        $element = new Text($name, $attributes);
        $this->parseDefaults($element, $label, 'text');

        return $element;
    }

    public function textarea(string $label, string $name, array $attributes = []): TextArea
    {
        $element = new TextArea($name, $attributes);
        $this->parseDefaults($element, $label, 'textarea');

        return $element;
    }

    public function url(string $label, string $name, array $attributes = []): Text
    {
        $element = new Text($name, $attributes);
        $this->parseDefaults($element, $label, 'url');

        return $element;
    }
}
