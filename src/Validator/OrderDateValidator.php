<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Repository\OrdersRepository;

class OrderDateValidator extends ConstraintValidator
{
    private $ordersRepository;

    public function __construct(OrdersRepository $ordersRepository)
    {
        $this->ordersRepository = $ordersRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        $existingOrders = $this->ordersRepository->countByDate($value);

        if ($existingOrders >= 3) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('d/m/Y H:i'))
                ->addViolation();
        }
    }
}
