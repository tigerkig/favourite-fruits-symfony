<?php

namespace App\Command;

use App\Entity\Fruit;
use App\Entity\Nutrition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;

// the name of the command is what users type after "php bin/console"

class GetFruitsCommand extends Command
{
    protected static $defaultName = 'app:get-fruits';  
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {       
        $client = HttpClient::create();

        $response = $client->request(
            'GET',
            'https://fruityvice.com/api/fruit/all'
        );

        $statusCode = $response->getStatusCode();
        $content = $response->toArray();

        if(count($content) > 0 && $statusCode === 200) {
            for($i = 0; $i < count($content) ; $i++) {
                $fruit = new Fruit();

                $fruit->setName($content[$i]['name']);
                $fruit->setGenus($content[$i]['genus']);
                $fruit->setFamily($content[$i]['family']);
                $fruit->setOrderF($content[$i]['order']);
                $fruit->setCreatedAt(new \DateTimeImmutable("now"));
                $fruit->setUpdatedAt(new \DateTimeImmutable("now"));
        
                $nutrition = new Nutrition();
                $nutrition->setCarbohydrates($content[$i]['nutritions']['carbohydrates']);
                $nutrition->setProtein($content[$i]['nutritions']['protein']);
                $nutrition->setFat($content[$i]['nutritions']['fat']);
                $nutrition->setCalories($content[$i]['nutritions']['calories']);
                $nutrition->setSugar($content[$i]['nutritions']['sugar']);
                $nutrition->setCreatedAt(new \DateTimeImmutable("now"));
                $nutrition->setUpdatedAt(new \DateTimeImmutable("now"));
        
                $fruit->setNutrition($nutrition);
        
                $this->entityManager->persist($fruit);
                $this->entityManager->flush();
            }
        }        

        return Command::SUCCESS;
    }
}

