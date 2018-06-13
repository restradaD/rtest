<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\LockHandler;

class PushNotificationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:push-notifications')
            ->setDescription('Flush notifications spool')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler('send-push-notifications.lock');

        if ( !$lock->lock() ) {
            $output->writeln('This command is already running in another process.');
        } else {
            $entityManager = $this->getContainer()->get('doctrine')->getManager();

            $notifications = $entityManager->getRepository('AppBundle:Notification')
                ->findBy(['response' => null]);

            foreach ($notifications as $notification) {
                $this->getContainer()->get('app.tools')->sendNotification($notification);
            }

            $output->writeln('Command result.');
        }

        $lock->release();
    }

}
