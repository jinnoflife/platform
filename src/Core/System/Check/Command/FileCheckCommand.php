<?php declare(strict_types=1);

namespace Shopware\Core\System\Check\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Check\File;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Theme\StorefrontPluginRegistryInterface;
use Shopware\Storefront\Theme\ThemeEntity;
use Shopware\Storefront\Theme\ThemeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\MessageBusInterface;

class FileCheckCommand extends Command
{
    protected static $defaultName = 'check:file:hash';
    
    /**
     * @var SymfonyStyle
     */
    private $io;

    private $fileCheck;
    
    public function __construct(File $fileCheck) {
        parent::__construct($this->getName());

        $this->fileCheck = $fileCheck;
    }


    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $result = $this->fileCheck->checkFiles();
        
        foreach ($result as $fileCompare) {
            if(! (bool)$fileCompare['result']) {
                $this->io->warning($fileCompare['name']);
            }
        }
        
        return 0;
    }
}
