<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class UserType extends AbstractType {

    private $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Password confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => $this->roleHierarchy)
            )
        ;
    }

}