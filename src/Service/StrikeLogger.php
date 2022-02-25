<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class StrikeLogger
{
    private string $logFile = '';

    private Filesystem $fileSystem;

    private bool $started = false;

    public function __construct(string $rootDir)
    {
        $this->fileSystem = new Filesystem();
        $this->logFile = $rootDir.'/var/log/strikelog.txt';
    }

    public function start(): self
    {
        if ($this->fileSystem->exists($this->logFile)) {
            $this->fileSystem->remove($this->logFile);
        } else {
            $this->fileSystem->touch($this->logFile);
        }

        $this->started = true;

        return $this;
    }

    public function add(string $string, bool $newLine = true): self
    {
        if (!$this->started) {
            $this->start();
        }

        $nl = $newLine ? "\n" : '';

        $this->fileSystem->appendToFile($this->logFile, $string.$nl);

        return $this;
    }

    public function getLog(): string
    {
        if ($this->fileSystem->exists($this->logFile)) {
            return file_get_contents($this->logFile);
        }

        return 'Log file not found :(';
    }
}
