<?php

namespace AppBundle\Pagination;

use Pagerfanta\Pagerfanta;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PaginationFactory
 * @package AppBundle\Pagination
 */
class PaginationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * PaginationFactory constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $filter
     * @param int $page
     * @param int $limit
     * @param $route
     * @param array $routeParams
     * @return PaginatedCollection
     */
    public function createCollection(QueryBuilder $qb, $filter = '', $page = 1, $limit = 10, $route, array $routeParams = array())
    {
        $adapter = new DoctrineORMAdapter($qb, true, false);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);
        $items = [];

        foreach ($pager->getCurrentPageResults() as $result) {
            $items[] = $result;
        }

        $paginatedCollection = new PaginatedCollection($items, $pager->getNbResults());

        $tmpParams = $routeParams;
        $routeParams['page'] = $page;

        if (isset($routeParams['sort_field'])) {
            unset($routeParams['sort_field']);
        }

        if (isset($routeParams['sort'])) {
            unset($routeParams['sort']);
        }

        if (!empty($filter)) {
            $routeParams['filter'] = $filter;
        }

        if (!empty($limit)) {
            $routeParams['limit'] = $limit;
        }

        $routeParams = $routeParams + $tmpParams;

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pager->getNbPages()));

        if ($pager->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pager->getNextPage()));
        }

        if ($pager->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pager->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}