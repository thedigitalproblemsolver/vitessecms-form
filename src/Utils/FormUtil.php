<?php

declare(strict_types=1);

namespace VitesseCms\Form\Utils;

use MongoDB\BSON\UTCDateTime;
use Phalcon\Forms\Element\ElementInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

class FormUtil
{
    public static function renderInputTemplate(
        ElementInterface $element,
        ?string $short,
        AbstractFormInterface $form
    ): string {
        if ('csrf' === $element->getAttribute('template')) {
            return (string) $element;
        }

        $params = [
            'elementId' => $element->getAttribute('id'),
            'elementLabel' => $element->getLabel(),
            'elementName' => $element->getName(),
            'elementDefault' => $element->getDefault(),
            'elementValue' => false,
            'formLabelAsPlaceholder' => $form->getLabelAsPlaceholder(),
            'short' => $short,
            'attributes' => $element->getAttributes(),
        ];

        $params = self::setElementValue($element, $form, $params, $short);

        //var_dump($params);

        if (empty($element->getAttribute('id'))) {
            $params['elementId'] = uniqid('', false);
        }

        return $form->eventsManager->fire(
            ViewEnum::RENDER_PARTIAL_EVENT,
            new RenderPartialDTO(
                'form/'.$form->getFormTemplate().'/'.$element->getAttribute('template'),
                $params
            )
        );
    }

    /**
     * @param array<string,string> $params * @param ElementInterface $element
     * @param AbstractFormInterface $form
     * @return array<string,string>
     */
    private static function setElementValue(
        ElementInterface $element,
        AbstractFormInterface $form,
        array $params,
        ?string $short,
    ): array {
        if (null === $short) {
            $params['elementValue'] = $element->getValue();
        }

        if (is_array($element->getAttribute('defaultValue'))) {
            $params['elementValue'] = match ($short) {
                null => $element->getAttribute('defaultValue')[$form->configuration->getLanguageShort()] ?? '',
                default => $element->getAttribute('defaultValue')[$short]
            };
        } elseif (is_string($element->getAttribute('defaultValue'))) {
            $params['elementValue'] = $element->getAttribute('defaultValue');
        } elseif ($element->getAttribute('defaultValue') instanceof UTCDateTime) {
            $params['elementValue'] = $element->getAttribute('defaultValue')->toDateTime()->format('Y-m-d');
        }

        return $params;
    }

    /**
     * @param array<string,string> $post
     */
    public static function hasValidRecaptcha(array $post): bool
    {
        return !empty($post['g-recaptcha-response']) && !empty($post['recaptcha-token']);
    }
}
