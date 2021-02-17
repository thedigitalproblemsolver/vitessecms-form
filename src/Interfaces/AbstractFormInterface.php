<?php declare(strict_types=1);

namespace VitesseCms\Form\Interfaces;

use VitesseCms\Core\AbstractController;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Services\ViewService;
use VitesseCms\Form\Helpers\AddElementHelper;
use VitesseCms\Form\Helpers\FormElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Media\Services\AssetsService;

/**
 * @property ViewService $view
 * @property AssetsService $assets
 */
interface AbstractFormInterface extends InjectableInterface
{
    public function addSubmitButton(string $label): AbstractFormInterface;

    public function addEmptyButton(string $label): AbstractFormInterface;

    public function addButton(string $label, string $name): AbstractFormInterface;

    public function addToggle(string $label, string $name): AbstractFormInterface;

    public function addNumber(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addText(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addUrl(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addEditor(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addTextarea(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addEmail(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addPassword(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addHtml(string $html): AbstractFormInterface;

    public function addAcl(string $label, string $name): AbstractFormInterface;

    public function addDropdown(string $label, string $name, Attributes $attributes): AbstractFormInterface;

    public function addHidden(string $name, ?string $value = null): AbstractFormInterface;

    public function addFilemanager(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addFile(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addUpload(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addDate(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function addTime(string $label, string $name, ?Attributes $attributes = null): AbstractFormInterface;

    public function renderForm(string $action, string $formName = null, bool $noAjax = false, bool $newWindow = false): string;

    public function getCsrf(): string;

    public function validate(): bool;

    public function getColumns(string $type, $columnType = 'col'): string;

    public function setColumn(Int $column, string $type, array $screens): AbstractFormInterface;

    public function setAjaxFunction(string $function): AbstractFormInterface;

    public function setFormClass(string $class): AbstractFormInterface;

    public function setFormTemplate(string $template): AbstractFormInterface;

    public function getFormTemplate(): string;

    public function setLabelAsPlaceholder(bool $labelAsPlaceholder): AbstractFormInterface;

    public function getLabelAsPlaceholder(): bool;
}
