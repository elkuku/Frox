<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Nette\Set\NetteSetList;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use RectorPrefix20220117\Symplify\SymfonyPhpConfig\ValueObjectInliner;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    // $services = $containerConfigurator->services();
    // $services->set(AnnotationToAttributeRector::class)
    //     ->call('configure', [[
    //                              AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
    //                                  new AnnotationToAttribute(
    //                                      IsGranted::class,
    //                                      IsGranted::class
    //                                  ),
    //                              ]),
    //                          ]]);

    // $containerConfigurator->import(DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    //
    // $containerConfigurator->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION);
    // $containerConfigurator->import(SymfonySetList::SYMFONY_CODE_QUALITY);
    // $containerConfigurator->import(SymfonySetList::SYMFONY_STRICT);
    // $containerConfigurator->import(SymfonySetList::SYMFONY_60);
    //
    // $containerConfigurator->import(NetteSetList::ANNOTATIONS_TO_ATTRIBUTES);

    // $containerConfigurator->import(SetList::EARLY_RETURN);
    // $containerConfigurator->import(LevelSetList::UP_TO_PHP_81);
    // $containerConfigurator->import(SymfonyLevelSetList::UP_TO_SYMFONY_60);



    return;

    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
