<?php declare(strict_types=1);

namespace VitesseCms\Form\Models;

class SubmissionIterator extends \ArrayIterator
{
    public function __construct(array $submissions)
    {
        parent::__construct($submissions);
    }

    public function current(): Submission
    {
        return parent::current();
    }
}