<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\FormUsersModifType;
use App\Form\FormUsersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController{
    #[Route('/ajouterUnUser', name:'ajouterUnUser', methods:['GET', 'POST'])]
    public function ajouterUnUser(Request $request, UsersRepository $usersRepository){
        $users = new Users();

        $formCreatUser = $this->createForm(FormUsersType::class, $users);
        $formCreatUser->handleRequest($request);

        if ($formCreatUser->isSubmitted() && $formCreatUser->isValid()){
            $users->setActive(true);
            $users->setAdmin(false);
            $usersRepository->add($users);

            return $this->redirectToRoute('mesUsers');
        }

        return $this->render('users/creerUnUser.html.twig', [
            'formCreatUser'=> $formCreatUser->createView(),
            'user'=> $users
        ]);
    }

    #[Route('/mesUsers', name:'mesUsers', methods:['GET', 'POST'])]
    public function mesUsers(UsersRepository $usersRepository){
        $usersActif = $usersRepository->findBy([
            'active'=>true
        ]);

        $usersInactif = $usersRepository->findBy([
            'active'=>false
        ]);

        return $this->render('users/mesUsers.html.twig', [
            'usersInactif'=>$usersInactif,
            'usersActif'=>$usersActif
        ]);
    }

    #[Route('/modifierActif/{id}', name:'modifierActif', methods:['GET', 'POST'])]
    public function modifierActif($id, UsersRepository $usersRepository){
        $users = $usersRepository->findOneBy([
            'id'=>$id
        ]);

        if ($users->getActive() == true){
            $users->setActive(false);
        }else{
            $users->setActive(true);
        }

        $usersRepository->add($users);

        return $this->redirectToRoute('mesUsers');
    }

    #[Route('/mettreAdmin/{id}', name:'mettreAdmin', methods:['GET', 'POST'])]
    public function mettreAdmin($id, UsersRepository $usersRepository){
        $users = $usersRepository->findOneBy([
            'id'=>$id
        ]);

        if ($users->getAdmin() == true){
            $users->setAdmin(false);
        }else{
            $users->setAdmin(true);
        }

        $usersRepository->add($users);

        return $this->redirectToRoute('mesUsers');
    }

    #[Route('/modifierUser/{id}', name:'modifierUser', methods:['GET', 'POST'])]
    public function modifierUser($id, UsersRepository $usersRepository, Request $request){
        $users = $usersRepository->findOneBy([
            'id'=>$id
        ]);

        $formModifUser = $this->createForm(FormUsersModifType::class, $users);
        $formModifUser->handleRequest($request);

        if ($formModifUser->isSubmitted() && $formModifUser->isValid()){
            $usersRepository->add($users);

            return $this->redirectToRoute('mesUsers');
        }

        return $this->render('users/modifierUsers.html.twig', [
            'formModifierUser'=>$formModifUser->createView(),
            'users'=>$users
        ]);
    }

    #[Route('/supprimerUsers/{id}', name:'supprimerUsers', methods:['GET', 'POST'])]
    public function supprimerUsers($id, UsersRepository $usersRepository){
        $users = $usersRepository->findOneBy([
            'id'=>$id
        ]);

        $usersRepository->remove($users);

        return $this->redirectToRoute('mesUsers');
    }
}