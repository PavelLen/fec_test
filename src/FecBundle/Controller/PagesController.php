<?php

namespace FecBundle\Controller;

use FecBundle\Entity\Document;
use FecBundle\Form\CostsType;
use FecBundle\Form\TransactionsType;
use FecBundle\Form\UploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PagesController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(TransactionsType::class);
        $form->handleRequest($request);

        return $this->render('@Fec/Pages/index.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }

    public function tableAction()
    {
        return $this->render('@Fec/Pages/table.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('@Fec/Pages/about.html.twig');
    }

    /**
     * @Template()
     */
    public function uploadAction()
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->getForm()
        ;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl());
        }

        return array('form' => $form->createView());
    }
}
