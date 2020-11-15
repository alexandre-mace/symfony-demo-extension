<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UserRepository extends ServiceEntityRepository
{
    public const MAX_USERS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUsers(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('u');

        return (new Paginator($qb, self::MAX_USERS_PER_PAGE))->paginate($page);
    }

    public function getTotalPostsByUser(User $user): int
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('count(p.author)')
            ->from(Post::class,'p')
            ->where('p.author = :user')
            ->setParameter('user', $user);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    public function getTotalCommentsByUser(User $user): int
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('count(c.author)')
            ->from(Comment::class,'c')
            ->where('c.author = :user')
            ->setParameter('user', $user);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }
}
