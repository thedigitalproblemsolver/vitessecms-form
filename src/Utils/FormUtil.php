<?php declare(strict_types=1);

namespace VitesseCms\Form\Utils;

use MongoDB\BSON\UTCDateTime;
use Phalcon\Forms\Element\ElementInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

class FormUtil
{
    public static function renderInputTemplate(ElementInterface $element, ?string $short, AbstractForm $form): string
    {
        if ($element->getAttribute('template') === 'csrf') :
            return (string)$element;
        endif;

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

        if ($short === null) :
            $params['elementValue'] = $element->getValue();
        endif;

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

        if (empty($element->getAttribute('id'))) :
            $params['elementId'] = uniqid('', false);
        endif;

        return $form->eventsManager->fire(ViewEnum::RENDER_PARTIAL_EVENT, new RenderPartialDTO(
            'form/' . $form->getFormTemplate() . '/' . $element->getAttribute('template'),
            $params
        ));
    }

    public static function hasValidRecaptcha(array $post): bool
    {
        return !empty($post['g-recaptcha-response']) && !empty($post['recaptcha-token']);
    }
}
