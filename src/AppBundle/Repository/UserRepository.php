<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 */
class UserRepository extends EntityRepository
{
    /**
     * Return one valid user by criteria
     * @param array $criteria
     * @param array $orderBy
     * @return mixed
     * */
    public function findOneValidBy(array $criteria, array $orderBy = null)
    {
        $criteria['enabled'] = true;
        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);

        return $persister->load($criteria, null, null, array(), null, 1, $orderBy);
    }

    /**
     * Filter user Repository.
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @return QueryBuilder
     * */
    public function findAllQueryBuilder($search = '', $sortField = 'id', $sortDirection = 'DESC')
    {
        $availableFieldsForSearch = ['u.first_name', 'u.last_name', 'u.username', 'u.email'];
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder
            ->where('u.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        if ( !empty($search) ) {
            foreach ($availableFieldsForSearch as $index => $field) {
                $where = $field.' LIKE :search';

                if (0 === $index) {
                    $queryBuilder->andWhere($where);
                } else {
                    $queryBuilder->orWhere($where);
                }
            }

            $queryBuilder
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        return $queryBuilder->orderBy('u.' . $sortField, $sortDirection);
    }
}
