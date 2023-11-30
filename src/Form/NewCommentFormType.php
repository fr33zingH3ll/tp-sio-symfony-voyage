<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Travel;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;    
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;

class NewCommentFormType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sender', TextType::class, [
                'label' => 'enter your email or leave empty if you wish to publish anonymously'
            ])
            ->add('message', TextType::class, [
                'label' => 'enter your comment'
            ])
            ->add('travel',EntityType::class, [
                // looks for choices from this entity
                'class' => Travel::class,
                'attr' => [
                    'style' => 'display: none;',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'travel' => null,  // Valeur par défaut pour le champ "travel"
        ]);

        // Déclarer l'option "travel" pour éviter l'erreur
        $resolver->setDefined(['travel']);
    }
}
