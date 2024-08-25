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

    public function validate($value, Constraint $constraint)
    {
        $endDateTime = (clone $value)->modify('+2 hours');
        $existingOrders = $this->ordersRepository->countOrdersInRange($value, $endDateTime);

        if ($existingOrders >= 3) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('d/m/Y H:i'))
                ->addViolation();
        }
    }
}
