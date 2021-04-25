<?php

namespace App\Command;

use App\Entity\StoredImage;
use App\Service\ImageStorage;
use App\Service\ImageValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImageStorageCommand extends Command
{
    protected static $defaultName = 'app:image-storage';
    protected static $defaultDescription = 'Add a short description for your command';
    /**
     * @var ImageValidator
     */
    private $imageValidator;
    /**
     * @var ImageStorage
     */
    private $imageStorage;

    /**
     * ImageStorageCommand constructor.
     */
    public function __construct(ImageValidator $imageValidator, ImageStorage $imageStorage)
    {
        parent::__construct();
        $this->imageValidator = $imageValidator;
        $this->imageStorage = $imageStorage;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->setHelp('Pass the path of a file to this along with the action you want to take')
            ->addArgument('image', InputArgument::OPTIONAL, 'file path to image')
            ->addOption('save', 's', InputOption::VALUE_NONE, 'Save Image')
            ->addOption('delete', 'rm', InputOption::VALUE_NONE, 'Delete image')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List stored images');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $imageFilePath = $input->getArgument('image');

        if ($imageFilePath) {
            if ($io->isDebug()) {
                $io->note(sprintf('You passed an argument: %s', $imageFilePath));
            }
            if (!$this->imageValidator->basicValidate($imageFilePath, $io)) {
                return Command::FAILURE;
            }
            if ($input->getOption('save')) {
                $image = $this->imageStorage->saveImage($imageFilePath, $io);
                if ($image) {
                    return Command::SUCCESS;
                }

                $io->error('A problem was encountered saving your image');
                return Command::FAILURE;
            }
            if ($input->getOption('delete')) {
                $delete = $this->imageStorage->deleteImage($imageFilePath);
                if ($delete) {
                    return Command::SUCCESS;
                }
                $io->error('A problem was encountered deleting your image');
                return Command::FAILURE;
            }
        } elseif ($input->getOption('list')) {
            $images = $this->imageStorage->listImages();
            $imageQuestion = new ChoiceQuestion('Choose from one of the following images', $images);
            /** @var StoredImage $chosenImage */
            $chosenImage = $io->askQuestion($imageQuestion);

            $actionQuestion = new ChoiceQuestion('Choose an action', ['nothing', 'delete'], 0);
            $action = $io->askQuestion($actionQuestion);

            switch ($action) {
                case 'nothing':
                    return Command::SUCCESS;
                case 'delete':
                    $delete = $this->imageStorage->deleteImage($chosenImage->getStoredLocation());
                    if ($delete) {
                        return Command::SUCCESS;
                    }
                    break;
            }
        } else {
            $io->error('You did not provide a file path to an image or request a list of stored images.');
            return Command::FAILURE;
        }

        $io->warning('A problem was encountered, please try the command with -h for help.');

        return Command::SUCCESS;
    }
}
