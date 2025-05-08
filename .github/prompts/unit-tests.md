## Tech Stack
- PHP 8.3, Laravel 12, PostgreSQL
- DDD, CQRS architecture
- API versions: v1 and v2
- PHPUnit for testing
## Code Style & Conventions
- So important is to use **snake_case** for variable and property naming
- Use **camelCase** for method naming
- Strong typing with PHP 8.3 features
- PSR-12 coding standard
## Testing Guidelines
- It's so important to always write multiple test methods to cover a wide range of scenarios, including edge cases, exception handling, and data validation.
- Eloquent models should never be mocked. Instead, seed them using Laravel’s database factories.
- In general try to mock every dependency, an exception to the rule are Repository tests in which we shouldn't mock database interactions.
- Use Laravel Eloquent factories to generate test data for the database if needed
- Model factories should be used in separate methods within the trait.
- Always write multiple test methods to cover a wide range of scenarios, including edge cases, exception handling, and data validation.
- Always create a dedicated test trait to encapsulate shared mock behavior or setup logic.
- Namespaces should match the namespace of the tested class, with the class name as the final directory segment.
- Ensure that tests adhere to the project's directory structure.
- Mocks should be initialized within the test class itself, while all mock behaviors should be implemented as separate methods inside the test trait.
- Thoroughly analyze all dependencies of the tested class to identify potential edge cases.
- Use Mockery for mocking dependencies and expectations.
- Follow this naming convention for test methods: test__{methodName}_{scenario}_{expectedOutcome}
- All test classes must extend the base Tests\TestCase class.
- Apply the DatabaseTransactions trait to ensure database changes are rolled back between tests.
- Always initialize the class under test using the Laravel service container via $this->app->make(...).

## Example test structure:

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

class FlashcardDeckReadRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    private FlashcardDeckReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardDeckReadRepository::class);
    }

    public function test__getAdminDecks_LanguageLevelFilterWorks(): void
    {
        // GIVEN
        $user = $this->createUser();
        $other = $this->createFlashcardDeck([
            'user_id' => null,
            'admin_id' => Admin::factory()->create(),
            'name' => 'Nal',
            'default_language_level' => LanguageLevel::A2,
        ]);
        $expected = $this->createFlashcardDeck([
            'user_id' => null,
            'admin_id' => Admin::factory()->create(),
            'name' => 'Alan',
            'default_language_level' => LanguageLevel::A1,
        ]);

        // WHEN
        $results = $this->repository->getAdminDecks($user->getId(), LanguageLevel::A1, 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($expected->id, $results[0]->getId()->getValue());
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

trait FlashcardDeckReadRepositoryTrait
{
    public function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\FlashcardPollManager;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Mockery\MockInterface;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Services\FlashcardPollManager;
use Flashcard\Application\Services\FlashcardPollResolver;
use Flashcard\Application\Repository\IFlashcardPollRepository;

class FlashcardPollManagerTest extends TestCase
{
    private FlashcardPollManager $service;

    private FlashcardPollResolver $flashcard_poll_resolver;
    private IFlashcardPollRepository|MockInterface $repository;
    private IFlashcardSelector|MockInterface $selector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flashcard_poll_resolver = \Mockery::mock(FlashcardPollResolver::class);
        $this->repository = \Mockery::mock(IFlashcardPollRepository::class);
        $this->selector = \Mockery::mock(IFlashcardSelector::class);
        $this->service = $this->app->make(FlashcardPollManager::class, [
            'resolver' => $this->flashcard_poll_resolver,
            'repository' => $this->repository,
            'selector' => $this->selector,
        ]);
    }

    public function test__refresh_WhenNewPoll_AddFlashcardsFromSelectorToPoll(): void
    {
        // GIVEN
        $user_id = new UserId(Uuid::uuid4()->toString());
        $flashcards = $this->mockFlashcards();
        $poll = $this->mockFlashcardsToPoll($flashcards);

        // WHEN
        $poll = $this->service->refresh($user_id);

        // THEN
        $this->assertSame(2, count($poll->getFlashcardIdsToAdd()));
        $this->assertSame($flashcards[0]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[0]->getValue());
        $this->assertSame($flashcards[1]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[1]->getValue());
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\FlashcardPollManager;

use Flashcard\Domain\Models\FlashcardPoll;trait FlashcardPollManagerTest
{
    private function mockFlashcards(): array
    {
        return [
            \Mockery::mock(Flashcard::class)->allows(['getId' => new FlashcardId()])
            \Mockery::mock(Flashcard::class)->allows(['getId' => new FlashcardId()]),
        ]
    }
    
    private function mockFlashcardsToPoll(array $flashcards): FlashcardPoll
    {
        $poll = new FlashcardPoll($user_id, 0);
        $this->flashcard_poll_resolver->shouldReceive('resolve')->andReturn($poll);
        $this->selector->shouldReceive('selectToPoll')->andReturn($flashcards);
        return $poll;
    }
}
```