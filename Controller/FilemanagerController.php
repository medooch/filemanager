<?php

namespace Core\FilemanagerBundle\Controller;

use Core\FilemanagerBundle\Entity\Files;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FilemanagerController extends Controller
{
    public function indexAction(Request $request)
    {
        $manager = $this->get('filemanager.folder.manager');
        if ($request->query->has('dir')) {
            $folder = $manager->find($request->query->get('dir'));
        } else {
            $folder = $manager->main();
            if (!$folder) {
                $folder = $manager->initRootDir();;
            }
        }

        $query = $manager->repository();
        $request->query->has('direction') ? $direction = $request->query->get('direction') : $direction = 'asc';
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

        $request->query->has('view') ? $view = $request->query->get('view') : $view = 'grid';

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
        $manager = $this->get('filemanager.folder.manager');

        switch ($request->get('action')) {
            case 'create':
                $target = $manager->find($request->get('target'));

                $path = $request->get('folder_name');
                $folderName = $request->get('folder_name');

                $folder = $this->get('filemanager.folder.manager')->create($path, $folderName, $folderName, $target);

                return $this->redirectToRoute('filemanager_index', array('dir' => $folder->getId()));
                break;
            case 'delete':
                $manager->delete($request->get('target'));
                return $this->redirectToRoute('filemanager_index', array('dir' => $request->get('dir')));
                break;
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
