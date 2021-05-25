<?php

namespace App\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseEntityHandler
{
    /** @var string */
    protected $entityName;

    protected ParameterBagInterface $parameterBag;

    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    protected $repository;

    protected $namespace;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameterBag)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(sprintf('App:%s%s', $this->namespace, $this->entityName));
        $this->parameterBag = $parameterBag;
    }

    public function getBy(Array $criteria = [], $orderBy = ['id' => 'desc'])
    {
        return $this->repository->findBy($criteria, $orderBy);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function getOneBy(Array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
