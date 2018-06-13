<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\EntityRepository;

/**
 * NotificationRepository
 */
class NotificationRepository extends EntityRepository
{
    /**
     * Filter notification Repository.
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @return QueryBuilder
     * */
    public function findAllQueryBuilder($search = null, $sortField = 'id', $sortDirection = 'DESC')
    {
        $queryBuilder = $this->createQueryBuilder('n');

        if ( !empty($search) ) {
            $availableFieldsForSearch = ['n.title', 'n.description', 'to_user.username',
                'to_user.first_name', 'to_user.last_name', 'to_user.email', 'from_user.username',
                'from_user.first_name', 'from_user.last_name', 'from_user.email'];

            $queryBuilder
                ->leftJoin('n.to', 'to_user')
                ->leftJoin('n.from', 'from_user')
            ;

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
