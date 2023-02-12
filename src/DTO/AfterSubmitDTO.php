<?php declare(strict_types=1);

namespace VitesseCms\Form\DTO;

use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Models\Submission;

class AfterSubmitDTO
{
    public function __construct(public readonly Submission $submission, public readonly FormBuilder $formBuilder){}
}