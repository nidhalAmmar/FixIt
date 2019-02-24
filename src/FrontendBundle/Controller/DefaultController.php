<?php

namespace FrontendBundle\Controller;

use AdminUserBundle\Entity\User;
use ClientBundle\Entity\Demande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ServiceBundle\Entity\Service;
use OffreBundle\Form\OffreType;
use OffreBundle\Entity\Offre;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrontendBundle:Default:index.html.twig');
    }
    public function HomeAction()
    {
        $demandes = $this->getDoctrine()->getRepository(Demande::class)->findAll();
        return $this->render('@Frontend/Default/Home.html.twig', array('demandes'=>$demandes));
    }
    public function ContacterAction(Request $request , $id)
    {

        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form = $form->handleRequest($request);
        $offre->setUser($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $demande = $em->getRepository(Demande::class)->find($id);
         $client = $em->getRepository(User::class)->find($demande->getIdClient());
        $service = $em->getRepository(Service::class)->find($demande->getCategorie());
        if ($form->isSubmitted()){

            $offre->setDateAjout( new \DateTime('now'));
            $offre->setClient($client);
            $em->persist($offre);
            $em->flush();

            return $this->redirectToRoute("fixit_homepage");

        }
        return $this->render('@Frontend/Default/AddOffreClient.html.twig', array(
            'form'   => $form->createView(), 'client'=>$client , 'service' =>$service
        ));

    }

    public function servicesAction()
    {
        $user = $this->getUser();
        $user->getId();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('OffreBundle:Offre')->createQueryBuilder('d')->where('d.User =:id')
            ->setParameter('id',$user)->getQuery();
        $offres = $qb->getResult();
        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
        return $this->render('@Frontend/Default/MesOffres.html.twig', array('services'=>$services ,'offres' =>$offres));
    }

    public function aboutusAction()
    {
        return $this->render('@Frontend/Default/aboutus.html.twig');
    }

    public function contactusAction()
    {
        return $this->render('@Frontend/Default/contactus.html.twig');
    }

    public function galleryAction()
    {
        return $this->render('@Frontend/Default/gallery.html.twig');
    }

    public function AddOffreAction(Request $request)
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted()){
            $offre->setDateAjout( new \DateTime('now'));
            $offre->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();

            return $this->redirectToRoute("fixit_MesOffres");

        }
        return $this->render('@Frontend/Default/AddOffre.html.twig', array(
            'form'   => $form->createView(),
        ));
       // return $this->render('@Frontend/Default/AddOffre.html.twig');
    }

    public function AddServiceAction()
    {
        return $this->render('@Frontend/Default/AddService.html.twig');
    }

    public function SuppOffreAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele= $em->getRepository(Offre::class)->find($id);

        $em->remove($modele);
        $em->flush();

        return $this->redirectToRoute("fixit_MesOffres");
    }

    public function ModifierOffreAction($id ,  Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $offre = $em->getRepository(Offre::class)->find($id);

        $form = $this->createForm(OffreType::class, $offre);
        $form->setData($offre);
        $form = $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($offre);
            $em->flush();
            return $this->redirectToRoute("fixit_MesOffres");
        }
        return $this->render('@Frontend/Default/ModifierOffre.html.twig', array('form' => $form->createView()));
    }

}
