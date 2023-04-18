<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullName', TextType::class)
            ->add('email', TextType::class)
            ->add('phone', TextType::class)
            ->add('address', TextareaType::class);

        /*
         * le cas d'un champs non mappé par l'entité
         */
        //             ->add('test', ChoiceType::class, [
        //                 'mapped' => false,
        //                 'choices' => [
        //                     'key1' => 'value1',
        //                     'key2' => 'value2',
        //                     'key3' => 'value3',
        //                 ],
        // //                'expanded' => true,
        //             ])
        //         ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // there is aussi setDefault()
        $resolver->setDefaults([
            'data_class' => User::class,
            'label' => false,
            'validation_groups' => ['profile'],
        ]);
    }
}
