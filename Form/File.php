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

namespace Core\FilemanagerBundle\Form;

use Core\FilemanagerBundle\Entity\Files;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


/**
 * Class File
 * @package Core\FilemanagerBundle\Form
 */
class File extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array('label' => 'Upload file'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Files::class,
        ));
    }
}
