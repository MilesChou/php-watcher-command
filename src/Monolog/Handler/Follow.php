<?php

namespace Watcher\Command\Monolog\Handler;

use SplFileObject;
use Symfony\Component\Console\Output\OutputInterface;
use Watcher\Command\Exception\InvalidLineSizeException;
use Watcher\Handler\HandlerAbstract;
use Watcher\Util\File;

/**
 * Follow Class for Monolog
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Follow extends HandlerAbstract
{
    /**
     * @var int
     */
    private $lines;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var int[]
     */
    private $size = [];

    /**
     * @param string $file
     * @param SplFileObject $fileObject
     */
    private function init($file, $fileObject)
    {
        if (null !== $this->lines) {
            $totalLines = File::getTotalLines($file);

            if ($totalLines < $this->lines) {
                throw new InvalidLineSizeException("Line size is invalid, total lines is $totalLines");
            }
            $fileObject->seek($totalLines - $this->lines);

            while (!$fileObject->eof()) {
                $text = $fileObject;
                echo $this->display($text);

                $fileObject->next();
            }
        }
    }

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param string $text
     */
    private function display($text)
    {
        $patterns = [];
        $patterns[] = '/^.*DEBUG/';
        $patterns[] = '/^.*INFO/';
        $patterns[] = '/^.*NOTICE/';
        $patterns[] = '/^.*WARNING/';
        $patterns[] = '/^.*ERROR/';
        $patterns[] = '/^.*CRITICAL/';
        $patterns[] = '/^.*ALERT/';
        $patterns[] = '/^.*EMERGENCY/';

        $replacements = [];
        $replacements[] = '[<fg=white>DEBU</>]';
        $replacements[] = '[<fg=blue>INFO</>]';
        $replacements[] = '[<fg=yellow>NOTI</>]';
        $replacements[] = '[<fg=yellow>WARN</>]';
        $replacements[] = '[<fg=red>ERRO</>]';
        $replacements[] = '[<fg=red>CRIT</>]';
        $replacements[] = '[<fg=red>ALER</>]';
        $replacements[] = '[<fg=red>EMER</>]';

        $text = preg_replace($patterns, $replacements, $text);
        $this->output->write($text);
    }

    /**
     * @param string $file
     * @param bool $isInit
     */
    public function invoke($file, $isInit)
    {
        $fileObject = new SplFileObject($file);

        $currentSize = filesize($file);
        if (!isset($this->size[$file])) {
            $this->size[$file] = $currentSize;
        }

        if ($isInit) {
            $this->init($file, $fileObject);
            return;
        }

        $fileObject->fseek($this->size[$file]);

        while (!$fileObject->eof()) {
            $this->display($fileObject->fgets());
        }

        $fileObject = null;

        $this->size[$file] = $currentSize;
    }

    /**
     * @param int $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }
}
