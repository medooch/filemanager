<?php

namespace Core\FilemanagerBundle\Twig\Extension;

use Core\FilemanagerBundle\Entity\Files;
use Core\FilemanagerBundle\Entity\Folders;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FilemanagerExtension extends \Twig_Extension
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->fileSystem = new Filesystem();;
        $this->output = '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filemanager';
    }

    public function getFunctions()
    {
        return array(
            'fileType' => new \Twig_Function_Method($this, '_checkFileType'),
            'url_file' => new \Twig_Function_Method($this, 'urlFile'),
            'breadCrumb' => new \Twig_Function_Method($this, 'breadCrumb'),
            'unset' => new \Twig_Function_Method($this, 'unset_array'),
        );
    }

    /**
     * @param array $parameters
     * @param $key
     * @return array
     */
    public function unset_array(array $parameters, $key)
    {
        if (array_key_exists($key, $parameters)) {
            unset($parameters[$key]);
        }

        return $parameters;
    }

    /**
     * @param string $dir
     * @return string
     */
    public function breadCrumb(Folders $folder)
    {
        if ($folder->getParent() && $folder->getLvl() != 0) {
            $this->output .= $this->breadCrumbItem($folder->getParent());
        } else {
            $this->output .= $this->breadCrumbItem($folder);
        }

        return $this->output;
    }

    public function breadCrumbItem(Folders $folder)
    {
        return '<li><a href="?dir=' . $folder->getId() . '">' . $folder->getFullName() . '</a></li>';
    }

    /**
     * @param Files $file
     * @return string
     */
    public function urlFile(Files $file)
    {
        return $this->container->get('assets.packages')->getUrl($file->getFullpath());
    }

    /**
     * @param $path
     * @return bool
     */
    public function _doesFileExist($path)
    {
        return file_exists($path);
    }

    /**
     * public functions
     */
    public function _checkFileType(Files $file)
    {
        $path = $this->container->getParameter('kernel.root_dir') . '/../web/' . $file->getFullpath();
        if (!$this->_doesFileExist($path)) return 'inexistent';

        /** check fileExist */

        /** @var  $mimeType */
        $mimeType = $file->getExtension();

        if ($this->_isAudio($mimeType)) return 'audio';
        if ($this->_isArchive($mimeType)) return 'archive';
        if ($this->_isHTML($mimeType)) return 'html';
        if ($this->_isImage($mimeType)) return 'image';
        if ($this->_isPDFDocument($mimeType)) return 'pdf-document';
        if ($this->_isPlainText($mimeType)) return 'plain-text';
        if ($this->_isPresentation($mimeType)) return 'presentation';
        if ($this->_isSpreadsheet($mimeType)) return 'spreadsheet';
        if ($this->_isTextDocument($mimeType)) return 'text-document';
        if ($this->_isVideo($mimeType)) return 'video';
        // else
        return 'inexistent ' . $mimeType;
    }

    public function _isAudio($mimeType)
    {
        return (preg_match('/audio\/.*/i', $mimeType));
    }

    public function _isArchive($mimeType)
    {
        return (
            preg_match('/application\/.*compress.*/i', $mimeType) ||
            preg_match('/application\/.*archive.*/i', $mimeType) ||
            preg_match('/application\/.*zip.*/i', $mimeType) ||
            preg_match('/application\/.*tar.*/i', $mimeType) ||
            preg_match('/application\/x\-ace/i', $mimeType) ||
            preg_match('/application\/x\-bz2/i', $mimeType) ||
            preg_match('/gzip\/document/i', $mimeType)
        );
    }

    public function _isHTML($mimeType)
    {
        return (preg_match('/text\/html/i', $mimeType));
    }

    public function _isImage($mimeType)
    {
        return (preg_match('/image\/.*/i', $mimeType));
    }

    public function _isPDFDocument($mimeType)
    {
        return (
            preg_match('/application\/acrobat/i', $mimeType) ||
            preg_match('/applications?\/.*pdf.*/i', $mimeType) ||
            preg_match('/text\/.*pdf.*/i', $mimeType)
        );
    }

    public function _isPlainText($mimeType)
    {
        return (preg_match('/text\/plain/i', $mimeType));
    }

    public function _isPresentation($mimeType)
    {
        return (
            preg_match('/application\/.*ms\-powerpoint.*/i', $mimeType) ||
            preg_match('/application\/.*officedocument\.presentationml.*/i', $mimeType) ||
            preg_match('/application\/.*opendocument\.presentation.*/i', $mimeType)
        );
    }

    public function _isSpreadsheet($mimeType)
    {
        return (
            preg_match('/application\/.*ms\-excel.*/i', $mimeType) ||
            preg_match('/application\/.*officedocument\.spreadsheetml.*/i', $mimeType) ||
            preg_match('/application\/.*opendocument\.spreadsheet.*/i', $mimeType)
        );
    }

    public function _isTextDocument($mimeType)
    {
        return (
            preg_match('/application\/.*ms\-?word.*/i', $mimeType) ||
            preg_match('/application\/.*officedocument\.wordprocessingml.*/i', $mimeType) ||
            preg_match('/application\/.*opendocument\.text.*/i', $mimeType)
        );
    }

    public function _isVideo($mimeType)
    {
        return (preg_match('/video\/.*/i', $mimeType));
    }

    /**
     * @param $dir
     * @param int $permissions
     */
    public function createDir($dir, $permissions = 0700)
    {
        try {
            $this->fileSystem->mkdir($dir, $permissions);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at " . $e->getPath();
        }
    }

    /**
     * @param $path
     */
    public function removeDir($path)
    {
        $this->fileSystem->remove($path);
    }

    /**
     * @param $oldDir
     * @param $newDir
     */
    public function renameDir($oldDir, $newDir)
    {
        $this->fileSystem->rename($oldDir, $newDir);
    }

    /**
     * @param $path
     * @param int $permission
     */
    public function chmod($path, $permission = 0700)
    {
        $this->fileSystem->chmod($path, $permission);
    }
}
