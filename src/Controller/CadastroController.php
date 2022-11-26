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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    public static function createLoginResponse($session, $token, $personName, $username, $redirect = null, $clear = false)
    {
        if ($redirect !== null) {
            $response = new RedirectResponse($redirect);
        } else {
            $response = new Response();
        }
        /*if ($clear) {
            $cookieExpires = 0;
        } else {
            $cookieExpires = time() + 86400; // TODO: Definir cookie baseado na data de expiração do token
        }
        $cookieSession = Cookie::create('sl_session')->withValue($session)->withExpires($cookieExpires)->withSecure(false)->withSameSite(null);
        $cookieToken = Cookie::create('sl_session-token')->withValue($token)->withExpires($cookieExpires)->withSecure(false)->withSameSite(null);
        $cookiePerson = Cookie::create('sl_session-person')->withValue($personName)->withExpires($cookieExpires)->withSecure(false)->withSameSite(null);
        $cookieUsername = Cookie::create('sl_session-username')->withValue($username)->withExpires($cookieExpires)->withSecure(false)->withSameSite(null);
        $response->headers->setCookie($cookieSession);
        $response->headers->setCookie($cookieToken);
        $response->headers->setCookie($cookiePerson);
        $response->headers->setCookie($cookieUsername);*/
        return $response;
    }

    /**
     * @Route("/cadastro", name="cadastro", methods={"GET", "POST"})
     */
    public function cadastro(Request $request, ReCaptcha $reCaptcha, ApiService $apiService, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator)
    {
        if (isset($_ENV['SL_PAINEL_CADASTRO_TIPO'])) {
            if ($_ENV['SL_PAINEL_CADASTRO_TIPO'] == 'externo') {
                $url = $_ENV['SL_PAINEL_CADASTRO_URL'];
                return $this->redirect($url);
            }
        }
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

            $response = self::createLoginResponse(\json_encode($registerResponse), $registerResponse['token'], $registerResponse['user']['name'], $registerResponse['user']['username'], $this->generateUrl('conta', []));
            return $response;
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
     * @Route("/logout-event", name="logout", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $response = self::createLoginResponse('', '', '', '', $this->generateUrl('home', []), true);
        return $response;
    }

    /**
     * @Route("/conta", name="conta", methods={"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function conta(Request $request)
    {
        if (isset($_ENV['SL_PAINEL_TIPO'])) {
            if ($_ENV['SL_PAINEL_TIPO'] == 'externo') {
                $url = $_ENV['SL_PAINEL_LOGIN_URL'] . '?token=' . $this->getUser()->getExtraFields()['token'];
                $refer = urlencode($this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
                if ($request->get('externalAutologin')) {
                    $url = $url . '&redirectAfterLogin=' . $refer;
                }
                $url = $url . '&refer=' . $refer;
                return $this->redirect($url);
                /*return $this->render('loginExterno.html.twig', [
                    'painelUrl' => $_ENV['SL_PAINEL_URL'],
                    'painelLoginUrl' => $_ENV['SL_PAINEL_LOGIN_URL'],
                ]);*/
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
