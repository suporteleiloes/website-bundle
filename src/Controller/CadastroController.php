<?php

namespace SL\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * CadastroController.
 *
 */
class CadastroController extends AbstractController
{
    /**
     * @Route("/cadastro", name="cadastro", methods={"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function cadastro(Request $request)
    {

        $tipo = $_SERVER['TIPO_CADASTRO'];

        if ($tipo === 'EXTERNO') {
            return $this->render('paginas/cadastro.externo.html.twig', array(
                'tipo' => $tipo
            ));
        }
        return $this->render('paginas/cadastro.html.twig', array(
            'tipo' => $tipo
        ));
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {

        return $this->render('@SLWebsite/login/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/conta", name="conta", methods={"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function conta(Request $request)
    {

        $tipo = $_SERVER['TIPO_LOGIN'];

        if ($tipo === 'EXTERNO') {
            return $this->render('paginas/login.externo.html.twig', array(
                'tipo' => $tipo
            ));
        }
        return $this->render('paginas/login.html.twig', array(
            'tipo' => $tipo
        ));
    }

    /**
     * @Route("/recuperar-senha", name="recuperar-senha", methods={"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function recuperarSenha(Request $request)
    {

        $tipo = $_SERVER['TIPO_CADASTRO'];

        if ($tipo === 'EXTERNO') {
            return $this->render('paginas/login.externo.recuperar-senha.html.twig', array(
                'tipo' => $tipo,
                'id' => intval($request->get('id')),
                'token' => $request->get('token')
            ));
        }
        return $this->render('paginas/cadastro.html.twig', array(
            'tipo' => $tipo
        ));
    }
}
