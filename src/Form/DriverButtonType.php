<?php


namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\SubmitButton;

class DriverButtonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picked', SubmitType::class, ['label' => 'Picked up'])
            ->add('delivered', SubmitType::class, ['label' => 'Delivered'])
        ;
    }

}