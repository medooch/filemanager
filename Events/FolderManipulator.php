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

namespace Core\FilemanagerBundle\Events;

use Core\FilemanagerBundle\Entity\Files;
use Core\FilemanagerBundle\Entity\Folders;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FolderManipulator
 * @package Core\FilemanagerBundle\Events
 */
class FolderManipulator implements EventSubscriber
{
    /**
     * @var Entity
     */
    private $entityManager;

    /**
     * @var mixed
     */
    private $rootDir;

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postRemove',
        );
    }

    /**
     * FolderManipulator constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->rootDir = $container->getParameter('root_dir');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $rootDir = $this->container->getParameter('kernel.root_dir');
        if ($entity instanceof Folders) {
            $fileSystem = $this->container->get('filemanager.twig_extension');
            $dir = $rootDir . '/../web/' . $entity->getPath();

            /** create the folder MKDIR */
            $fileSystem->createDir($dir);

            /** Manipulate folder permissions */
            switch ($entity->getPermissions()) {
                case 0:
                    $fileSystem->chmod($dir, 0000);
                    break;
                case 1:
                    $fileSystem->chmod($dir, 0555);
                    break;
                case 2:
                    $fileSystem->chmod($dir, 0777);
                    break;
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    }

    /**
     * @param $args
     *
     * Event Remove User Or Trombino Or Picture Or Groups
     */
    public function postRemove($args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Folders) {
            $fileSystem = $this->container->get('filemanager.twig_extension');

            $fileSystem->removeDir($this->container->getParameter('kernel.root_dir') . '/../web/' . $entity->getPath());
        }
    }
}
