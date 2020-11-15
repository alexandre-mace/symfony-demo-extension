<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class PostSubscriber implements EventSubscriber
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->updateUserTotalPostUpdates($args);
    }

    public function updateUserTotalPostUpdates(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Post) {
            $currentUser = $this->security->getUser();

            // Add 1 to user total post updates because this user has just updated one post
            if ($currentUser instanceof User) {
                $currentUser->setTotalPostUpdates($currentUser->getTotalPostUpdates() + 1);

                $entityManager = $args->getObjectManager();
                $entityManager->flush();
            }
        }
    }
}