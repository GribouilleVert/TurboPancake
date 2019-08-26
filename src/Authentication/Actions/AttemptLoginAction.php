<?php
namespace TurboModule\Authentication\Actions;

use Psr\Http\Message\ServerRequestInterface;
use TurboPancake\Router\RouterAware;
use TurboPancake\AuthentificationInterface;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Router;
use TurboPancake\Services\Neon;
use TurboPancake\Services\Session\SessionInterface;

class AttemptLoginAction {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AuthentificationInterface
     */
    private $authentification;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Neon
     */
    private $flash;

    /**
     * @var string
     */
    private $afterLoginRoute;
    
    /**
     * @var SessionInterface
     */
    private $session;

    use RouterAware;

    public function __construct(
        RendererInterface $renderer,
        AuthentificationInterface $authentification,
        Router $router,
        SessionInterface $session,
        string $afterLoginRoute
    ) {
        $this->authentification = $authentification;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->session = $session;
        $this->flash = new Neon($session);
        $this->afterLoginRoute = $afterLoginRoute;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $fields = $request->getParsedBody();
        if (!empty($fields['username']) AND !empty($fields['password'])) {
            $user = $this->authentification->login($fields['username'], $fields['password']);
            if ($user) {
                $this->flash->success('Bienvenue !');
                $redirect = $this->session->get('auth.redirect');
                if ($redirect !== null) {
                    $this->session->delete('auth.redirect');
                    return $this->directTemporaryRedirect($redirect);
                }
                return $this->temporaryRedirect($this->afterLoginRoute);
            } else {
                $this->flash->error('Nom d\'utilisateur ou mot de passe incorrecte');
            }
        } else {
            $this->flash->warning('Merci de remplir tous les champs');
        }

        $username = $fields['username']??'';
        return $this->renderer->render('@auth/login', compact('username'));
    }

}
