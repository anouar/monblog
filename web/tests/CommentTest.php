<?php

namespace App\Tests\Functional;

use App\Entity\Comment;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;
use Ramsey\Uuid\Uuid;

class CommentTest
{
    public function testGetComment()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();

        $factory = CheeseListingFactory::new(['owner' => $user]);
        // CL 1: unpublished
        $factory->create();

        // CL 2: published
        $comment = $factory->published()->create([
            'title' => 'comment title',
            'content' => 'comment text',
            'createdAt' => new \DateTime() ,
        ]);

        // CL 3: published
        $factory->published()->create();

        $client->request('GET', '/api/comments');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
        $this->assertJsonContains(['hydra:member' => [
            0 => [
                '@id' => '/api/cheeses/' . $cheeseListing2->getId(),
                'title' => 'cheese2',
                'description' => 'cheese',
                'price' => 1000,
                'owner' => '/api/users/' . $user->getUuid(),
                'shortDescription' => 'cheese',
                'createdAtAgo' => '1 second ago',
            ]
        ]]);
    }

    public function testGetCommentItem()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();

        $otherUser = UserFactory::new()->create();

        $cheeseListing1 = CheeseListingFactory::new()->create(['owner' => $otherUser]);

        $client->request('GET', '/api/cheeses/'.$cheeseListing1->getId());
        $this->assertResponseStatusCodeSame(404);

        $response = $client->request('GET', '/api/users/'.$otherUser->getUuid());
        $data = $response->toArray();
        $this->assertEmpty($data['cheeseListings']);
    }
}
