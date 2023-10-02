<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PresentationRecetteControllerTest extends WebTestCase
{
    public function testLogOut()
    {
        $client = static::createClient();
        
        $recipeId = 1;
        
        $crawler = $client->request('GET', '/presentation/recette/'.$recipeId);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert.alert-info>a', 'Inscrivez-vous maintenant !');

    }

    public function testLogIn()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('john.doe@example.com');

        $client->loginUser($testUser);
        
        $recipeId = 1;
        
        $crawler = $client->request('GET', '/presentation/recette/'.$recipeId);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.comment-form h3', 'Laissez un commentaire');

    }

}

