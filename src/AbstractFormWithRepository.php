<?php

declare(strict_types=1);

namespace VitesseCms\Form;

use Phalcon\Forms\Form;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;

/**
 * @deprecated should get repositories by event
 */
abstract class AbstractFormWithRepository extends AbstractForm implements FormWithRepositoryInterface
{
    protected BaseRepositoriesInterface $repositories;

    abstract public function buildForm(): FormWithRepositoryInterface;

    public function setRepositories(BaseRepositoriesInterface $baseRepositories): FormWithRepositoryInterface
    {
        $this->repositories = $baseRepositories;

        return $this;
    }

    public function setEntity($entity): Form
    {
        $this->entity = $entity;

        return $this;
    }
}
