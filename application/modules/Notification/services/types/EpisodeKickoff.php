<?php

namespace Notification\Services\Types;


use Notification\Models\Notification;
use Notification\Models\Mappers\NotificationMapper;
use Notification\Models\NotificationSubject;
use Notification\Models\NotificationTypes;
use Notification\Models\View\EpisodeKickoffSubject;
use Notification\Services\NotificationService;

/**
 * Class EpisodeKickoff
 * @package Notification\Services\Types
 */
class EpisodeKickoff extends NotificationService
{

    /**
     * @var NotificationMapper
     */
    private $notificationMapper;
    /**
     * @var \Story_Model_Mapper_EpisodeMapper
     */
    private $episodeMapper;

    /**
     * GroupEntry constructor.
     */
    public function __construct ()
    {
        $this->episodeMapper = new \Story_Model_Mapper_EpisodeMapper();
    }

    /**
     * @param Notification $notification
     *
     * @throws \Exception
     */
    public function create (Notification $notification)
    {
        $this->handle($notification->getSubjectId(), NotificationTypes::EPISODE_KICKOFF);
    }

    /**
     * @param int $subjectId
     * @param int $notificationType
     *
     * @throws \Exception
     */
    public function handle (int $subjectId, int $notificationType)
    {
        $characters = $this->episodeMapper->getParticipantsByEpisode($subjectId);
        $notification = new Notification();
        $notification->setSubjectId($subjectId)
            ->setType($notificationType);
        foreach ($characters as $character) {
            $notification->setUserId($character->getUserid());
            $this->getMapper()->create($notification);
        }
    }

    /**
     * @return NotificationMapper
     */
    protected function getMapper (): NotificationMapper
    {
        if ($this->notificationMapper === null) {
            $this->notificationMapper = new \Notification\Models\Mappers\Types\EpisodeKickoff();
        }
        return $this->notificationMapper;
    }

    /**
     * @param int $id
     *
     * @return NotificationSubject
     * @throws \Exception
     */
    protected function getSubject (int $id): NotificationSubject
    {
        return new EpisodeKickoffSubject($this->episodeMapper->getEpisodeById($id));
    }


}