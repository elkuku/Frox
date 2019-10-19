<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('cast_to_array', [$this, 'objectFilter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_url', [$this, 'getUrl']),
        ];
    }

    public function getUrl($type)
    {
        switch ($type) {
            case 'intel':
                return getenv('INTEL_URL');
                break;
            default:
                throw new \UnexpectedValueException('Unknown URL type: '.$type);
        }
    }

    /**
     * Convert object to array for Twig usage..
     *
     * @param object $classObject
     *
     * @return array
     */
    public function objectFilter($classObject): array
    {
        $array    = (array)$classObject;
        $response = [];

        $className = \get_class($classObject);

        foreach ($array as $k => $v) {
            $response[trim(str_replace($className, '', $k))] = $v;
        }

        return $response;
    }
}
