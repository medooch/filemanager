<?php

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
     * @param array $filePost
     * @param $folder
     * @return Files
     */
    public function create(Files $file)
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    /**
     * @param $path
     * @param $name
     * @param $fullname
     * @param $id
     * @return Folders|null|object
     */
    public function update($path, $name, $fullname, $id)
    {
        $folder = $this->find($id);

        if (!$folder) {
            throw new NotFoundHttpException('Folder ' . $id . ' dosnt exist.');
        }

        $folder->setName($name);
        $folder->setFullName($fullname);
        $folder->setPath($path);

        $this->entityManager->persist($folder);
        $this->entityManager->flush();

        return $folder;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $folder = $this->find($id);

        if (!$folder) {
            throw new NotFoundHttpException('Folder ' . $id . ' dosnt exist.');
        }

        $this->entityManager->remove($folder);
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
        return $this->entityManager->getRepository('FilemanagerBundle:Folders');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery()
    {
        return $this->repository()->createQueryBuilder('e');
    }

    /**
     * @param $dir
     * @return Folders|null|object
     */
    public function main()
    {
        return $this->findOneBy(array(
            'lvl' => 0,
        ));
    }

    public function initRootDir()
    {
        $path = $this->rootDir . '/../web/data';
        $name = $fullname = str_replace($this->rootDir . '/../web/', '', $path);
        return $this->create($name, $name, $fullname);
    }
}