<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private const EMAIL_CONSTRAINT_MESSAGE = 'L\'Email "test" n\'est pas valide.';
    private const NOT_BLANK_CONSTRAINT_MESSAGE = 'Veuillez saisir une valeur.';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }
    public function getEntity(): User
    {
        $user = new User();

        $user
            ->setEmail('test@test.com')
            ->setRoles(['ROLE_USER'])
            ->setFirstname('Firsname')
            ->setLastname('LastName')
            ->setUsername('username')
            ->setAvatar('http://https://via.placeholder.com/150');
        return $user;
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = $this->validator->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }


    /**
     * Test if User is a valid entity
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * test if email is not valid
     */
    public function testNotValidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('test'), 1);
    }

    /**
     * test message invalid email
     */
    public function testInValidEmailMessage()
    {
        $user = $this->getEntity();
        $user->setEmail('test');
        $errors = $this->validator->validate($user);

        $this->assertEquals(self::EMAIL_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

    /**
     * test if empty email
     */
    public function testEmptyEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    /**
     * test if empty Avatar
     */
    public function testEmptyAvatar()
    {
        $this->assertHasErrors($this->getEntity()->setAvatar(''), 1);
    }

    /**
     * test if empty Avatar
     */
    public function testEmptyAvatarMessage()
    {
        $user = $this->getEntity();
        $user->setAvatar('');
        $errors = $this->validator->validate($user);

        $this->assertEquals(self::NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }


    /**
     * test if empty password
     */
    public function testEmptyPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword(''), 0);
    }

    /**
     * test if empty firstname
     */
    public function testEmptyFirstname()
    {
        $this->assertHasErrors($this->getEntity()->setFirstname(''), 1);
    }

    /**
     * test if empty lastname
     */
    public function testEmptyLastname()
    {
        $this->assertHasErrors($this->getEntity()->setLastname(''), 1);
    }

    /**
     * test if empty username
     */
    public function testEmptyUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }
}
