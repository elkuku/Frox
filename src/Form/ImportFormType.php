<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 05.10.18
 * Time: 12:55
 */

namespace App\Form;

use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'province',
                EntityType::class,
                [
                    'class'       => Province::class,
                    'placeholder' => 'Select a Province...',
                    'required'    => false,
                ]
            )
            ->add(
                'city',
                null,
                [
                    'required' => false,
                ]
            )
            ->add('intelLink')
            ->add(
                'gpxRaw',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '10'],
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}