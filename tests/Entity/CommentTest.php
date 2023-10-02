<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;

class CommentTest extends TestCase
{
    public function testComment(): void
    {
        
        $comment = new Comment();
        
        $content = 'This is a test comment.';
        $comment->setContent($content);
        $this->assertEquals($content, $comment->getContent());
        
        $recipe = new Recipe();
        $comment->setCommentRecipe($recipe);
        $this->assertSame($recipe, $comment->getCommentRecipe());
        
        $user = new User();
        $comment->setUser($user);
        $this->assertSame($user, $comment->getUser());
        
        $createdAt = new \DateTimeImmutable('2023-07-19 12:34:56');
        $comment->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $comment->getCreatedAt());
        
        $validated = true;
        $comment->setValidated($validated);
        $this->assertEquals($validated, $comment->isValidated());

        $this->assertTrue(true);
    }
}
