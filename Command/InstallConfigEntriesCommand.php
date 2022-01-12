<?php

namespace Modera\ConfigBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Modera\ConfigBundle\Config\ConfigEntriesInstaller;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InstallConfigEntriesCommand extends Command
{
    private ConfigEntriesInstaller $installer;

    public function __construct(ConfigEntriesInstaller $installer)
    {
        $this->installer = $installer;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:config:install-config-entries')
            ->setDescription('Installs configuration-entries defined through extension-points mechanism')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(' >> Installing configuration-entries ...');

        $installedEntries = $this->installer->install();

        foreach ($installedEntries as $entry) {
            $output->writeln(sprintf('  - %s ( %s )', $entry->getName(), $entry->getReadableName()));
        }
        if (count($installedEntries) == 0) {
            $output->writeln(" >> There's nothing to install, aborting");
        } else {
            $output->writeln(' >> Done!');
        }

        return 0;
    }
}
