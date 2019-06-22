<?php


namespace Notification\Services;


use Notification\Models\Notification;
use Notification\Models\NotificationTypes;

/**
 * Class EventListener
 * @package Notification\Services
 */
class EventListener implements \Application_Model_Events_Observer
{

    const NEW_GROUP_MESSAGE = 1;
    const NEW_PERSONAL_MESSAGE = 2;
    const NEW_WISH = 3;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * EventListener constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct (NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param \SplSubject $subject
     *
     * @throws \Exception
     */
    public function update (\SplSubject $subject)
    {
        if (!$subject instanceof \Application_Model_Events_Subject) {
            return;
        }

        foreach ($subject->getEvents() as $event) {
            switch ($event['event']) {
                case \Nachrichten_Service_Nachrichten::NEW_MESSAGE_EVENT:
                    $this->notificationService->handle(
                        $event['messageId'],
                        NotificationTypes::PERSONAL_MESSAGE
                    );
                    break;
                case \Feedback\Services\Wish::NEW_WISH_EVENT:
                    $this->notificationService->handle(
                        $event['wishId'],
                        NotificationTypes::WISH
                    );
                    break;
            }
        }
    }


    private function findNotificationByMessageId() {

    }


}