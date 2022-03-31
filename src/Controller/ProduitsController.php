<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\FormProduitsType;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitsController extends AbstractController{

    #[Route('/mesProduits', name:'mesProduits', methods: ['GET', 'POST'])]
    public function mesProduits(ProduitsRepository $produitsRepository){
        $produitsActif = $produitsRepository->findBy([
            'active'=>true
        ]);

        $produitsInactif = $produitsRepository->findBy([
            'active'=>false
        ]);

        return $this->render('produits/mesProduits.html.twig', [
            'produitsActif'=> $produitsActif,
            'produitsInactif'=> $produitsInactif
        ]);
    }

    #[Route('/changeActive/{id}', name:'changeActive', methods:['GET', 'POST'])]
    public function changeActive(ProduitsRepository $produitsRepository, $id){
        $produit = $produitsRepository->findOneBy([
            'id'=>$id
        ]);

        if ($produit->getActive() == true){
            $produit->setActive(false);
            $produit->setStock(0);
        }else{
            $produit->setActive(true);
        }

        $produitsRepository->add($produit);

        return $this->redirectToRoute('mesProduits');
    }

    #[Route('/ajouterDesProduits', name:'ajouterDesProduits', methods:['GET', 'POST'])]
    public function ajouterDesProduits(ProduitsRepository $produitsRepository, Request $request){
        $produits = new Produits();

        $formAjouterDesProduits = $this->createForm(FormProduitsType::class, $produits);
        $formAjouterDesProduits->handleRequest($request);

        if ($formAjouterDesProduits->isSubmitted() && $formAjouterDesProduits->isValid()){
            $produits->setActive(true);
            $produitsRepository->add($produits);

            return $this->redirectToRoute('mesProduits');
        }

        return $this->render('produits/ajouterUnProduit.html.twig', [
            'produits'=> $produits,
            'formAjouterDesProduits'=> $formAjouterDesProduits->createView()
        ]);
    }

    #[Route('/supprimerProduit/{id}', name:'supprimerProduit', methods:['GET', 'POST'])]
    public function supprimerProduit(ProduitsRepository $produitsRepository, $id){
        $produits = $produitsRepository->findOneBy([
            'id'=> $id
        ]);

        if ($produits->getActive() == false){
            $produitsRepository->remove($produits);
        }

        return $this->redirectToRoute('mesProduits');
    }

    #[Route('/modifierProduit/{id}', name:'modifierProduit', methods:['GET', 'POST'])]
    public function modifierProduit(ProduitsRepository $produitsRepository, $id, Request $request){
        $produits = $produitsRepository->findOneBy([
            'id'=>$id
        ]);

        $formModifierDesProduits = $this->createForm(FormProduitsType::class, $produits);

        $formModifierDesProduits->handleRequest($request);

        if ($formModifierDesProduits->isSubmitted() && $formModifierDesProduits->isValid()){
            $produitsRepository->add($produits);

            return $this->redirectToRoute('mesProduits');
        }

        return $this->render('produits/modifierUnProduit.html.twig', [
            'produits'=>$produits,
            'formModifierDesProduits'=>$formModifierDesProduits->createView()
        ]);
    }

    #[Route('/acheterUnProduit/{id}', name:'acheterUnProduit', methods:['GET', 'POST'])]
    public function acheterUnProduit(ProduitsRepository $produitsRepository, $id){
        $produit = $produitsRepository->findOneBy([
            'id'=>$id
        ]);

        $nbStock = $produit->getStock();

        $nbStock --;

        $produit->setStock($nbStock);

        if ($nbStock < 1){
            $produit->setActive(false);
        }

        $produitsRepository->add($produit);

        return $this->redirectToRoute('mesProduits');
    }
}
