<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'required' => true,
                    'label' => "Titre de l'article",
                    'attr' =>
                        [
                            'class' => 'form-control',
                        ],
                ])
            //->add('media')
            ->add('content', TextareaType::class,
                [
                    'required' => true,
                    'label' => "Contenu de l'article",
                    'attr' =>
                        [
                            'class' => 'form-control',
                            'rows' => 7,
                        ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
