<?php

namespace App\Entity\EntityTraits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait GlobalMethodsTrait
{

    /**
     * @ORM\PrePersist
     */
    public function resolveCreatedAt()
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTime();
        }
    }
}
