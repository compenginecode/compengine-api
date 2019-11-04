<?php

namespace DomainLayer\ORM\Notification\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DomainLayer\ORM\Notification\Notification;

/**
 * Class DatabaseNotificationRepository
 * @package DomainLayer\ORM\Notification\Repository
 */
class DatabaseNotificationRepository extends EntityRepository implements INotificationRepository
{
    /** getDailyNotifications
     *
     *
     *
     * @return array
     */
    public function getDailyNotifications() {
        return $this->_em->getRepository(Notification::class)->findBy([
            "frequency" => Notification::DAILY,
        ]);
    }

    /** getWeeklyNotifications
     *
     *
     *
     * @return array
     */
    public function getWeeklyNotifications() {
        return $this->_em->getRepository(Notification::class)->findBy([
            "frequency" => Notification::WEEKLY,
        ]);
    }
}
