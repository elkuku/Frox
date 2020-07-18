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
                    'label'       => 'Province',
                    'class'       => Province::class,
                    'placeholder' => 'Select a Province...',
                    'required'    => false,
                ]
            )
            ->add(
                'city',
                null,
                [
                    'label'    => 'City',
                    'required' => false,
                    'attr'     => ['list' => 'citiesList'],
                ]
            )
            ->add('intelLink', null, ['required' => false])
            ->add(
                'gpxRaw',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'csvRaw',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'idmcsvRaw',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'JsonRaw',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'OffleJson',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'multiexportcsv',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
            ->add(
                'multiexport-json',
                TextareaType::class,
                [
                    'attr'     => ['cols' => '30', 'rows' => '5'],
                    'required' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
