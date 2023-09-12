<?php declare(strict_types=1);

namespace VitesseCms\Form\Enums;

enum SubmissionEnum:string
{
    case LISTENER = 'ExportTypeListener';
    case GET_REPOSITORY = 'ExportTypeListener:getRepository';
}