<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentTest extends KernelTestCase
{
    private const LENGTH_TITLE_CONSTRAINT_MESSAGE = 'le nombre de caractère du titre dépasse 100 caractères.';
    private const CONTENT_501_CHARACTER = 'ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspice';
    private const LENGTH_CONTENT_CONSTRAINT_MESSAGE = 'le nombre de caractère du Content dépasse 500 caractères.';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }
    public function getEntity(): Comment
    {
        $comment = new Comment();

        $comment->setContent('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, 
                totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.')
               ->setCreatedAt(new \DateTime());

        return $comment;
    }

    public function assertHasErrors(Comment $comment, int $number = 0)
    {
        self::bootKernel();
        $errors = $this->validator->validate($comment);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }


    /**
     * Test if Comment is a valid entity
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * test if not valid Description
     */
    public function testNotValidDescription()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
        //more than 500 character
        $comment = $this->getEntity();
        $this->assertHasErrors($comment->setContent(self::CONTENT_501_CHARACTER), 1);
        $errors = $this->validator->validate($comment);
        $this->assertEquals(self::LENGTH_CONTENT_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }
}
