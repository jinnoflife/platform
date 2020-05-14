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

class GenerateFileHashesCommand extends Command
{
    protected static $defaultName = 'check:file:hash:generate';
    
    /**
     * @var SymfonyStyle
     */
    private $io;

    /** @var string */
    private $projectDir;
    
    public function __construct(string $projectDir) {
        parent::__construct($this->getName());

        $this->projectDir = $projectDir;
    }


    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $finder = (new Finder())
            ->in($this->projectDir . '/src/')
            ->in($this->projectDir . '/vendor/shopware/')
            ->name('*.php')
            ->name('*.js')
            ->name('*.html.twig')
            ->name('*.scss')
            ->notPath('node_modules')
            ->followLinks()->files();

        $this->io->progressStart($finder->count());
        foreach ($finder->getIterator() as $fileInfo) {
            $curFileName = $fileInfo->getRealPath();
            $hashes[$curFileName] = hash('sha256', file_get_contents($curFileName));
            
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();
        

        file_put_contents(__DIR__ .'/../hashes.sha256', json_encode($hashes));
        
        return 0;
    }
}
