<?php declare(strict_types=1);

namespace VitesseCms\Form\Interfaces;

interface SelectOptionEnumInterface
{
    public static function getLabel($label): string;
}