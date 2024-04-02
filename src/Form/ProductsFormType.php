<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'label' => 'Nom'
            ])
            ->add('description')
            ->add('price', MoneyType::class, options: [
                'label' => 'Prix',
                'divisor' => 100,
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peut pas être négatif'
                    )
                ]
            ])
            ->add('stock', options: [
                'label' => 'Stock'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'query_builder' => function(CategoriesRepository $cr) {
                    return $cr->createQueryBuilder('c')
                    ->where('c.parent IS NOT NULL')
                    ->orderBy('c.name', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
