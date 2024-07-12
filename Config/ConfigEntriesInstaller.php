<?php

namespace Modera\ConfigBundle\Config;

use Doctrine\ORM\EntityManagerInterface;
use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Modera\ExpanderBundle\Ext\ContributorInterface;

/**
 * Collects instances of {@class ConfigurationEntry} from the system and persists them to the database. If a
 * configuration entry already exists it won't be updated.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigEntriesInstaller
{
    private ContributorInterface $provider;

    private EntityManagerInterface $em;

    public function getProvider(): ContributorInterface
    {
        return $this->provider;
    }

    public function __construct(ContributorInterface $provider, EntityManagerInterface $em)
    {
        $this->provider = $provider;
        $this->em = $em;
    }

    private function findEntry(ConfigurationEntryDefinition $entryDef): ?ConfigurationEntry
    {
        return $this->em->getRepository(ConfigurationEntry::class)->findOneBy(['name' => $entryDef->getName()]);
    }

    /**
     * @return ConfigurationEntryDefinition[]
     */
    public function install(): array
    {
        $installedEntries = [];

        foreach ($this->provider->getItems() as $entryDef) {
            /** @var ConfigurationEntryDefinition $entryDef */
            $entry = $this->findEntry($entryDef);
            if (!$entry) {
                $installedEntries[] = $entryDef;
                $entry = ConfigurationEntry::createFromDefinition($entryDef);
            } else {
                $entry->applyDefinition($entryDef);
            }
            $this->em->persist($entry);
        }
        $this->em->flush();

        return $installedEntries;
    }
}
