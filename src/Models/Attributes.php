<?php declare(strict_types=1);

namespace VitesseCms\Form\Models;

class Attributes
{
    public string $inputType;
    public bool $required;
    public bool $readonly;
    public bool $disabled;
    public string $inputClass;
    public array $options;
    public bool $multiple;
    public bool $multilang;
    public string $template;
    public bool $filemanager;
    public bool $checked;
    public string $dataUrl;
    public int $min;
    public int $max;
    public $defaultValue;
    public float $step;
    public array $allowedTypes;
    public bool $noEmptyText;
    public ?string $elementId;
    public ?string $placeholder;

    public function __construct()
    {
        $this->inputClass = '';
        $this->inputType = '';
        $this->required = false;
        $this->multiple = false;
        $this->multilang = false;
        $this->options = [];
        $this->template = '';
        $this->filemanager = false;
        $this->checked = false;
        $this->allowedTypes = [];
        $this->elementId = null;
    }

    public function setInputType(string $inputType): Attributes
    {
        $this->inputType = $inputType;

        return $this;
    }

    public function setRequired(bool $required = true): Attributes
    {
        $this->required = $required;

        return $this;
    }

    public function getInputClass(): string
    {
        return (string)$this->inputClass;
    }

    public function setInputClass(string $inputClass): Attributes
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options ?? [];
    }

    public function setOptions(array $options): Attributes
    {
        $this->options = $options;

        return $this;
    }

    public function setMultiple(bool $multiple = true): Attributes
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function setReadonly(bool $readonly = true): Attributes
    {
        $this->readonly = $readonly;
        $this->disabled = $readonly;

        return $this;
    }

    public function setMultilang(bool $multilang = true): Attributes
    {
        $this->multilang = $multilang;

        return $this;
    }

    public function setTemplate(string $template): Attributes
    {
        $this->template = $template;

        return $this;
    }

    public function setFilemanager(bool $filemanager): Attributes
    {
        $this->filemanager = $filemanager;

        return $this;
    }

    public function setDataUrl(string $dataUrl): Attributes
    {
        $this->dataUrl = $dataUrl;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue): Attributes
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function setChecked(bool $checked = true): Attributes
    {
        $this->checked = $checked;

        return $this;
    }

    public function setMin(int $min): Attributes
    {
        $this->min = $min;

        return $this;
    }

    public function setMax(int $max): Attributes
    {
        $this->max = $max;

        return $this;
    }

    public function setStep(float $step): Attributes
    {
        $this->step = $step;

        return $this;
    }

    public function setAllowedTypes(array $allowedTypes): Attributes
    {
        $this->allowedTypes = $allowedTypes;

        return $this;
    }

    public function setNoEmptyText(bool $noEmptyText = true): Attributes
    {
        $this->noEmptyText = $noEmptyText;

        return $this;
    }

    public function setElementId(?string $elementId): Attributes
    {
        $this->elementId = $elementId;

        return $this;
    }

    public function setPlaceholder(string $string): Attributes
    {
        $this->placeholder = $string;

        return $this;
    }
}
