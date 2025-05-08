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
- Tests should use Laravel http testing client
- Only mock interactions with external services. If the interaction is not an HTTP request, do not mock it.
- Eloquent models required by http tests should be seeded via Laravel factories
- Model factories should be used in separate methods within the trait.
- It's so important that everything with the Models in namespace should not be mocked
- Always write multiple test methods to cover many HTTP scenarios like success, unauthorized, not found, etc.
- Always create a dedicated test trait to encapsulate shared mock behavior or setup logic.
- Namespaces should match the namespace of the tested class, with the class name as the final directory segment.
- Ensure that tests adhere to the project's directory structure.
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

namespace Tests\Smoke\Flashcard\Infrastructure\Http\Controllers\FavouriteController;

class FavouriteControllerTest extends TestCase
{
    use FavouriteControllerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    public function test__addFavourite_UserIsFlashcardOwner_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $flashcard = $this->createFlashcard([]);
        $request_entry_data = [
            'user_id' => $customer->wp_client_id,
            'recipe_id' => $recipe->getExternalId(),
        ];

        // WHEN
        $response = $this->actingAs($user)->json('POST', route('flashcards.add') [
            'flashcard_id' = $flashcard->id,
        ]);

        // THEN
        $response->assertNoContent();
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Tests\Smoke\Flashcard\Infrastructure\Http\Controllers\FavouriteController;

trait FavouriteMealControllerTrait
{
    private function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }
    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }
}
```