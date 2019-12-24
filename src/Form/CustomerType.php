<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('customer_type', ChoiceType::class, array(
                'choices' => array(
                    'Wholesaler'=> 'Wholesaler',
                    'Retailer'=> 'Retailer',
                    'End Customer' => 'End Customer'
                )
            ))
            ->add('place_no', TextType::class)
            ->add('street', TextType::class)
            ->add('city', ChoiceType::class, array(
                'choices' => array(
                    'Colombo' => 'Colombo',
                    'Negombo'=> 'Negombo',
                    'Galle' => 'Galle',
                    'Jaffna' => 'Jaffna', 
                    'Matara' => 'Matara',
                    'Trincomalee' => 'Trincomalee'
                )
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type'=> PasswordType::class,
                'attr' => ['class'=>'form-control'],
                'first_options' => array('label'=>'Password'),
                'second_options' => array('label'=>'Password')
            ), [
                'attr'=> ['class'=>'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
