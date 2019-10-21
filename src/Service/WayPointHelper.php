<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 25.12.18
 * Time: 08:38
 */

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class WayPointHelper
{
    private $rootDir;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir.'/public/wp_images';
    }

    public function checkImage(int $wpId, string $imageUrl, bool $forceUpdate = false)
    {
        $fileSystem = new Filesystem();

        if (false === $fileSystem->exists($this->rootDir)) {
            $fileSystem->mkdir($this->rootDir);
        }

        $imagePath = $this->rootDir.'/'.$wpId.'.jpg';

        if (true === $fileSystem->exists($imagePath) && false === $forceUpdate) {
            return;
        }

        $ch = curl_init($imageUrl);
        $fp = fopen($imagePath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_VERBOSE, true);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function cleanName(string $name): string
    {
        $replacements = [
            'á' => 'a',
            'é'  => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'ni',
            'ü' => 'u'
        ];

        $name = trim($name);
        $name = str_replace(['.', ',', ';', ':', '"', '\'', '\\'], '', $name);

        $name = str_replace(array_keys($replacements), $replacements, $name);

        return $name;
    }
}
