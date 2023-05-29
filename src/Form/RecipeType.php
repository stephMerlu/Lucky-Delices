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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', null, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de la recette'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Etapes',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Les étapes de la recette'
                ]
            ])
            ->add('youtube', null, [
                'label' => 'Youtube',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Lien vers YouTube'
                ]
            ])
            ->add('people', ChoiceType::class, [
                'label' => 'Nombre de personnes',
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
                'label' => 'Difficulté',
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
                'label' => 'Photo',
                'required' => false,
                'allow_delete' => true,
                'asset_helper' => true,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Categorie',
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisis une catégorie',
            ])
            ->add('ingredient', EntityType::class, [
                'label' => 'Ingrédient',
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
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'Sous-titre',
                'required' => true,
            ])
            ->add('time', IntegerType::class, [
                'label' => 'Temps de préparation (en minutes)',
                'required' => true,
            ])
            ->add('favorite', CheckboxType::class, [
                'label' => 'Favorite',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
