<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();
        $task->setTitle('Tâche 1');
        $task->setContent('Tâche 1');
        $task->setIsDone(0);
        $task->setCreatedAt(new \DateTimeImmutable('now'));
        $task->setUser($this->getReference(UserFixtures::USER_REFERENCE));

        $manager->persist($task);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(UserFixtures::class);
    }
}