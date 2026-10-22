<?php

declare(strict_types=1);

namespace NurseryNest\Repository;

use PDO;

/**
 * Base class for every repository that reads or writes one nursery's data (NUR-3).
 * The nursery id is fixed at construction from the logged-in user's session, and every
 * query in a subclass must filter on it.
 */
abstract class TenantRepository
{
    public function __construct(
        protected readonly PDO $db,
        protected readonly int $nurseryId,
    ) {
        if ($nurseryId <= 0) {
            throw new \InvalidArgumentException('A tenant repository needs a nursery id.');
        }
    }

    public function nurseryId(): int
    {
        return $this->nurseryId;
    }
}
