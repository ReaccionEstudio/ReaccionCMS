<?php

namespace ReaccionEstudio\ReaccionCMSBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager
{
    /**
     * @var
     */
    protected $class;

    /**
     * @var EntityManagerInterface
     *
     * EntityManager
     */
    protected $em;

    /**
     * AbstractManager constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Language
     */
    public function create()
    {
        return new $this->class();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->getRepo()->findOneBy(['id' => $id]);
    }

    /**
     * @return mixed
     */
    public function findBy(Array $filters = array(), Array $order = array() )
    {
        return $this->getRepo()->findBy($filters, $order);
    }

    /**
     * @return mixed
     */
    public function findAll(Array $order = Array())
    {
        return $this->getRepo()->findBy(array(), $order);
    }

    /**
     * @param $entity
     * @return bool
     */
    public function save($entity) : bool
    {
        try
        {
            $this->em->persist($entity);
            $this->em->flush();

            return true;
        }
        catch(\Exception $e)
        {
            // TODO: log error
            throw $e;
            return false;
        }
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function remove($entity)
    {
        try
        {
            $this->em->remove($entity);
            $this->em->flush();
        }
        catch(\Exception $e)
        {
            // TODO: log error
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function getRepo()
    {
        return $this->em->getRepository($this->class);
    }
}