<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OauthController extends AbstractController
{
    /**
     * @Route("/oauth", name="oauth")
     */
    public function index(): Response
    {
        return $this->render("oauth/index.html.twig", [
            "controller_name" => "OauthController",
        ]);
    }
}
