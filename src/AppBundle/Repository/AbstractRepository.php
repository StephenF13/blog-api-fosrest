<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PagerFanta\Adapter\DoctrineORMAdapter;
use PagerFanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit = 20, $offset = 0)
    {
        $limit = (int)$limit;

        if (0 === $limit) {
            throw new \LogicException('$limit must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pager->setCurrentPage(ceil(($offset + 1) / $limit));
        $pager->setMaxPerPage((int)$limit);

        return $pager;
    }
}