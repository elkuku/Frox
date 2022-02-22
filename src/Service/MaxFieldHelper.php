<?php

namespace App\Service;

use DirectoryIterator;
use Elkuku\MaxfieldParser\MaxfieldParser;
use Elkuku\MaxfieldParser\Type\MaxField;

class MaxFieldHelper
{
    public function __construct(private string $rootDir, private int $maxfieldVersion)
    {
        $this->rootDir = $rootDir.'/public/maxfields';
    }

    public function getList(): array
    {
        $list = [];

        foreach (new DirectoryIterator($this->rootDir) as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $list[] = $fileInfo->getFilename();
            }
        }

        sort($list);

        return $list;
    }

    public function getMaxField(string $item): MaxField
    {
        return (new MaxfieldParser($this->rootDir))
            ->parse($item);
    }

    public function getMaxfieldVersion(): int
    {
        return $this->maxfieldVersion;
    }
}
