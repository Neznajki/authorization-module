<?php declare(strict_types=1);


namespace App\Service;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordEncryptService implements UserPasswordEncoderInterface
{
    /** @var string */
    protected $salt;

    /**
     * PasswordEncryptService constructor.
     * @param string $salt
     */
    public function __construct(string $salt = 'just random salt')
    {
        $this->salt = $salt;
    }

    /**
     * Encodes the plain password.
     *
     * @param UserInterface $user The user
     * @param string $plainPassword The password to encode
     *
     * @return string The encoded password
     */
    public function encodePassword(UserInterface $user, $plainPassword)
    {
        return hash('sha256', $plainPassword . $this->salt);
    }

    /**
     * @param UserInterface $user The user
     * @param string $raw A raw password
     *
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid(UserInterface $user, $raw)
    {
        return $this->encodePassword($user, $raw) === $user->getPassword();
    }
}
