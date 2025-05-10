## Tech Stack
- PHP 8.3, Laravel 12, PostgreSQL
- DDD, CQRS architecture
- API versions: v1 and v2
- PHPUnit for testing
## Code Style & Conventions
- Use **snake_case** for variable and property naming
- Use **camelCase** for method naming
- Strong typing with PHP 8.3 features
- PSR-12 coding standard
## Testing Guidelines
- Eloquent models should never be mocked. Instead, seed them using Laravel’s database factories.
- It's so important that everything with the Models in namespace should not be mocked
- Only mock interactions with external services. If the interaction is not an HTTP request, do not mock it.
- Always write multiple test methods that cover a broad range of scenarios, including edge cases, exception handling, and data validation.
- Always create a dedicated test trait.
- Namespaces should mirror the namespace of the tested class and must end with the class name as the lowest-level directory.
- Mocks should be initialized in the test class, but all mocked behaviors should be defined in separate methods within the trait.
- Analyze all dependencies of the class when identifying edge cases.
- Use Eloquent factories to seed database data.
- Model factories should be used in separate methods within the trait.
- Use Mockery for mocking dependencies.
- Ensure tests follow the project's directory structure.
- Name test methods using the convention: test__{methodName}_{scenario}_{expectedOutcome}. Also, annotate them with @test to explicitly mark them as test methods.
- All tests should extend the Tests\TestCase class.
- Include the DatabaseTransactions trait in all test classes.
- Always initialize the class under test using $this->app->make(...) from the service container.
## Example test structure:
```php
<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\User;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use Tests\Base\FlashcardTestCase;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Command\CreateSession;
use Flashcard\Application\Command\CreateSessionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateSessionHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private CreateSessionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(CreateSessionHandler::class);
    }

    public function test__createSessionHandler_ShouldCreateSession(): void
    {
        // GIVEN
        $user_id = User::factory()->create()->getId();
        $deck_id = $this->createDeckId($this->createFlashcardDeck([
            'user_id' => $user_id->getValue(),
        ]));
        $cards_per_session = 5;
        $device = 'Mozilla/Firefox';
        $command = new CreateSession(
            $user_id,
            $cards_per_session,
            $device,
            $deck_id,
            SessionType::FLASHCARD,
        );

        // WHEN
        $result = $this->command_handler->handle($command);

        // THEN
        $this->assertTrue($result->success());
    }
}
```
```php
<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

trait CreateSessionHandlerTrait 
{
    private function createDeckId(FlashcardDeck $deck): FlashcardDeckId
    {
       return new FlashcardDeckId($deck->id);
    }
    
    private function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }
}
```
