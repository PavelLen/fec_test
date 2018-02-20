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
            /**
             * Create Document Entity
             */
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            /**
             * Date period
             */
            $startDate = $request->request->get('startDate');
            empty($startDate) ? $startDate = new \DateTime("1970-01-01 00:00:00") : $startDate = new \DateTime($startDate);

            $endDate = $request->request->get('endDate');
            empty($endDate) ? $endDate = new \DateTime() : $endDate = new \DateTime($endDate);

            $document->upload();

            /**
             * Run parsing
             */
            $parsing = new ParsingDocument($document->getAbsolutePath());
            $parsing = $parsing->getParsedFileData($startDate, $endDate);

            /**
             * Add parsing data to DB
             */
            $em = $this->getDoctrine()->getManager();
            foreach ($parsing as $category => $groups) {
                foreach ($groups as $group => $entry){
                    foreach ($entry as $transactions => $value){

                        $costs = $em->getRepository(Costs::class)->createQueryBuilder('c')
                            ->where('c.costsCategory LIKE :category')
                            ->andWhere('c.costsGroup LIKE :group')
                            ->andWhere('c.costsEntry LIKE :entry')
                            ->setParameter('category', '%'.$category.'%')
                            ->setParameter('group', '%'.$group.'%')
                            ->setParameter('entry', '%'.$transactions.'%')
                            ->getQuery()
                            ->getOneOrNullResult();

                        if(empty($costs)){
                            $costs = new Costs();
                        }

                        $costs ->setCostsCategory($category);
                        $costs ->setCostsGroup($group);
                        $costs->setCostsEntry($transactions);
                        $em->persist($costs);

                        if(!empty($value)){
                            foreach ($value as $date => $sum){
                                $transaction = $em->getRepository(Transaction::class)->createQueryBuilder('t')
                                    ->where('t.date LIKE :date')
                                    ->setParameter('date', '%'.$date.'%')
                                    ->getQuery()
                                    ->getOneOrNullResult();

                                if (empty($transaction)){
                                    $transaction = new Transaction();
                                }
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
