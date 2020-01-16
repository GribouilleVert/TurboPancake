<?php
namespace TurboModule\Authentication;

use TurboModule\Authentication\Database\Tables\UsersTable;
use TurboPancake\Auth\Identity\IdentityCheckerInterface;
use TurboPancake\Auth\UserInterface;

class DatabaseIdentityChecker implements IdentityCheckerInterface {

    /**
     * @var UserInterface
     */
    protected $user = null;

    /**
     * @var string
     */
    protected $password = null;

    /**
     * @var UsersTable
     */
    private $usersTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    /**
     * @inheritDoc
     */
    public function withUser(UserInterface $user): IdentityCheckerInterface
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withIdentifier(string $identifier): IdentityCheckerInterface
    {
        $this->user = $this->usersTable->findByIdentifier($identifier);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withPassword(string $rawPassword): IdentityCheckerInterface
    {
        $this->password = $rawPassword;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function check(): bool
    {
        if (!$this->user OR !$this->password) {
            return false;
        }

        if (password_verify($this->password, $this->user->password)) {
            return true;
        }
        return false;
    }

}