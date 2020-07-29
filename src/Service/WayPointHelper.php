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
    private string $rootDir;
    private string $intelUrl;

    public function __construct(string $rootDir, string $intelUrl)
    {
        $this->rootDir = $rootDir.'/public/wp_images';
        $this->intelUrl = $intelUrl;
    }

    public function getImagePath(string $wpId):string
    {
        return $this->rootDir.'/'.$wpId.'.jpg';
    }

    public function findImage(?string $wpId): bool
    {
        if (!$wpId) {
            return false;
        }

        $fileSystem = new Filesystem();

        if (false === $fileSystem->exists($this->rootDir)) {
            $fileSystem->mkdir($this->rootDir);
        }

        $imagePath = $this->getImagePath($wpId);

        return $fileSystem->exists($imagePath) ? $imagePath : false;
    }

    public function checkImage(
        string $wpId,
        string $imageUrl,
        bool $forceUpdate = false
    ): void {
        $imagePath = $this->findImage($wpId);

        if ($imagePath && false === $forceUpdate) {
            return;
        }

        $imagePath = $this->getImagePath($wpId);

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

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getIntelUrl(): string
    {
        return $this->intelUrl;
    }

    public function cleanName(string $name): string
    {
        $replacements = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'Ó' => 'O',
            'ú' => 'u',
            'Ú' => 'U',
            'ñ' => 'ni',
            'ü' => 'ue',
        ];

        $name = trim($name);
        $name = str_replace(['.', ',', ';', ':', '"', '\'', '\\'], '', $name);

        $name = str_replace(array_keys($replacements), $replacements, $name);

        return $name;
    }
}
