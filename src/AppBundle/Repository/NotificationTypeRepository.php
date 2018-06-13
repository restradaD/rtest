<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\EntityRepository;

/**
 * NotificationTypeRepository
 */
class NotificationTypeRepository extends EntityRepository
{
    /**
     * Filter Notification Type Repository.
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @return QueryBuilder
     * */
    public function findAllQueryBuilder($search = null, $sortField = 'createdAt', $sortDirection = 'DESC')
    {
        $queryBuilder = $this->createQueryBuilder('n');

        if ( !empty($search) ) {
            $availableFieldsForSearch = ['n.id', 'n.name'];

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

        return $queryBuilder->orderBy('n.' . $sortField, $sortDirection);
    }
}
