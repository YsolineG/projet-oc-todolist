<?php

namespace App\Test\Controller;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;
    /** @var UserRepository */
    private $repository;
    private $path = '/users/';
    /** @var AbstractDatabaseTool $databaseTool */
    private $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(User::class);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testIndex(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $testUser = $this->repository->findOneBy(['email' => 'admin@test.com']);
        $this->client->loginUser($testUser);

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('To Do List app');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $testUser = $this->repository->findOneBy(['email' => 'admin@test.com']);
        $this->client->loginUser($testUser);

        $numObjsInRepo = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%screate', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Ajouter', [
            'user[email]' => 'Testing@test.com',
            'user[password][first]' => 'Testing',
            'user[password][second]' => 'Testing',
            'user[username]' => 'Testing',
        ]);

        self::assertResponseRedirects('/users/');

        self::assertCount($numObjsInRepo + 1, $this->repository->findAll());
    }

    public function testEdit(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $testUser = $this->repository->findOneBy(['email' => 'admin@test.com']);
        $this->client->loginUser($testUser);

        $user = $this->repository->findOneBy(['username' => 'Paul']);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $user->getId()));

        $this->client->submitForm('Modifier', [
            'user[password][first]' => 'Something New',
            'user[password][second]' => 'Something New',
        ]);

        self::assertResponseRedirects('/users/');

        $fixture = $this->repository->findAll();

        /** @var UserPasswordHasherInterface $userPasswordHasher */
        $userPasswordHasher = (static::getContainer()->get(UserPasswordHasherInterface::class));
        self::assertTrue($userPasswordHasher->isPasswordValid($fixture[0], 'Something New'));
    }
}
