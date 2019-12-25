<?php

namespace App\Form;

use App\Entity\TrainSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destination',ChoiceType::class, [
                'choices'  => [
                    'Colombo' => 'Colombo',
                    'Negambo' => 'Negambo',
                    'Galle' => 'Galle',
                    'Matara' => 'Matara',
                    'Jaffna' => 'Jaffna',
                    'Trincomalee'=> 'Trincomalee',
                ],
            ])
            ->add('capacity')
            ->add('start_time')
            ->add('journey_time')
            ->add('day', ChoiceType::class, [
                'choices'  => [
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday'=> 'Saturday',
                    'Sunday'=> 'Sunday',
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrainSchedule::class,
        ]);
    }
}
