<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Infrastructure\Doctrine\Shared\QB;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DbAttributeRepository extends ServiceEntityRepository implements AttributeRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Attribute::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function get(AttributeId $attrId): Attribute
    {
        $attr = $this->find($attrId->getVal());
        if ($attr === null) {
            throw EntityNotFoundException::byId($attrId, Attribute::NAME);
        }

        return $attr;
    }

    public function hasByName(string $name): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new QB(
                $this
                    ->createQueryBuilder('a')
                    ->select('COUNT(a.attributeId)')
            ))
                ->whereFieldLike(Attribute::FIELD_NAME, $name, 'a')
                ->getORMQB()
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function findByName(string $name): ?Attribute
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new QB($this->createQueryBuilder('a')))
            ->whereFieldLike(Attribute::FIELD_NAME, $name, 'a')
            ->getORMQB()
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function add(Attribute $attr): void
    {
        $this->getEntityManager()->persist($attr);
    }
}
