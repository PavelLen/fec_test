<?php

namespace FecBundle\Controller;

use FecBundle\Entity\Costs;
use FecBundle\Entity\Document;
use FecBundle\Entity\Transaction;
use FecBundle\Utils\ParsingDocument;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class PagesController extends Controller
{
    /**
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $document = new Document();

        $form = $this->createFormBuilder($document)
            ->add('file', FileType::class, [
                'label' => 'Загрузить csv документ',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $startDate = $request->request->get('startDate');
            $endDate = $request->request->get('endDate');

            $em->persist($document);
            $em->flush();

            $document->upload();

            $parsing = new ParsingDocument($document->getAbsolutePath());
            $parsing = $parsing->getParsedFileData($startDate, $endDate);

            $em = $this->getDoctrine()->getManager();
            var_dump($parsing); die();
            foreach ($parsing as $category => $groups) {
                foreach ($groups as $group => $entry){
                    foreach ($entry as $transactions => $value){
                        $costs = new Costs();
                        $costs ->setCostsCategory($category);
                        $costs ->setCostsGroup($group);
                        $costs->setCostsEntry($transactions);
                        $em->persist($costs);

                        if(!empty($value)){
                            foreach ($value as $date => $sum){
                                $transaction = new Transaction();
                                $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
                                $transaction->setDate($datetime);
                                $transaction->setSum($sum);

                                $transaction->setCostsEntry($costs);
                                $em->persist($transaction);
                            }
                        }
                        $em->flush();
                    }
                }
            }

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
