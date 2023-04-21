<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Form\MediaType;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de la recette'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Les étapes de la recette'
                ]
            ])
            ->add('youtube', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Lien vers YouTube'
                ]
            ])
            ->add('people', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Pour combien de personne',
                'choices' => [
                    '2' => 2,
                    '4' => 4,
                    '6' => 6,
                    '8' => 8,
                    '10' => 10,
                    '12' => 12,
                    '+12' => +12,
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('level', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Niveau de difficulté',
                'choices' => [
                    'Très simple' => 'très simple',
                    'Facile' => 'facile',
                    'Un peu compliqué' => 'un peu compliqué',
                    'Plutôt difficile' => 'plutôt difficile',
                ],
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'asset_helper' => true,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => MediaType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisis une catégorie',
            ])
            ->add('ingredient', EntityType::class, [
                'required' => true,
                'class' => Ingredient::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (IngredientRepository $repository) {
                    return $repository->createQueryBuilder('i')
                        ->orderBy('i.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choisissez des ingrédients',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
