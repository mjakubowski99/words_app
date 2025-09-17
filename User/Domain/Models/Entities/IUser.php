<?php

declare(strict_types=1);

namespace User\Domain\Models\Entities;

use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\UserId;

interface IUser
{
    public function getId(): UserId;

    public function getPassword(): string;

    public function getEmail(): string;

    public function getName(): string;

    public function getUserLanguage(): Language;
    public function getLearningLanguage(): Language;
    public function profileCompleted(): bool;
    public function setUserLanguage(Language $language): void;
    public function setLearningLanguage(Language $language): void;
    public function setProfileCompleted(): void;
}
