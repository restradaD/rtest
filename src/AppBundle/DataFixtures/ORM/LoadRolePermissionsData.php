<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\RolePermission;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadRolePermissionsData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadRolePermissionsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $assignments = $this->getAssignments();

        foreach ($assignments as $role => $referenceCodes) {
            foreach ($referenceCodes as $referenceCode) {
                $rolePermission = new RolePermission();
                $rolePermission->setRole($role);
                $rolePermission->setPermission($this->getReference($referenceCode));

                $manager->persist($rolePermission);
                $manager->flush();
            }
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 30;
    }

    /**
     * @return array
     */
    protected function getAssignments()
    {
        return [
            'ROLE_ADMIN' => [
                'app.user.manage',
                'app.user.index',
                'app.user.new',
                'app.user.edit',
                'app.user.show',
                'app.user.delete',

            ],
            'ROLE_APP' => [],
            'ROLE_SUPER_ADMIN' => [
                'app.company.manage',
                'app.company.index',
                'app.company.new',
                'app.company.edit',
                'app.company.show',
                'app.company.delete',
                'app.permission.manage',
                'app.permission.index',
                'app.permission.new',
                'app.permission.edit',
                'app.permission.show',
                'app.permission.delete',
                'app.permission.assign',
                'app.user.manage',
                'app.user.index',
                'app.user.new',
                'app.user.edit',
                'app.user.show',
                'app.user.delete',
            ]
        ];
    }
}