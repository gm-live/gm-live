<?php

declare(strict_types=1);

namespace App\Validators;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Di\Annotation\Inject;

abstract class AbstractValidator
{
    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected $oValidate;

}
