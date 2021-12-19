<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Flusher;
use Doctrine\ORM\EntityManagerInterface;

final class DbFlusher implements Flusher
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
