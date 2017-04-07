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

use Core\FilemanagerBundle\Entity\Folders;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FolderManager
 * @package Core\FilemanagerBundle\Manager
 */
class FolderManager
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
     * @param $path
     * @param $name
     * @param $fullname
     * @param Folders|null $parent
     * @return Folders
     */
    public function create($path, $name, $fullname, Folders $parent = null, $permissions = null)
    {
        $folder = new Folders();
        $folder->setName($name);
        $folder->setFullName($fullname);

        $folder->setPermissions($permissions);
        if ($parent) {
            $folder->setParent($parent);
            $path = $parent->getPath() . '/' . $path;
            if (!$permissions){
                $folder->setPermissions($parent->getPermissions());
            }
        }

        $folder->setPath($path);

        $this->entityManager->persist($folder);
        $this->entityManager->flush();

        return $folder;
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
     * @return Folders|null|object
     */
    public function main()
    {
        return $this->findOneBy(array(
            'lvl' => 0,
        ));
    }

    /**
     * @return Folders
     */
    public function initRootDir()
    {
        $path = $this->rootDir . '/../web/data';
        $name = $fullname = str_replace($this->rootDir . '/../web/', '', $path);
        return $this->create($name, $name, $fullname, null, 2);
    }
}