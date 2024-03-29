<?php

namespace App\Twig;

use App\Entity\Waypoint;
use App\Service\WayPointHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use UnexpectedValueException;
use function get_class;

class AppExtension extends AbstractExtension
{
    private WayPointHelper $wayPointHelper;

    public function __construct(WayPointHelper $wayPointHelper)
    {
        $this->wayPointHelper = $wayPointHelper;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('cast_to_array', [$this, 'objectFilter']),
            new TwigFilter('intelLink', [$this, 'intelLink']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_url', [$this, 'getUrl']),
            new TwigFunction('hasImage', [$this, 'hasImage']),
        ];
    }

    public function getUrl(string $type): string
    {
        switch ($type) {
            case 'intel':
                return $_ENV['INTEL_URL'];
            default:
                throw new UnexpectedValueException('Unknown URL type: '.$type);
        }
    }

    /**
     * Convert object to array for Twig usage..
     *
     * @param object $classObject
     *
     * @return array<string, string>
     */
    public function objectFilter(object $classObject): array
    {
        $array = (array)$classObject;
        $response = [];

        $className = get_class($classObject);

        foreach ($array as $k => $v) {
            $response[trim(str_replace($className, '', $k))] = $v;
        }

        return $response;
    }

    public function intelLink(Waypoint $wayPoint): string
    {
        return sprintf(
            '%s/intel?ll=%s,%s&z=17&pll=%s,%s',
            $this->wayPointHelper->getIntelUrl(),
            $wayPoint->getLat(),
            $wayPoint->getLon(),
            $wayPoint->getLat(),
            $wayPoint->getLon(),
        );
    }

    public function hasImage(Waypoint $waypoint): bool
    {
        return (bool)$this->wayPointHelper->findImage($waypoint->getGuid());
    }
}
