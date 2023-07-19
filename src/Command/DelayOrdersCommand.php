<?php

namespace App\Command;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:delay-orders',
    description: 'Delays all "processing" orders that have passed their delivery time.'
)]
class DelayOrdersCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager,string $name = 'app:delay-orders')
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orders = $this->entityManager->getRepository(Order::class)->findProcessingOrdersBeforeDate(new \DateTime());
        $orderCount = count($orders);

        if ($orderCount > 0) {
            /** @var Order $order */
            foreach ($orders as $order) {
                $order->setStatus(Order::STATUS_DELAYED);
                $this->entityManager->persist($order);
            }

            $this->entityManager->flush();
        }

        $output->writeln(sprintf('%s Order(s) delayed.', $orderCount));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                'This command delays all orders with estimated delivery dates before the time you execute this command.'
            )
        ;
    }
}