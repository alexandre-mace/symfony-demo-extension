<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage user contents in the backend.
 *
 * @Route("/admin/user")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * Lists all User entities.
     * Pagination available.
     *
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods="GET", name="admin_user_index")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods="GET", name="admin_user_index_paginated")
     * @param UserRepository $userRepository
     * @param int $page
     * @return Response
     */
    public function index(UserRepository $userRepository, int $page): Response
    {
        $paginator = $userRepository->getAllUsers($page);

        // Loop over ONLY paginated users to get extra data
        $users = [];
        foreach ($paginator->getResults() as $user) {
            $users[] = [
                'entity'        => $user,
                'totalPosts'    => $userRepository->getTotalPostsByUser($user),
                'totalComments' => $userRepository->getTotalCommentsByUser($user)
            ];
        }

        return $this->render('admin/user/index.html.twig', [
            'paginator' => $paginator,
            'users' => $users,
        ]);
    }
}
