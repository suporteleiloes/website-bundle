<?php

namespace SL\WebsiteBundle\Controller;

use App\Entity\PreCadastro;
use App\Form\PreCadastroType;
use ReCaptcha\ReCaptcha;
use SL\WebsiteBundle\Services\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
     * @Route("/cadastro", name="cadastro", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function cadastro(Request $request, ReCaptcha $reCaptcha, ApiService $apiService)
    {
        $cadastroTemplate = 'cadastro.html.twig';
        $cadastro = new PreCadastro();
        $form = $this->createForm(PreCadastroType::class, $cadastro);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $botCheck = $reCaptcha->verify($request->request->get('g-recaptcha-response'));
            $botCheckTest = $botCheck->isSuccess() && $botCheck->getScore() >= 0.5;
            if (!$botCheckTest) {
                $form->addError(new FormError('ReCaptcha inválido'));
                return $this->renderForm($cadastroTemplate, array(
                    'form' => $form
                ));
            }

            try {
                $userData = $apiService->cadastro(
                    $cadastro->getNome(),
                    $cadastro->getEmail(),
                    $cadastro->getCelular(),
                    $cadastro->getPassword()
                );
            } catch (\Throwable $exception) {
                $form->addError(new FormError($exception->getMessage()));
                return $this->renderForm($cadastroTemplate, array(
                    'form' => $form
                ));
            }

            // Manual authenticate

            return $this->redirectToRoute('conta');
        }

        return $this->renderForm($cadastroTemplate, array(
            'form' => $form
        ));
    }

    /**
     * !Route("/login", name="login", methods={"GET", "POST"})
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

        return $this->render('conta.html.twig', array());
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
