<?php

namespace DomainLayer\ORM\Notification\Repository;

/**
 * Interface INotificationRepository
 * @package DomainLayer\ORM\Notification\Repository
 */
interface INotificationRepository
{
    /** getDailyNotifications
     *
     *
     *
     * @return array
     */
    public function getDailyNotifications();

    /** getWeeklyNotifications
     *
     *
     *
     * @return array
     */
    public function getWeeklyNotifications();
}
