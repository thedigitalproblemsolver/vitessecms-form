<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners\Models;

use VitesseCms\Form\Repositories\SubmissionRepository;

class SubmissionListener {
    public function __construct(public readonly SubmissionRepository $submissionRepository)
    {
    }

    public function getRepository():SubmissionRepository
    {
        return $this->submissionRepository;
    }
}