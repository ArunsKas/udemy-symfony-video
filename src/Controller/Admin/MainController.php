<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class MainController extends AbstractController {

    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        $is_invalid = null;
        if ($form->isSubmitted() && $form->isValid()) {
            exit('valid');
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
            'is_invalid' => $is_invalid,
        ]);
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }
        return $this->render('admin/videos.html.twig', [
            'videos' => $videos
        ]);
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = NULL)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }

    /**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan() {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount() {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());

        $em->remove($user);
        $em->flush();

        session_destroy();

        return $this->redirectToRoute('main_page');
    }

}
