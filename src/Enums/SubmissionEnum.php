<?php

declare(strict_types=1);

namespace VitesseCms\Form\Enums;

enum SubmissionEnum: string
{
    case LISTENER = 'SubmissionListener';
    case GET_REPOSITORY = 'SubmissionListener:getRepository';
}