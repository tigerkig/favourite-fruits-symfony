<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Entity\Nutrition;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpClient\HttpClient;

class FruitController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/allFruits", name="allFruits")
     */
    public function allFruits(Request $request, PaginatorInterface $paginator): Response
    {
        $api_url = $this->getParameter('app.api_url') . 'all';

        if($request->query->get('name'))
            $api_url = $this->getParameter('app.api_url') . $request->query->get('name');
        if($request->query->get('family'))
            $api_url = $this->getParameter('app.api_url') . 'family/' . $request->query->get('family');

        $client = HttpClient::create();
        $response = $client->request( 'GET', $api_url );
        $statusCode = $response->getStatusCode();

        if($statusCode === 200) {
            if($request->query->get('name') != '')
                $content[] = $response->toArray();
            else
                $content = $response->toArray();
        } else {
            $content = [];
        }

        $paginatedFruits = $paginator->paginate($content, $request->query->getInt('page', 1), 5);
        return $this->render('allFruits.html.twig', ['fruits' => $paginatedFruits, 'page' => $request->query->getInt('page', 1)]);
    }

    /**
     * @Route("/create", name="create-fruit", methods={"POST"})
     */
    public function create(Request $request, MailerInterface $mailer) 
    {
        $headers = [
            'Content-Type' => 'text/plain'
        ];

        $body = '{
            "genus": "'.$request->request->get('genus').'",
            "name": "'.$request->request->get('name').'",
            "family": "'.$request->request->get('family').'",
            "order": "'.$request->request->get('order_f').'",
            "nutritions": {
                "carbohydrates": '.$request->request->get('carbohydrates').',
                "protein": '.$request->request->get('protein').',
                "fat": '.$request->request->get('fat').',
                "calories": '.$request->request->get('calories').',
                "sugar": '.$request->request->get('sugar').'
            }
        }';

        $client = HttpClient::create();
        $response = $client->request(
            'PUT',
            $this->getParameter('app.api_url'),
            [
                'headers' => $headers,
                'body' => $body
            ]
        );
        $statusCode = $response->getStatusCode();
        $content = $response->toArray();

        if(isset($content['success']) && $statusCode === 200) {
            // $email = (new Email())
            //     ->from($this->getParameter('app.from_email'))
            //     ->to($this->getParameter('app.to_email'))
            //     ->subject('Created New Fruit!')
            //     ->text('You have created new fruit!')
            //     ->html('<p>You can create more than.</p>');

            // $mailer->send($email);
            $this->addFlash('success', 'You have created a new fruit!');
            return $this->redirectToRoute('allFruits');
        } else {
            $this->addFlash('danger', 'Please enter the vaild data.');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/favorite", name="favorite-fruit")
     */
    public function getFavoriteFruit(Request $request): Response
    {
        $ids = $request->query->get('ids');
        $client = HttpClient::create();
        $allFavoriteArray = array();
        $sum_nutrition = array();
        $carbohydrates = $protein = $fat = $calories = $sugar = 0;

        foreach (json_decode($ids) as $key => $id) {
            $response = $client->request(
                'GET',
                $this->getParameter('app.api_url').$id
            );
            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            if($statusCode === 200) {
                array_push($allFavoriteArray, $content);
                $carbohydrates = $carbohydrates + (float) $content['nutritions']['carbohydrates'];
                $protein = $protein + (float) $content['nutritions']['protein'];
                $fat = $fat + (float) $content['nutritions']['fat'];
                $calories = $calories + (float) $content['nutritions']['calories'];
                $sugar = $sugar + (float) $content['nutritions']['sugar'];
            }
        }
        
        return $this->render('favoriteFruits.html.twig', [
            'favorites' => $allFavoriteArray, 
            'carbohydrates' => $carbohydrates,
            'protein' => $protein,
            'fat' => $fat,
            'calories' => $calories,
            'sugar' => $sugar
        ]);
    }

}
