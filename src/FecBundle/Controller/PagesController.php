<?php

namespace FecBundle\Controller;

use FecBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PagesController extends Controller
{
    /**
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $document->upload();

            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('fec_upload');
        }

        return $this->render('@Fec/Pages/upload.html.twig', [
            'form' => $form->createView(),
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
}
