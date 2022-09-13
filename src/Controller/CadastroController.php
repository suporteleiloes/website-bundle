<?php

namespace SL\WebsiteBundle\Controller;

use App\Entity\AlterarSenhaModel;
use App\Entity\PreCadastro;
use App\Entity\RecuperarSenhaModel;
use App\Form\AlterarSenhaType;
use App\Form\PreCadastroType;
use App\Form\RecuperarSenhaType;
use ReCaptcha\ReCaptcha;
use SL\WebsiteBundle\Services\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

/**
 * CadastroController.
 *
 */
class CadastroController extends AbstractController
{
    /**
     * @Route("/cadastro", name="cadastro", methods={"GET", "POST"})
     */
    public function cadastro(Request $request, ReCaptcha $reCaptcha, ApiService $apiService, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator)
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
                $registerResponse = $apiService->cadastro(
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
            $username = $registerResponse['user']['username'];
            $userData = $apiService->requestToken($username, $cadastro->getPassword());
            $user = new InMemoryUser($username, null, $userData['user']['roles'], true, true, true, true, $userData);
            $userAuthenticator->authenticateUser(
                $user,
                $formLoginAuthenticator,
                $request
            );

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
        if (isset($_ENV['SL_PAINEL_EXTERNO'])) {
            if ($_ENV['SL_PAINEL_EXTERNO'] == '1') {
                return $this->redirect($_ENV['SL_PAINEL_URL']);
            }
        }
        return $this->render('conta.html.twig', array());
    }

    /**
     * @Route("/recuperar-senha", name="recuperar-senha", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function recuperarSenha(Request $request, ReCaptcha $reCaptcha, ApiService $apiService)
    {
        if ($request->get('id') && $request->get('token')) {
            $model = new AlterarSenhaModel();
            $form = $this->createForm(AlterarSenhaType::class, $model);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $response = $apiService->recuperarSenhaSalvar(
                        $request->get('id'),
                        $request->get('token'),
                        $model->getSenha()
                    );

                } catch (\Throwable $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                    return $this->renderForm('recuperar-senha-alterar.html.twig', array(
                        'form' => $form
                    ));
                }
                return $this->renderForm('recuperar-senha-alterar.html.twig', array(
                    'form' => $form,
                    'success' => true
                ));
            }
            return $this->renderForm('recuperar-senha-alterar.html.twig', array(
                'form' => $form,
                'id' => $request->get('id'),
                'token' => $request->get('token')
            ));
        }

        $model = new RecuperarSenhaModel();
        $form = $this->createForm(RecuperarSenhaType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $botCheck = $reCaptcha->verify($request->request->get('g-recaptcha-response'));
            $botCheckTest = $botCheck->isSuccess() && $botCheck->getScore() >= 0.5;
            if (!$botCheckTest) {
                $form->addError(new FormError('ReCaptcha inválido'));
                return $this->renderForm('recuperar-senha.html.twig', array(
                    'form' => $form
                ));
            }

            try {
                $response = $apiService->recuperarSenha(
                    $model->getUsername()
                );

                if (isset($response['email'])) {
                    return $this->renderForm('recuperar-senha.html.twig', array(
                        'sended' => $response['email']
                    ));
                }

            } catch (\Throwable $exception) {
                $form->addError(new FormError($exception->getMessage()));
                return $this->renderForm('recuperar-senha.html.twig', array(
                    'form' => $form
                ));
            }

            return $this->redirectToRoute('conta');
        }

        return $this->renderForm('recuperar-senha.html.twig', array(
            'form' => $form,
        ));
    }
}
