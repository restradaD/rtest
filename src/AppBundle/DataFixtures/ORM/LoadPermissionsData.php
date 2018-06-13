<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Permission;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

/**
 * Class LoadPermissionsData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadPermissionsData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $domain = 'app';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var TranslationRepository $repository */
        $repository = $manager->getRepository('Gedmo\\Translatable\\Entity\\Translation');

        foreach ($this->getData() as $module => $actions) {
            foreach ($actions as $action => $locales) {
                $code = $this->domain . '.' . $module . '.' . $action;
                $permission = $this->createPermission($code, $manager);

                foreach ($locales as $locale => $metadata) {
                    foreach ($metadata as $key => $value) {
                        $repository->translate($permission, $key, $locale, $value);
                        $manager->flush();
                    }
                }
            }
        }
    }


    /**
     * @param $code
     * @param ObjectManager $manager
     * @return Permission
     */
    protected function createPermission($code, ObjectManager $manager)
    {
        $permission = new Permission();
        $permission->setCode($code);
        $permission->setDescription('');
        $manager->persist($permission);
        $manager->flush();

        $this->addReference($code, $permission);

        return $permission;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }

    protected function getData()
    {
        return [
            'user' => [
                'manage' => [
                    'en' => ['description' => 'User management'],
                    'es' => ['description' => 'Administración de usuarios'],
                ],
                'index' => [
                    'en' => ['description' => 'User list'],
                    'es' => ['description' => 'Listado de usuarios'],
                ],
                'new' => [
                    'en' => ['description' => 'User creation'],
                    'es' => ['description' => 'Creación de usuarios'],
                ],
                'edit' => [
                    'en' => ['description' => 'User update'],
                    'es' => ['description' => 'Actualización de usuarios'],
                ],
                'show' => [
                    'en' => ['description' => 'Show single user'],
                    'es' => ['description' => 'Mostrar usuarios individuales'],
                ],
                'delete' => [
                    'en' => ['description' => 'Remove user'],
                    'es' => ['description' => 'Remover usuarios'],
                ]
            ],
            'company' => [
                'manage' => [
                    'en' => ['description' => 'Company management'],
                    'es' => ['description' => 'Administración de empresas'],
                ],
                'index' => [
                    'en' => ['description' => 'Company list'],
                    'es' => ['description' => 'Listado de empresas'],
                ],
                'new' => [
                    'en' => ['description' => 'New company'],
                    'es' => ['description' => 'Nueva empresa'],
                ],
                'edit' => [
                    'en' => ['description' => 'company update'],
                    'es' => ['description' => 'Edición de empresa'],
                ],
                'show' => [
                    'en' => ['description' => 'Show single company'],
                    'es' => ['description' => 'Mostrar empresas individuales'],
                ],
                'delete' => [
                    'en' => ['description' => 'Remove company'],
                    'es' => ['description' => 'Remover empresa'],
                ]
            ],
            'permission' => [
                'manage' => [
                    'en' => ['description' => 'Permission management'],
                    'es' => ['description' => 'Administración de permisos'],
                ],
                'index' => [
                    'en' => ['description' => 'Permission list'],
                    'es' => ['description' => 'Listado de permisos'],
                ],
                'new' => [
                    'en' => ['description' => 'Permission creation'],
                    'es' => ['description' => 'Creación de permisos'],
                ],
                'edit' => [
                    'en' => ['description' => 'Permission update'],
                    'es' => ['description' => 'Actualización de permisos'],
                ],
                'show' => [
                    'en' => ['description' => 'Show single permission'],
                    'es' => ['description' => 'Mostrar detalle del permiso'],
                ],
                'delete' => [
                    'en' => ['description' => 'Remove permission'],
                    'es' => ['description' => 'Remover permiso'],
                ],
                'assign' => [
                    'en' => ['description' => 'Permission assignment'],
                    'es' => ['description' => 'Asignación de permisos'],
                ],
            ]
        ];
    }
}