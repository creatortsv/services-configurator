<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

class ServiceAbstract
{
    /**
     * @param array $attributes
     * @param Collection $constraints
     * 
     * @return void
     * @throws UnprocessableEntityHttpException
     */
    protected function validate(array $attributes, Collection $constraints): void
    {
        $validator = Validation::createValidator();
        if (count($violations = $validator->validate($attributes, $constraints))) {
            foreach ($violations as $error) {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new UnprocessableEntityHttpException(json_encode($errors));
        }
    }
}