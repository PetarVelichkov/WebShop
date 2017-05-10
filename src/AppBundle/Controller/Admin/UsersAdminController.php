<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\UserEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersAdminController extends Controller
{
    /**
     * @Route("/users", name="admin_list_users")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsersAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $pager  = $this->get('knp_paginator');
        /** @var User[] $users */
        $users = $pager->paginate(
                $repository
                ->createQueryBuilder('users')
                ->select('users')
                ->orderBy('users.id', 'asc')
                ->getQuery()
                ->getResult(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("webshop/Admin/users_list.html.twig", [
            "users" => $users
        ]);
    }


    /**
     * @Route("/users/delete/{id}", name="admin_delete_user")
     * 
     *
     * @param $id
     * @return RedirectResponse
     */
    public function deleteUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash("success", "User deleted successfully!");

        return $this->redirectToRoute("admin_list_users");
    }


    /**
     * @Route("/users/edit/{id}", name="admin_edit_user")
     * @param $id
     * @param Request $request
     * @return Response
     * @internal param User $user
     */
    public function editUserAction($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "User {$user->getEmail()} updated successfully!");

            return $this->redirectToRoute("admin_list_users");
        }

        return $this->render("webshop/Admin/users_edit.html.twig", [
            "edit_form" => $form->createView()
        ]);
    }
}
