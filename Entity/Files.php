<?php

namespace Core\FilemanagerBundle\Entity;

use Core\FilemanagerBundle\Traits\TracableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="Core\FilemanagerBundle\Repository\FilesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Files
{
    use TracableTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Folders", inversedBy="files")
     */
    private $folder;

    /**
     * @var
     */
    private $file;

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @var
     */
    private $uploadPath;

    /**
     * @return mixed
     */
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * @param mixed $uploadPath
     */
    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = $uploadPath . $this->folder->getPath();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setName()
    {
        $this->name = $this->file->getClientOriginalName();

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setSize()
    {
        $this->size = $this->file->getClientSize();

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return $this
     */
    public function setExtension()
    {
        $this->extension = $this->file->getMimeType();

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set folder
     *
     * @param \Core\FilemanagerBundle\Entity\Folders $folder
     *
     * @return Files
     */
    public function setFolder(\Core\FilemanagerBundle\Entity\Folders $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \Core\FilemanagerBundle\Entity\Folders
     */
    public function getFolder()
    {
        return $this->folder;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File|null $file
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\File $file = null)
    {
        if ($file instanceof \Symfony\Component\HttpFoundation\File\File) {
            $this->file = $file;
            // check if we have an old image path
            if (isset($this->path)) {
                // store the old name to delete after the update
                $this->temp = $this->path;
                $this->path = null;
            } else {
                $this->path = 'initial';
            }

            $this->setName();
            $this->setExtension();
            $this->setSize();
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadPath(), $this->path);

        // check if we have an old image
        if (isset($this->temp) && file_exists($this->getUploadPath() . '/' . $this->temp)) {
            // delete the old image
            unlink($this->getUploadPath() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @var
     */
    private $fullpath;

    /**
     * @return string
     */
    public function getFullpath()
    {
        return $this->folder->getPath() . '/' . $this->path;
    }
}
