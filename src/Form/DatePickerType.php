<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;


class DatePickerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('publishedAt', DateType::class, [
            'widget' => 'single_text',

            // prevents rendering it as type="date", to avoid HTML5 date pickers
            'html5' => false,

            // adds a class that can be selected in JavaScript
            'attr' => ['class' => 'js-datepicker'],
        ]);
    }


}