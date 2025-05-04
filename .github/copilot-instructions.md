# GitHub Copilot Instructions for PHP 8.3 & Laravel 12 DDD Project
## Tech Stack
- PHP 8.3, Laravel 12, PostgreSQL
- DDD architecture with CQRS pattern
- API versions: v1 and v2
- PHPUnit for testing
## Code Style & Conventions
- Use **snake_case** for variable and property naming
- Use **camelCase** for method naming
- Strong typing with PHP 8.3 features
- PSR-12 coding standard
## Project Structure
```
├── Admin/               # Admin panel using Filament
├── Flashcard/           # Flashcard domain module
├── Exercise/            # Exercise domain module
├── User/                # User domain module
├── Shared/              # Shared components
├── Integrations/        # External integrations (Gemini)
├── entry/               # Laravel application entry point
```
## Domain Module Layers
- **Domain**: Models, ValueObjects, Contracts, Exceptions
- **Application**: Command, Query, Services, DTO, Repositories (interfaces)
- **Infrastructure**: Http (Controllers, Requests, Resources), Repositories (implementations)
## Testing Guidelines
- Tests should follow project directory structure
- Test method naming: `test__{methodName}_{case}_{expectation}`
- All tests should derive from `Tests\TestCase`
- Use `DatabaseTransactions` trait in all tests
- Always initialize tested properties via Laravel container in `setUp` method
- Test types: Unit, Integration, Smoke
## Example test structure:
```php
namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    
    private FlashcardRepository $flashcard_repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->flashcard_repository = app(FlashcardRepository::class);
    }
    
    public function test__create_validInput_returnsFlashcard(): void
    {
        //GIVEN
        //WHEN
        //THEN
    }
}
```
## Patterns & Practices
- Repository interfaces in Application layer, implementations in Infrastructure
- Value objects for primitive types with business meaning
- Command/Query bus pattern
- Mappers for database to domain model conversion
- Factory pattern for complex object creation
## Special Features
- SmTwo spaced repetition algorithm
- AI content generation with Gemini
- Multiple API versions (v1, v2)
## When Coding
- Match the correct namespace for module and layer
- Follow layered architecture principles
- Use repository interfaces from Application layer
- Implement repositories in Infrastructure layer
- Remember snake_case for variables, camelCase for methods