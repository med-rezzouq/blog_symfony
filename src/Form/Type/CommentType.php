<?php

declare(strict_types=1);

namespace App\Form\Type;


use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('comment', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // there is aussi setDefault()
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'label' => false,
            'validation_groups' => ["comment"],
        ]);
    }
}
