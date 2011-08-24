<?php

namespace Google\GeolocationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cleans cache entries
 */
class CleanCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('google:geolocation:clean-cache')
            ->setDescription('Clean Geolocation cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $geolocationApi = $this->getContainer()->get('google_geolocation.geolocation_api');

        try
        {
            $cleanCount = $geolocationApi->cleanCache();
            $output->writeln($cleanCount . " cache entries were removed");
        }
        catch (\Exception $e)
        {
            $output->writeln("Cache not available. Cannot clean!");
        }
    }
}
