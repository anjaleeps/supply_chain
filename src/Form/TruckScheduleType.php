<?php

namespace App\Form;

use App\Entity\TruckSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TruckScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_time')
            ->add('end_time')
            ->add('status')
            ->add('truck')
            ->add('driver')
            ->add('driver_assistant')
            ->add('route')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TruckSchedule::class,
        ]);
    }
}
