<?php

namespace AppBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class CheckFilesystemCommand
 * @package AppBundle\Command
 */
class CheckFilesystemCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('docroot', 'd', InputOption::VALUE_REQUIRED, 'Document root', null),
            ))
            ->setName('app:check-filesystem')
            ->setDescription('Flush notifications spool')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Checking Basic Filesystem Structure.');
        $io->block('These folders should exists and be writable.');

        $rows = $this->checkFilesystem($input);

        $this->displayTable($io, $rows);
        $this->fixProblems($io, $rows);

        $io->note('Remember: Code');
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function getNonWritableFolders(array $rows)
    {
        $nonWritableFolders = [];

        foreach ($rows as $row) {
            if ('No' === $row['writable']) {
                $nonWritableFolders[] = $row['folder'];
            }
        }

        return $nonWritableFolders;
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function getNonExistentFolders(array $rows)
    {
        $nonExistentFolders = [];

        foreach ($rows as $row) {
            if ('No' === $row['exists']) {
                $nonExistentFolders[] = $row['folder'];
            }
        }

        return $nonExistentFolders;
    }

    /**
     * @param SymfonyStyle $io
     * @param array $rows
     */
    protected function fixProblems(SymfonyStyle $io, array $rows)
    {
        $bar = $io->createProgressBar(100);
        $nonExistentFolders = $this->getNonExistentFolders($rows);
        $nonWritableFolders = $this->getNonWritableFolders($rows);

        $totalOfIssues = count($nonExistentFolders) + count($nonWritableFolders);

        if ( $totalOfIssues > 0) {
            $io->comment('Fixing...');

            $steps = ceil(92/$totalOfIssues);
            $bar->advance(8);

            $this->fixNonExistentFolderIssues($nonExistentFolders, $bar, $steps);
            $this->fixNonWritableFolderIssues($nonWritableFolders, $bar, $steps);

            $io->newLine();
        }
    }

    /**
     * @param array $nonWritableFolders
     * @param ProgressBar $bar
     * @param $steps
     */
    protected function fixNonWritableFolderIssues(array $nonWritableFolders, ProgressBar &$bar, $steps)
    {
        foreach ($nonWritableFolders as $nonWritableFolder) {
            if (chmod($nonWritableFolder, 0777)) {
                $bar->advance($steps);
            }

            sleep(1);
        }
    }

    /**
     * @param array $nonExistentFolders
     * @param ProgressBar $bar
     * @param $steps
     */
    protected function fixNonExistentFolderIssues(array $nonExistentFolders, ProgressBar &$bar, $steps)
    {
        $fs = new Filesystem();

        foreach ($nonExistentFolders as $nonExistentFolder) {
            $fs->mkdir($nonExistentFolder);

            if ($fs->exists($nonExistentFolder)) {
                $bar->advance($steps);
            }

            sleep(1);
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param array $rows
     */
    protected function displayTable(SymfonyStyle $io, array $rows)
    {
        $io->table(array('Folder', 'Exists', 'Writable'), $rows);
    }

    /**
     * @param SymfonyStyle $io
     * @param array $rows
     */
    protected function displayErrors(SymfonyStyle $io, array $rows)
    {
        foreach ($rows as $row) {
            $folder = $row['folder'];

            if ('No' === $row['exists']) {
                $io->error($folder . ' does not exists.');
            } elseif ('No' === $row['writable']) {
                $io->error($folder . ' is not writable.');
            }
        }
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function checkFilesystem(InputInterface $input)
    {
        $rows = [];
        $fs = new Filesystem();
        $folders = $this->getFoldersToCheck($input);

        foreach ($folders as $key => $folder) {
            $exists = $fs->exists($folder);

            $rows[$key]['folder'] = $folder;
            $rows[$key]['exists'] = $exists ? 'Yes' : 'No';

            if ($exists) {
                $rows[$key]['writable'] = chmod($folder, 0777) ? 'Yes': 'No';
            } else {
                $rows[$key]['writable'] = 'No';
            }

        }

        return $rows;
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getFoldersToCheck(InputInterface $input)
    {
        $pathCollection = [];
        $documentRoot = $input->getOption('docroot');

        if (null === $documentRoot) {
            $documentRoot = realpath($this->getContainer()->getParameter('kernel.root_dir').'/../web');
        }

        $folders = $this->getContainer()->getParameter('web_folders');

        foreach ($folders as $folder) {
            $pathCollection[] = $documentRoot . '/' . $folder;
        }

        return $pathCollection;
    }

}
