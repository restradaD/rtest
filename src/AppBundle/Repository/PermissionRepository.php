<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\EntityRepository;

/**
 * PermissionRepository
 *
 */
class PermissionRepository extends EntityRepository
{
    /**
     * Filter user Repository.
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @return QueryBuilder
     * */
    public function findAllQueryBuilder($search = '', $sortField = 'id', $sortDirection = 'DESC')
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ( !empty($search) ) {
            $availableFieldsForSearch = ['p.code', 'p.description'];

            foreach ($availableFieldsForSearch as $index => $field) {
                $where = $field.' LIKE :search';

                if (0 === $index) {
                    $queryBuilder->where($where);
                } else {
                    $queryBuilder->orWhere($where);
                }
            }

            $queryBuilder
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        return $queryBuilder->orderBy('p.' . $sortField, $sortDirection);
    }
}
