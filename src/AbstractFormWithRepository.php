<?php declare(strict_types=1);

namespace VitesseCms\Form;

use Phalcon\Forms\Form;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;

abstract class AbstractFormWithRepository extends AbstractForm implements FormWithRepositoryInterface
{
    protected BaseRepositoriesInterface $repositories;

    abstract function buildForm(): FormWithRepositoryInterface;

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
