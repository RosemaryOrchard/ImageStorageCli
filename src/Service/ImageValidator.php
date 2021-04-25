<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImageValidator
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ImageValidator constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $filePath
     * @param SymfonyStyle|null $io
     * @return bool
     */
    public function basicValidate(string $filePath, SymfonyStyle $io = null): bool
    {
        if (!file_exists($filePath)) {
            return $this->logInvalidImage($io, 'Attempted to validate an image and the path did not exist (' . $filePath . ')');
        }
        if (!is_file($filePath)) {
            return $this->logInvalidImage($io, 'Attempted to validate an image which is not a file (' . $filePath . ')');
        }
        if (!getimagesize($filePath)) {
            return $this->logInvalidImage($io, 'Attempted to validate a file which is not an image (' . $filePath . ')');

        }
        return true;
    }

    /**
     * @param SymfonyStyle $io
     * @param string $message
     * @return bool
     */
    private function logInvalidImage(SymfonyStyle $io, string $message): bool
    {
        $this->logger->warning($message);
        //if there is no Symfony style this is not being run from the command line, add support for returning the message to the user in another fashion.
        if ($io) {
            $io->error($message);
        }
        return false;
    }
}