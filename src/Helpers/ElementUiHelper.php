<?php

declare(strict_types=1);

namespace VitesseCms\Form\Helpers;

use Phalcon\Forms\Element\ElementInterface;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Tag;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Utils\FormUtil;
use VitesseCms\Language\Repositories\LanguageRepository;

final class ElementUiHelper
{
    public function __construct(private readonly LanguageRepository $languageRepository)
    {
    }

    public function renderElement(ElementInterface $element, AbstractForm $form): string
    {
        $inputClass = $element->getAttribute('inputClass', '');
        $entity = $form->getEntity();

        if (substr_count($inputClass, 'editor')) {
            $form->assets->loadEditor();
        }

        if (empty($element->getAttribute('template'))) {
            $element->setAttribute('template', 'text');
        }

        if ('date' === $element->getAttribute('inputType') && is_array($element->getDefault())) {
            $firstKey = array_key_first($element->getDefault());
            if (substr_count($element->getName(), '[from]')) {
                $element->setDefault($element->getDefault()[$firstKey]['from']);
            }
            if (substr_count($element->getName(), '[till]')) {
                $element->setDefault($element->getDefault()[$firstKey]['till']);
            }
        }

        if ($element->getAttribute('multilang')) {
            return $this->renderMultiLanguageElement($element, $form);
        } else {
            return $this->renderSingleLanguageElement($element, $form, $entity, $inputClass);
        }
    }

    private function renderMultiLanguageElement(ElementInterface $element, AbstractForm $form): string
    {
        $return = '';
        $element->setAttribute('multilang', null);
        $languageIterator = $this->languageRepository->findAll();
        $element->setAttribute('defaultValue', $element->getValue());

        while ($languageIterator->valid()) {
            $language = $languageIterator->current();
            $shortCode = $language->getShortCode();

            if (isset($element->getValue()[$shortCode])) {
                $element->setDefault($element->getValue()[$shortCode]);
            }

            if (Select::class === get_class($element)) {
                if ($element->getAttribute('multiple')) {
                    $element->setDefault($element->getValue());
                }

                $options = $element->getAttribute('options');
                foreach ($options as $key => $option) {
                    if (
                        isset($element->getDefault()[$shortCode])
                        && in_array($option['value'], $element->getDefault()[$shortCode], true)
                    ) {
                        $option['selected'] = true;
                    } else {
                        $option['selected'] = false;
                    }
                    $options[$key] = $option;
                }
                $element->setAttribute('options', $options);
            }

            $return .= FormUtil::renderInputTemplate($element, $shortCode, $form);
            $return .= $this->generatePreview($element);
            $languageIterator->next();
        }

        return $return;
    }

    private function generatePreview(ElementInterface $element, string $key = null): string
    {
        if (File::class === get_class($element)) {
            $file = null;
            $value = $element->getValue();

            if (is_string($value)) {
                $file = FileUtil::getTag($value);
            } elseif (is_array($value)) {
                if (isset($value[$key])) {
                    $file = FileUtil::getTag($value[$key]);
                }
            }

            if (null !== $file) {
                return '<div class="file-preview">'.$file.'</div>';
            }
        }

        return '';
    }

    private function renderSingleLanguageElement(
        ElementInterface $element,
        AbstractForm $form,
        $entity,
        string $inputClass
    ): string {
        $return = '';

        if ($element->getAttribute('multiple')) {
            $fieldName = str_replace('[]', '', $element->getName());
            if ($entity) {
                $element->setDefault($entity->_($fieldName));
            }
        }

        if (substr_count($inputClass, 'select2-sortable') > 0) {
            $value = '';
            if (is_array($element->getValue())) {
                $value = implode(',', $element->getValue());
            }
            $return .= Tag::hiddenField([
                $element->getAttribute('id').'_select2Sortable',
                'value' => $value,
            ]);
        }

        if (
            is_object($entity)
            && (
                true === $entity->_($element->getName())
                || '1' === $entity->_($element->getName())
            )
        ) {
            $element->setAttribute('checked', true);
        }

        if (
            is_object($entity)
            && Text::class === get_class($element)
            && substr_count($element->getName(), '[')
            && substr_count($element->getName(), ']')
        ) {
            $index = ElementHelper::parseTextNameAttribute(
                $element->getName()
            );
            $array = $entity->_($index[0]);

            if (isset($array[$index[1]])) {
                $element->setDefault(trim($array[$index[1]]));
            }
        }

        if (
            is_object($entity)
            && Select::class === get_class($element)
        ) {
            if (
                substr_count($element->getName(), '[')
                && substr_count($element->getName(), ']')
            ) {
                $index = ElementHelper::parseTextNameAttribute(
                    $element->getName()
                );
                $array = $entity->_($index[0]);

                if (isset($array[$index[1]])) {
                    $element->setDefault(trim($array[$index[1]]));
                    $options = $element->getAttribute('options');
                    foreach ($options as $key => $option) {
                        if ($option['value'] === trim($array[$index[1]])) {
                            $options[$key]['selected'] = true;
                        }
                    }
                    $element->setAttribute('options', $options);
                }
            } else {
                $value = $entity->_($element->getName());
                if (is_numeric($value)) {
                    $value = (int) $value;
                }
                $options = $element->getAttribute('options');
                foreach ($options as $key => $option) {
                    if ($option['value'] === $value) {
                        $options[$key]['selected'] = true;
                    }
                }
                $element->setAttribute('options', $options);
            }
        }

        $return .= FormUtil::renderInputTemplate($element, null, $form);
        $return .= $this->generatePreview($element);

        return $return;
    }
}
