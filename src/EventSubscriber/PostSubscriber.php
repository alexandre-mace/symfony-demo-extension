<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class PostSubscriber implements EventSubscriber
{
    private $security;
    private $toBePersisted = [];

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postFlush
        ];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->updateUserTotalPostUpdates($args);
    }

    public function updateUserTotalPostUpdates(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Post) {
            $currentUser = $this->security->getUser();

            // Add 1 to user total post updates because this user has just updated one post
            if ($currentUser instanceof User) {
                $currentUser->setTotalPostUpdates($currentUser->getTotalPostUpdates() + 1);
                $this->toBePersisted[] = $currentUser;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if(!empty($this->toBePersisted)) {
            $entityManager = $event->getEntityManager();
            foreach ($this->toBePersisted as $persistData) {
                $entityManager->persist($persistData);
            }

            $this->toBePersisted = [];
            $entityManager->flush();
        }
    }
}