<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 05.10.18
 * Time: 12:55
 */

namespace App\Form;

use App\Entity\Waypoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WaypointFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('province')
            ->add('category')
            ->add('city');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Waypoint::class,
            ]
        );
    }
}
