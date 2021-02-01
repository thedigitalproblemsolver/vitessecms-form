<?php declare(strict_types=1);

namespace VitesseCms\Form\Models;

class Attributes
{
    /**
     * @var string
     */
    public $inputType;

    /**
     * @var bool
     */
    public $required;

    /**
     * @var bool
     */
    public $readonly;

    /**
     * @var bool
     */
    public $disabled;

    /**
     * @var string
     */
    public $inputClass;

    /**
     * @var array
     */
    public $options;

    /**
     * @var bool
     */
    public $multiple;

    /**
     * @var bool
     */
    public $multilang;

    /**
     * @var string
     */
    public $template;

    /**
     * @var bool
     */
    public $filemanager;

    /**
     * @var bool
     */
    public $checked;

    /**
     * @var string
     */
    public $dataUrl;

    /**
     * @var int
     */
    public $min;

    /**
     * @var int
     */
    public $max;

    public $defaultValue;

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

    public function setInputClass(string $inputClass): Attributes
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function getInputClass(): string
    {
        return $this->inputClass;
    }

    public function setOptions(array $options): Attributes
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setMultiple(bool $multiple = true): Attributes
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function setReadonly(bool $readonly): Attributes
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

    public function setChecked(bool $checked): Attributes
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
}
