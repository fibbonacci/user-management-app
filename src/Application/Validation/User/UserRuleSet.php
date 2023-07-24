<?php

declare(strict_types=1);

namespace App\Application\Validation\User;

use App\Domain\User\Enums\RestrictedDomainsEnum;
use App\Domain\User\Enums\RestrictedNamesEnum;
use App\Domain\User\UserEntity;
use App\Domain\User\UserService;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\ValidationContext;

class UserRuleSet
{
    public function __construct(
        private readonly UserService $userService,
        #[Required]
        #[StringValue]
        #[Length(min: 8, max: 64)]
        #[Regex('/^[a-z0-9]+$/', message: "Name can contain only letters a-z (lowercase) and numbers 0-9")]
        #[Callback(method: 'validateUniqueness', skipOnError: true)]
        #[Callback(method: 'validateRestrictedNames', skipOnError: true)]
        public ?string $name = null,
        #[Required]
        #[StringValue]
        #[Email]
        #[Length(max: 255)]
        #[Callback(method: 'validateUniqueness', skipOnError: true)]
        #[Callback(method: 'validateRestrictedDomains', skipOnError: true)]
        public ?string $email = null,
        #[Length(max: 1000, skipOnEmpty: true)]
        public ?string $notes = null,
        private readonly ?UserEntity $userEntity = null,
    ) {
    }

    private function validateUniqueness(string $value, Callback $rule, ValidationContext $ctx): Result
    {
        $getter = 'get' . ucfirst($ctx->getAttribute());
        if ($this->userEntity && $this->userEntity->{$getter}() === $value) {
            return new Result();
        }

        if ($this->userService->countUsersByAttribute($ctx->getAttribute(), $value) > 0) {
            return (new Result())->addError(sprintf('The user with such %s already exists.', $ctx->getAttribute()));
        }

        return new Result();
    }

    private function validateRestrictedNames(string $value): Result
    {
        foreach (RestrictedNamesEnum::cases() as $restrictedName) {
            if (str_contains($value, $restrictedName->value)) {
                return (new Result())->addError('Provided name contains restricted words.');
            }
        }

        return new Result();
    }

    private function validateRestrictedDomains(string $value): Result
    {
        [, $domainPart] = explode('@', $value);

        if (in_array($domainPart, array_column(RestrictedDomainsEnum::cases(), 'value'))) {
            return (new Result())->addError('Provided email contains restricted domain.');
        }

        return new Result();
    }
}
