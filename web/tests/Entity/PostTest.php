<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostTest extends KernelTestCase
{
    private const TITLE_101_CHARCTER = 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie';
    private const LENGTH_TITLE_CONSTRAINT_MESSAGE = 'le nombre de caractère du titre dépasse 100 caractères.';
    private const CONTENT_1501_CHARACTER = 'ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspice ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspice ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspiciatie
ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,Sed ut perspi';
    private const LENGTH_CONTENT_CONSTRAINT_MESSAGE = 'le nombre de caractère du Content dépasse 1500 caractères.';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }
    public function getEntity(): Post
    {
        $post = new Post();

        $post->setTitle('Sed ut perspiciatis unde omnis iste natus error sit voluptatem')
                ->setContent('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, 
                totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.')
                ->setPublished(true)
                ->setPublishedAt(new \DateTime())
               ->setCreatedAt(new \DateTime());

        return $post;
    }

    public function assertHasErrors(Post $post, int $number = 0)
    {
        self::bootKernel();
        $errors = $this->validator->validate($post);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }


    /**
     * Test if Post is a valid entity
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * test if not valid Title
     */
    public function testNotValidTitle()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1);
        //more than 100 character
        $post = $this->getEntity();
        $this->assertHasErrors($post->setTitle(self::TITLE_101_CHARCTER), 1);
        $errors = $this->validator->validate($post);
        $this->assertEquals(self::LENGTH_TITLE_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

    /**
     * test if not valid Description
     */
    public function testNotValidDescription()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
        //more than 1500 character
        $post = $this->getEntity();
        $this->assertHasErrors($post->setContent(self::CONTENT_1501_CHARACTER), 1);
        $errors = $this->validator->validate($post);
        $this->assertEquals(self::LENGTH_CONTENT_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }


    /**
     * test if empty updatedAt
     */
    public function testEmptyUpdatedAt()
    {
        $this->assertHasErrors($this->getEntity()->setUpdatedAt(null), 0);
    }

    /**
     * test if empty publishedAt
     */
    public function testEmptyPublishedAt()
    {
        $this->assertHasErrors($this->getEntity()->setPublishedAt(null), 0);
    }
}
