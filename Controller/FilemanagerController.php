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

namespace Core\FilemanagerBundle\Controller;

use Core\FilemanagerBundle\Entity\Files;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FilemanagerController
 * @package Core\FilemanagerBundle\Controller
 */
class FilemanagerController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var get the default folder in database $manager */
        $manager = $this->get('filemanager.folder.manager');
        if ($request->query->has('dir')) {
            $folder = $manager->find($request->query->get('dir'));
        } else {
            $folder = $manager->main();
            if (!$folder) {
                /** @var init the main root defined on the bundle configuration $folder */
                $folder = $manager->initRootDir();;
            }
        }

        /** @var get Directories from folders_table $query */
        $query = $manager->repository();
        $request->query->has('direction') ? $direction = $request->query->get('direction') : $direction = 'asc';

        /** apply sort by parameter */
        if ($request->query->has('sort')) {
            switch ($request->query->get('sort')) {
                case 'name':
                    $directories = $query->getChildren($folder, true, 'name', $direction);
                    break;
                case 'created-at':
                    $directories = $query->getChildren($folder, true, 'created', $direction);
                    break;
                case 'updated-at':
                    $directories = $query->getChildren($folder, true, 'updated', $direction);
                    break;
                default:
                    $directories = $query->getChildren($folder, true);
            }
        } else {
            $directories = $query->getChildren($folder, true);
        }

        /** get view (list or grid) from the request or define it as Grid */
        $request->query->has('view') ? $view = $request->query->get('view') : $view = 'grid';

        /** @var File Upload $formUpload */
        $formUpload = $this->createForm('Core\FilemanagerBundle\Form\File', new Files());

        return $this->render('@Filemanager/layout.html.twig', array(
            'directories' => $directories,
            'folder' => $folder,
            'view' => $view,
            'formUpload' => $formUpload->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function doAction(Request $request)
    {
        /** @var Folder Manager From Container $manager */
        $manager = $this->get('filemanager.folder.manager');

        /** execute actions */
        switch ($request->get('action')) {
            /** create folder */
            case 'create':
                /** @var get the target dir $target */
                $target = $manager->find($request->get('target'));

                /** @var folder name $path */
                $path = $request->get('folder_name');
                $folderName = $request->get('folder_name');

                /** @var insert the folder and create it $folder */
                $folder = $this->get('filemanager.folder.manager')->create($path, $folderName, $folderName, $target);

                return $this->redirectToRoute('filemanager_index', array('dir' => $folder->getId()));
                break;
            /** delete folder */
            case 'delete':
                $manager->delete($request->get('target'));
                return $this->redirectToRoute('filemanager_index', array('dir' => $request->get('dir')));
                break;
            /** upload file */
            case 'upload_file':
                $fileManager = $this->get('filemanager.file.manager');
                $formUpload = $this->createForm('Core\FilemanagerBundle\Form\File', new Files());
                if ($request->isMethod('POST')) {
                    $formUpload->handleRequest($request);
                    if ($formUpload->isValid()) {
                        $file = $formUpload->getData();
                        $file->setFolder($manager->find($request->get('target')));
                        $file->setUploadPath($this->getParameter('kernel.root_dir') .'/../web/');
                        $fileManager->create($formUpload->getData());
                    }
                }
                return $this->redirectToRoute('filemanager_index', array('dir' => $request->get('target')));
                break;
            case 'rename':
                break;
        }
    }
}
