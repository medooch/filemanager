<?php

namespace Core\FilemanagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class FilemanagerController extends Controller
{
    public function indexAction(Request $request)
    {
        $mainRoot = $rootDir = $this->getParameter('root_dir');
        if (!is_dir($rootDir)) {
            $this->get('filemanager.twig_extension')->createDir($rootDir);
        }

        $directories = new Finder();
        $files = new Finder();
        if ($request->query->has('dir')) {
            $target = $request->query->get('dir');
            $rootDir .= '/' . $target;
        }

        $directories->sortByType();
        $directories->directories()->in($rootDir);

        $files->sortByType();
        $files->files()->in($rootDir);

        return $this->render('@Filemanager/layout.html.twig', array(
            'directories' => $directories,
            'files' => $files,
            'mainRoot' => basename($mainRoot),
            'rootDir' => basename($rootDir),
        ));
    }

    public function doAction(Request $request)
    {
        if (!$request->query->has('action') || !$request->query->has('dir')) {
            throw $this->createNotFoundException('Invalid Url');
        }

        $fileSystem = $this->get('filemanager.twig_extension');
        switch ($request->query->get('action')){
            case 'create':
                $fileSystem->createDir($request->query->get('dir'));
                break;
            case 'delete':
                $fileSystem->removeDir($request->query->get('dir'));
                break;
            case 'create':
                $fileSystem->createDir($request->query->get('dir'));
                break;
            case 'create':
                $fileSystem->createDir($request->query->get('dir'));
                break;
        }
    }
}
