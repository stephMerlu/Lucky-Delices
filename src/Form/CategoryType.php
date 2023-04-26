<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{

    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [
            'required' => true,
            'attr' => [
            'placeholder' => 'Nom de la catégorie'
            ]
        ])
        ->add('parent', EntityType::class, [
            'class' => Category::class,
            'required' => false,
            'placeholder' => 'Choisir une catégorie',
            'choices' => $this->categoryRepository->findEntreePlatDessertCategories(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
            $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}