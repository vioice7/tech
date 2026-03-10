<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/promote', name: 'app_admin_user_promote', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function promoteUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/demote', name: 'app_admin_user_demote', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function demoteUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setRoles(['ROLE_USER']);
        $em->flush();

        return $this->redirectToRoute('app_admin_users');
    }
}
