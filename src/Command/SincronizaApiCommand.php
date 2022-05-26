<?php

namespace SL\WebsiteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SL\WebsiteBundle\Services\ApiService;
use SL\WebsiteBundle\Services\DatabaseOperationsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SincronizaApiCommand extends Command
{
    protected static $defaultName = 'sl:api:sincronizar';
    private $em;
    private $dbService;
    private $apiService;

    public function __construct(EntityManagerInterface $em, DatabaseOperationsService $dbService, ApiService $apiService, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->dbService = $dbService;
        $this->apiService = $apiService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Envia um e-mail aos arrematantes solicitando alteração de senha.')
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->note(sprintf('Iniciando...'));

        try {
            // Limpa o banco de dados atual
            $this->dbService->clearAllTables();
            $data = $this->apiService->requestAllActiveSiteData();
            dump(json_decode($data->getBody(), true));
            // Carrega os leilões e bens ativos
        } catch (\Exception $e){
            $io->error($e->getMessage());
        }

        $io->writeln('');

        $io->success(sprintf('Comando finalizado com: Sucesso'));

        return 0;
    }

}
