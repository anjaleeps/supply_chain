<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('unit_price')
            ->add('size')
            ->add('picture')
            ->add('category',ChoiceType::class, [
                'choices'  => [
                    'Food' => 'Food',
                    'Cloth' => 'Cloth',
                    'Electronic' => 'Electronic',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Available' => 'Available',
                    'Not Availabe' => 'Not Available',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
