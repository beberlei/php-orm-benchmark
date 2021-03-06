<?php

namespace OrmBench\Provider;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use OrmBench\Models\Doctrine\Posts;

class Doctrine extends AbstractProvider
{
    private $em;

    public function setUp()
    {
        $config = Setup::createAnnotationMetadataConfiguration(
            [DOCROOT . '/src/Models/Doctrine'],
            false
        );

        $this->em = EntityManager::create(require_once DOCROOT . '/config/doctrine.php', $config);
    }

    public function create()
    {
        $post = new Posts();

        $post->setTitle('Yet another article: ' . __CLASS__);
        $post->setBody('This is the body of the article.');
        $post->setCreatedAt(time());
        $post->setUpdatedAt(time());

        $this->em->persist($post);
        $this->em->flush();

        assert($post instanceof Posts);
        assert(is_numeric($post->getId()));
        assert($post->getId() > 0);

        $this->removePKs[] = $post->getId();
    }

    public function read(int $id)
    {
        $post = $this->em
            ->getRepository(Posts::class)
            ->findOneBy(['id' => $id]);
        
        assert($post instanceof Posts);

        $comments = $post->getComments();
        assert($comments->first()->getBody() === 'It is a comment.');
    }
}
