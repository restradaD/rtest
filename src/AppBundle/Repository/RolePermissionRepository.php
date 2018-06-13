<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * RolePermissionRepository
 *
 */
class RolePermissionRepository extends EntityRepository
{
    /**
     * find Role Permission by roles.
     * @param array $roles
     * @param string $attribute
     * @param string $entityName
     * @return array
     * */
    public function findByRoles(array $roles, $attribute, $entityName)
    {
        $code = "app.$entityName.$attribute";

        $queryBuilder = $this->createQueryBuilder('rp')
            ->select('count(rp.id)')
            ->leftJoin('rp.permission', 'p')
            ->where('rp.role IN (:roles)')
            ->andWhere('p.code = :code')
            ->setParameters(['roles' => $roles, 'code' => $code])
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
