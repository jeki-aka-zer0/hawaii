<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
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
        $attr = $this->find($attrId->getValue());
        if ($attr === null) {
            throw EntityNotFoundException::byId($attrId, Attribute::NAME);
        }

        return $attr;
    }

    public function hasByName(string $name): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->createQueryBuilder('a')
                ->select('COUNT(a.attributeId)')
                ->andWhere(sprintf('lower(a.%s) = :name', Attribute::FIELD_NAME))
                ->setParameter('name', mb_strtolower($name))
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function add(Attribute $attr): void
    {
        $this->getEntityManager()->persist($attr);
    }
}
