<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Bundle\ScopeBundle\Model\ScopeCriteria;

class BannerRepository extends EntityRepository
{
    public function getActiveBanners(ScopeCriteria $criteria): mixed
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $expression = $queryBuilder->expr();

        $queryBuilder->from(Banner::class, 'banner')
            ->select('banner')
            ->innerJoin(
                Scope::class,
                'scope',
                Join::WITH,
                $queryBuilder->expr()->isMemberOf('scope', 'banner.scopes')
            )
            ->andWhere('banner.start < :currentDateTime')
            ->andWhere(
                $expression->orX(
                    $expression->isNull('banner.end'),
                    $expression->gt('banner.end', ':currentDateTime')
                )
            )
            ->andWhere('banner.enabled = true')
            ->setParameter('currentDateTime', new \DateTime())
            ->orderBy('banner.priority', 'DESC')
            ->addOrderBy('banner.updatedAt', 'DESC');

        $criteria->applyWhereWithPriority($queryBuilder, 'scope');

        return $queryBuilder->getQuery()->getResult();
    }
}
