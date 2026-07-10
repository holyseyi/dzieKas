<?php

/**
 * Input Validation Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules Format: 'field' => 'required|email|min:3|max:255'
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        if (str_contains($rule, ':')) {
            [$ruleName, $param] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        match ($ruleName) {
            'required' => $this->checkRequired($field, $value),
            'email' => $this->checkEmail($field, $value),
            'min' => $this->checkMin($field, $value, (int) $param),
            'max' => $this->checkMax($field, $value, (int) $param),
            'numeric' => $this->checkNumeric($field, $value),
            'url' => $this->checkUrl($field, $value),
            default => null,
        };
    }

    private function checkRequired(string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    private function checkEmail(string $field, mixed $value): void
    {
        if ($value && !Security::isValidEmail((string) $value)) {
            $this->errors[$field] = 'Please enter a valid email address.';
        }
    }

    private function checkMin(string $field, mixed $value, int $min): void
    {
        if ($value && strlen((string) $value) < $min) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
        }
    }

    private function checkMax(string $field, mixed $value, int $max): void
    {
        if ($value && strlen((string) $value) > $max) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.";
        }
    }

    private function checkNumeric(string $field, mixed $value): void
    {
        if ($value && !is_numeric($value)) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
        }
    }

    private function checkUrl(string $field, mixed $value): void
    {
        if ($value && filter_var((string) $value, FILTER_VALIDATE_URL) === false) {
            $this->errors[$field] = 'Please enter a valid URL.';
        }
    }
}
