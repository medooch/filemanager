<?php

namespace Core\FilemanagerBundle\Form;

use Core\FilemanagerBundle\Entity\Folders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FolderPermissions
 * @package Core\FilemanagerBundle\Form
 */
class FolderPermissions extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permissions', ChoiceType::class, array(
                /** permissions */
                'choices' => array(
                    'No permissions' => 0,
                    'Read Only' => 1,
                    'Read/Write' => 2,
                ),
                'choice_attr' => function () {
                    return ['class' => 'checkbox'];
                },
                /** radio button */
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'filemanager_bundle_folder_permissions';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Folders::class,
        ));
    }
}
