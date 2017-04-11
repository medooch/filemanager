<?php

/*
 * This file is part of the FilemanagerBundle package.
 *
 * (c) Trimech Mahdi <http://www.trimech-mahdi.fr/>
 * @author: Trimech Mehdi <trimechmehdi11@gmail.com>

 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\FilemanagerBundle\Manager;

use Core\FilemanagerBundle\Entity\Files;
use Core\FilemanagerBundle\Entity\Folders;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FolderManager
 * @package Core\FilemanagerBundle\Manager
 */
class FileManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * FolderManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $rootDir = '')
    {
        $this->entityManager = $entityManager;
        $this->rootDir = $rootDir;
    }

    /**
     * @param Files $file
     * @return Files
     */
    public function create(Files $file)
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    /**
     * @param $name
     * @param $id
     * @return Folders|null|object
     */
    public function update($name, $id)
    {
        $file = $this->find($id);

        if (!$file) {
            throw new NotFoundHttpException('File ' . $id . ' dosnt exist.');
        }

        $file->setName($name);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $file = $this->find($id);

        if (!$file) {
            throw new NotFoundHttpException('File ' . $id . ' dosnt exist.');
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param $id
     * @return Folders|null|object
     */
    public function find($id)
    {
        return $this->repository()->find($id);
    }

    /**
     * @param array $criteria
     * @return array|\Core\FilemanagerBundle\Entity\Folders[]
     */
    public function findBy(array $criteria)
    {
        return $this->repository()->findBy($criteria);
    }

    /**
     * @param array $criteria
     * @return Folders|null|object
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository()->findOneBy($criteria);
    }

    /**
     * @return array|\Core\FilemanagerBundle\Entity\Folders[]
     */
    public function findAll()
    {
        return $this->repository()->findAll();
    }

    /**
     * @return \Core\FilemanagerBundle\Repository\FoldersRepository|\Doctrine\ORM\EntityRepository
     */
    public function repository()
    {
        return $this->entityManager->getRepository('FilemanagerBundle:Files');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery()
    {
        return $this->repository()->createQueryBuilder('e');
    }
}