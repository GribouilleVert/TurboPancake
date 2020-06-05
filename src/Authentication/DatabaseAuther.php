<?php
namespace TurboModule\Authentication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TurboModule\Authentication\Database\Tables\UsersTable;
use TurboPancake\Auth\Exceptions\NotLoggedException;
use TurboPancake\Auth\UserInterface;
use TurboPancake\AuthenticationInterface;
use TurboPancake\Database\Exceptions\NoRecordException;
use TurboPancake\Database\Exceptions\QueryBuilderException;
use TurboPancake\Services\Session\SessionInterface;

class DatabaseAuther implements AuthenticationInterface {

    /**
     * @var UsersTable
     */
    private $usersTable;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var User
     */
    private $userCache;

    public function __construct(UsersTable $usersTable, SessionInterface $session)
    {
        $this->usersTable = $usersTable;
        $this->session = $session;
    }

    /**
     * Renvoie l'utilisateur actif, si l'utilisateur n'est pas connecté renvoie
     * une NotLoggedExcpetion
     *
     * @param ServerRequestInterface|null $request If null use global vars instead
     * @return UserInterface
     * @throws NotLoggedException
     * @throws QueryBuilderException
     */
    public function getUser(?ServerRequestInterface $request = null): UserInterface
    {
        if (!$this->isLogged()) {
            throw new NotLoggedException();
        }

        if (is_null($this->userCache)) {
            $clone = clone $this->usersTable;
            $clone->setThrowOnNotFound(true);
            try {
                $this->userCache = $clone->find($this->session->get('auth.user'));
            } catch (NoRecordException $e) {
                $this->logout();
                throw new NotLoggedException();
            }
        }

        return $this->userCache;
    }

    /**
     * Indique si l'utilisateur est connecté
     * @param ServerRequestInterface|null $request
     * @return bool
     */
    public function isLogged(?ServerRequestInterface $request = null): bool
    {
        return $this->session->get('auth.user') !== null;
    }

    public function login(string $identifier, ResponseInterface &$response, array $options = []): ?UserInterface
    {
        $user = $this->usersTable->findByIdentifier($identifier);
        if ($user) {
            $this->session->set('auth.user', $user->id);
            return $user;
        }

        return null;
    }

    /**
     * Permet de déconnecter la sessions
     */
    public function logout(ResponseInterface &$response): void
    {
        $this->session->delete('auth.user');
    }
}
