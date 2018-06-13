<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\NotificationType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

/**
 * Class LoadNotificationTypeData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadNotificationTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var TranslationRepository $repository */
        $repository = $manager->getRepository('Gedmo\\Translatable\\Entity\\Translation');

        $taskAssigned = new NotificationType();
        $taskAssigned->setId(1);
        $taskAssigned->setName('Nueva tarea');
        $taskAssigned->setTextTemplate('%user% ha creado la tarea %title%.');
        $taskAssigned->setTranslatableLocale('es');

        $repository
            ->translate($taskAssigned, 'name', 'en', 'New Task')
            ->translate($taskAssigned, 'textTemplate', 'en', '%user% has created the task %title%.')

            ->translate($taskAssigned, 'name', 'pl_PL', 'Nowe zadanie')
            ->translate($taskAssigned, 'textTemplate', 'pl_PL', '%user% stworzył zadaniem %title%.')
        ;

        $manager->persist($taskAssigned);
        /** --------------------------------- **/

        $taskUnassigned = new NotificationType();
        $taskUnassigned->setId(2);
        $taskUnassigned->setName('Tarea actualizada');
        $taskUnassigned->setTextTemplate('%user% ha actualizado la tarea %title%.');
        $taskUnassigned->setTranslatableLocale('es');

        $repository
            ->translate($taskUnassigned, 'name', 'en', 'Task update')
            ->translate($taskUnassigned, 'textTemplate', 'en', '%user% has updated the task %title%.')

            ->translate($taskUnassigned, 'name', 'pl_PL', 'Zadanie aktualizacji')
            ->translate($taskUnassigned, 'textTemplate', 'pl_PL', '%user% uaktualnili zadanie %title%.')
        ;

        $manager->persist($taskUnassigned);
        /** --------------------------------- **/


        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 13;
    }
}