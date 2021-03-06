<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 12.02.2018
 * Time: 1:51
 */

namespace User\Action;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Service\AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template\TemplateRendererInterface;

class Login implements ServerMiddlewareInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var AuthAdapter
     */
    private $authAdapter;

    /**
     * LoginAction constructor.
     * @param TemplateRendererInterface $templateRenderer
     * @param AuthenticationService $authService
     * @param AuthAdapter $authAdapter
     */
    public function __construct(TemplateRendererInterface $templateRenderer, AuthenticationService $authService, AuthAdapter $authAdapter)
    {
        $this->templateRenderer = $templateRenderer;
        $this->authService = $authService;
        $this->authAdapter = $authAdapter;
    }


    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        if ($request->getMethod() === 'POST') {
            return $this->authenticate($request);
        }

        return new HtmlResponse($this->templateRenderer->render('user::login'));
    }

    public function authenticate(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $refererUri = $request->getHeaderLine('referer');

        if (empty($params['email'])) {
            return new HtmlResponse($this->templateRenderer->render('user::login', [
                'error' => 'The username cannot be empty',
            ]));
        }

        if (empty($params['password'])) {
            return new HtmlResponse($this->templateRenderer->render('user::login', [
                'username' => $params['email'],
                'error'    => 'The password cannot be empty',
            ]));
        }

        $this->authAdapter->setEmail($params['email']);
        $this->authAdapter->setPassword($params['password']);

        $result = $this->authService->authenticate();
        if (!$result->isValid()) {
            return new HtmlResponse($this->templateRenderer->render('user::login', [
                'username' => $params['email'],
                'error'    => 'The credentials provided are not valid',
            ]));
        }

        return new RedirectResponse($refererUri);
        //return new RedirectResponse('/admin');
    }
}