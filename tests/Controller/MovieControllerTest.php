<?php

namespace App\Tests\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MovieControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $movieRepository;
    private string $path = '/movie/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->movieRepository = $this->manager->getRepository(Movie::class);

        foreach ($this->movieRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Movie index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'movie[title]' => 'Testing',
            'movie[slug]' => 'Testing',
            'movie[synopsis]' => 'Testing',
            'movie[releaseYear]' => 'Testing',
            'movie[durationSeconds]' => 'Testing',
            'movie[storageBasePath]' => 'Testing',
            'movie[hlsMasterUrl]' => 'Testing',
            'movie[posterUrl]' => 'Testing',
            'movie[audioLanguages]' => 'Testing',
            'movie[ratingAvg]' => 'Testing',
            'movie[ratingCount]' => 'Testing',
            'movie[ageRating]' => 'Testing',
            'movie[status]' => 'Testing',
            'movie[createdAt]' => 'Testing',
            'movie[updatedAt]' => 'Testing',
            'movie[categories]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->movieRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Movie();
        $fixture->setTitle('My Title');
        $fixture->setSlug('My Title');
        $fixture->setSynopsis('My Title');
        $fixture->setReleaseYear('My Title');
        $fixture->setDurationSeconds('My Title');
        $fixture->setStorageBasePath('My Title');
        $fixture->setHlsMasterUrl('My Title');
        $fixture->setPosterUrl('My Title');
        $fixture->setAudioLanguages('My Title');
        $fixture->setRatingAvg('My Title');
        $fixture->setRatingCount('My Title');
        $fixture->setAgeRating('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCategories('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Movie');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Movie();
        $fixture->setTitle('Value');
        $fixture->setSlug('Value');
        $fixture->setSynopsis('Value');
        $fixture->setReleaseYear('Value');
        $fixture->setDurationSeconds('Value');
        $fixture->setStorageBasePath('Value');
        $fixture->setHlsMasterUrl('Value');
        $fixture->setPosterUrl('Value');
        $fixture->setAudioLanguages('Value');
        $fixture->setRatingAvg('Value');
        $fixture->setRatingCount('Value');
        $fixture->setAgeRating('Value');
        $fixture->setStatus('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCategories('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'movie[title]' => 'Something New',
            'movie[slug]' => 'Something New',
            'movie[synopsis]' => 'Something New',
            'movie[releaseYear]' => 'Something New',
            'movie[durationSeconds]' => 'Something New',
            'movie[storageBasePath]' => 'Something New',
            'movie[hlsMasterUrl]' => 'Something New',
            'movie[posterUrl]' => 'Something New',
            'movie[audioLanguages]' => 'Something New',
            'movie[ratingAvg]' => 'Something New',
            'movie[ratingCount]' => 'Something New',
            'movie[ageRating]' => 'Something New',
            'movie[status]' => 'Something New',
            'movie[createdAt]' => 'Something New',
            'movie[updatedAt]' => 'Something New',
            'movie[categories]' => 'Something New',
        ]);

        self::assertResponseRedirects('/movie/');

        $fixture = $this->movieRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getSynopsis());
        self::assertSame('Something New', $fixture[0]->getReleaseYear());
        self::assertSame('Something New', $fixture[0]->getDurationSeconds());
        self::assertSame('Something New', $fixture[0]->getStorageBasePath());
        self::assertSame('Something New', $fixture[0]->getHlsMasterUrl());
        self::assertSame('Something New', $fixture[0]->getPosterUrl());
        self::assertSame('Something New', $fixture[0]->getAudioLanguages());
        self::assertSame('Something New', $fixture[0]->getRatingAvg());
        self::assertSame('Something New', $fixture[0]->getRatingCount());
        self::assertSame('Something New', $fixture[0]->getAgeRating());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCategories());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Movie();
        $fixture->setTitle('Value');
        $fixture->setSlug('Value');
        $fixture->setSynopsis('Value');
        $fixture->setReleaseYear('Value');
        $fixture->setDurationSeconds('Value');
        $fixture->setStorageBasePath('Value');
        $fixture->setHlsMasterUrl('Value');
        $fixture->setPosterUrl('Value');
        $fixture->setAudioLanguages('Value');
        $fixture->setRatingAvg('Value');
        $fixture->setRatingCount('Value');
        $fixture->setAgeRating('Value');
        $fixture->setStatus('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setCategories('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/movie/');
        self::assertSame(0, $this->movieRepository->count([]));
    }
}
