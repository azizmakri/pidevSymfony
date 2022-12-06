<?php

namespace App\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\ClientComType;
use App\Form\PublicationType;
use App\Repository\CommentaireRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse ;

class PublicationController extends AbstractController


{
    /**
     * @Route("/", name="display_pub")
     */
    public function index(CommentaireRepository $repo): Response
    {
        $pubs = $this->getDoctrine()->getManager()->getRepository(Publication::class)->findAll();

        $fullpubication = [];

        foreach ($pubs as $key ) {

            $fullpubication[] = [
                'pub' => $key,
                'commentaire' => $repo->findOneBySomeField($key->getIdpub())

            ];
        }
        


        return $this->render('publication/index.html.twig', [
                'p'=>$fullpubication
        ]);
    }
    /**
     * @param $id
     * @param PublicationRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeevent/{id}", name="likeevent")
     */
    public function likeEvent(PublicationRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaime() + 1;
        $event->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('display_pub_front');
    }
    /**
     * @Route("/stat/{id}", name="stat")
     */
    public function statAction($id): Response
    {
        $pieChart = new PieChart();

        $entityManager = $this->getDoctrine()->getManager();
        $objet = $entityManager->getRepository(Publication::class)->find($id);
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable( array(
            ['publication', 'Nombre de jaime'],
            ['Jaime',     $objet->getJaime() ],
            ['Jaime pas',      $objet->getJaimepas() ],
        ));

        $pieChart->getOptions()->setTitle('Stat Jaime par Publication');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);


        return $this->render('publication/statrec.html.twig', array(
                'piechart' => $pieChart,
            )

        );

    }
    /**
     * @param $id
     * @param PublicationRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeevent/{id}", name="dislikeevent")
     */
    public function dislikeEvent(PublicationRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaimepas() + 1;
        $event->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('display_pub_front');
    }
    /**
     * @Route("/listepub", name="display_pub_front")
     */
    public function afficherPubs(): Response
    {
        $pubs = $this->getDoctrine()->getManager()->getRepository(Publication::class)->findAll();
        return $this->render('publication/frontpub.html.twig', [
            'p'=>$pubs
        ]);
    }
    /**
     * @Route("/removePub/{id}", name="supp_pub")
     */
    public function deletePub(Publication $pub): Response
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($pub);
        $em->flush();
        return $this->redirectToRoute('display_pub');
    }

    /**
     * @Route("/addPub", name="addPub")
     */
    public function addPub(Request $request)
    {
        $pub= new Publication();

        $form = $this-> createForm(PublicationType::class,$pub);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em-> persist($pub); //add
            $em->flush();

            return $this->redirectToRoute('display_pub');
        }
        return $this->render('publication/createPub.html.twig',['f'=>$form->createView()]);

    }

    /**
     * @Route("/addPubFront", name="addPubFront")
     */
    public function addPubFront(Request $request)
    {
        $pub= new Publication();

        $form = $this-> createForm(PublicationType::class,$pub);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em-> persist($pub); //add
            $em->flush();

            return $this->redirectToRoute('display_pub_front');
        }
        return $this->render('publication/createPubFront.html.twig',['f'=>$form->createView()]);

    }

    /**
     * @Route("pubItem/{id}",name="pub_item")
     */
    public function showPubItem($id,CommentaireRepository $repo,Request $request,ManagerRegistry $managerRegistry)
    {

        $pubs = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($id);

        $comment = new Commentaire();
        $form = $this->createForm(ClientComType::class,$comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setIdpub($id);
            $manager = $managerRegistry->getManager();
            $manager->persist($comment);
            $manager->flush();

        }


       return $this->render("publication/pubItem.html.twig",[
            'pub'=> $pubs,
           'commentaire' => $repo->findOneBySomeField($pubs->getIdpub()),
           'form' =>$form->createView()
       ]);
    }

    /**
     * @Route("/modifPub/{id}", name="modifPub")
     */
    public function modifPub(Request $request,$id): Response
    {
        $pub= $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($id) ;

        $form = $this-> createForm(PublicationType::class,$pub);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('display_pub');
        }
        return $this->render('publication/updatePub.html.twig',['f'=>$form->createView()]);

    }

    /**
     * @Route("/pub/pdf",name="pub_pdf")
     */
    public function pdf(PublicationRepository $repProd)
    {
        $publications = $repProd->findAll();
        $pdfOptions = new Options;
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);


        $html = $this->render("default/mypdf.html.twig",[
            'pubs'=>$publications
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);

    }

    /**
     * @Route("/publication/displayJSON", name="app_publication_displayJSON", methods={"GET"})
     */
    public function afficherPubJson(EntityManagerInterface $entityManager,PublicationRepository $repo,NormalizerInterface $normalizer)
    {
        $publications = $repo->findAll();
        $json=$normalizer->normalize($publications);
        return new Response(json_encode($json));
    }

    /**
     * @Route("/deleteJSON/{idpub}", name="app_publication_deletePubJSON")
     */
    public function deleteJSON(NormalizerInterface $normalizer, $idpub){

        $em = $this->getDoctrine()->getManager();
        $pub = $em->getRepository(Publication::class)->find($idpub);
        $em->remove($pub);
        $em->flush();
        $data=$normalizer->normalize($pub,'json',['groups'=>'post:read']);

        return new Response("Publication deleted successfully".json_encode($data));
    }

    /**
     * @Route("/addJSON/new", name="app_publication_addPubJSON")
     */
    public function addPubJSON(Request $request,NormalizerInterface $normalizer){
        //$content=$request->getContent();
        $em = $this->getDoctrine()->getManager();
        $Pub= new Publication();

        // $datePb=strtotime($request->get('datePub'));
        // $my_date = date('Y-m-d H:i:s', $datePb);

        $Pub->setIduser($request->get('iduser'));
        $Pub->setTopic($request->get('topic'));
        $Pub->setTexte($request->get('texte'));

        $em->persist($Pub);
        $em->flush();
        $data=$normalizer->normalize($Pub,'json',['groups'=>'post:read']);

        return new Response("Publication added successfully".json_encode($data));
    }

    /**
     * @Route("/updateJSON/{idpub}", name="app_publication_updatePubJSON")
     */
    public function updatePubJSON(Request $request,NormalizerInterface $normalizer, $idpub){
        $em = $this->getDoctrine()->getManager();
        $Pub = $em->getRepository(Publication::class)->find($idpub);

        $Pub->setIduser($request->get('iduser'));
        $Pub->setTopic($request->get('topic'));
        $Pub->setTexte($request->get('texte'));

        $em->persist($Pub);
        $em->flush();
        $data=$normalizer->normalize($Pub,'json',['groups'=>'post:read']);

        return new Response(json_encode($data));
    }
}
