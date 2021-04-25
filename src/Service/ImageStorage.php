<?php


namespace App\Service;

use App\Entity\StoredImage;
use App\Repository\StoredImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImageStorage
{
    /**
     * @var StoredImageRepository
     */
    private $imageRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var string
     */
    private $destination;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ImageStorage constructor.
     */
    public function __construct(StoredImageRepository $imageRepository, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->destination = 'images/';
        $this->logger = $logger;
    }

    public function saveImage(string $filePath, SymfonyStyle $io = null): ?StoredImage
    {
        $image = $this->checkImageIsStored($filePath);
        if (!$image) {
            $image = new StoredImage();
            $image->setStoredLocation($this->moveImageFile($filePath));
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            $this->cliOutput($io, 'The image was saved! ' . $image->getStoredLocation(), 'success');
        }
        return $image;
    }

    public function deleteImage(string $filePath, SymfonyStyle $io = null): bool
    {
        $image = $this->checkImageIsStored($filePath);
        if (!$image) {
            $this->cliOutput($io, 'Image not found! ' . $filePath, 'error');
            return false;
        }
        unlink($image->getStoredLocation());
        $this->entityManager->remove($image);
        $this->entityManager->flush();
        return true;
    }

    public function listImages(): array
    {
        return $this->imageRepository->findAll();
    }

    private function checkImageIsStored(string $filePath): ?StoredImage
    {
        return $this->imageRepository->findOneBy([
            'storedLocation' => $filePath
        ]);
    }

    private function moveImageFile(string $filePath): string
    {
        $filePathArray = explode('/', $filePath);
        $fileName = array_pop($filePathArray);
        $newFilePath = $this->destination . $fileName;
        rename($filePath, $newFilePath);
        $this->logger->debug('Saved ' . $filePath . ' to ' . $newFilePath);

        return $newFilePath;
    }

    private function cliOutput(SymfonyStyle $io, string $message, $type = null): void
    {
        if ($io) {
            switch ($type) {
                case 'error':
                    $io->error($message);
                    break;
                case 'success':
                    $io->success($message);
                    break;
                default:
                    $io->writeln($message);
                    break;
            }
        }
    }


}