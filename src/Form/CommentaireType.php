<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire')
            // ->add('datePublication')
            // ->add('etat')
            // ->add('auteur', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'nom'
            // ])
            // ->add('article')
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'titre'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter un commentaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
