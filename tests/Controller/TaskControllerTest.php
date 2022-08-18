<?php

namespace App\Test\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;
    /** @var TaskRepository */
    private $repository;
    private $path = '/tasks/';
    /** @var AbstractDatabaseTool $databaseTool */
    private $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Task::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'paul@test.com']);
        $this->client->loginUser($testUser);

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('To Do List app');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testIndexRedirect(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('label', "Nom d'utilisateur :");
    }

    public function testNew(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'paul@test.com']);
        $this->client->loginUser($testUser);

        $this->client->request('GET', sprintf('%screate', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Testing',
            'task[content]' => 'Testing',
        ]);

        self::assertResponseRedirects('/tasks/');

        self::assertCount(1, $this->repository->findAll());
    }

    public function testEdit(): void
    {
        $this->databaseTool->loadFixtures([
            TaskFixtures::class
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'paul@test.com']);
        $this->client->loginUser($testUser);

        $task = $this->repository->findOneBy(['title' => 'Tâche 1']);
        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $task->getId()));

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Something New',
            'task[content]' => 'Something New',
        ]);

        self::assertResponseRedirects('/tasks/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
    }

    public function testRemove(): void
    {
        $this->databaseTool->loadFixtures([
            TaskFixtures::class
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'paul@test.com']);
        $this->client->loginUser($testUser);

        self::assertCount(1, $this->repository->findAll());

        $this->client->request('GET', sprintf('%s', $this->path));
        $this->client->submitForm('Supprimer', [], 'GET');

        self::assertCount(0, $this->repository->findAll());
        self::assertResponseRedirects('/tasks/');
    }
}
