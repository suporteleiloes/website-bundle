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
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            // Limpa o banco de dados atual
            $this->dbService->clearAllTables();
            $response = $this->apiService->requestAllActiveSiteData(); // @TODO: Precisa receber também os bens de venda direta
            $data = json_decode($response->getBody(), true);
            if (isset($data['leiloes']) && is_array($data['leiloes']) && count($data['leiloes'])) {
                $io->note('Sincronizando leilões');
                foreach ($data['leiloes'] as $leilao) {
                    $this->apiService->processLeilao($leilao);
                }
                $io->note('Leilões sincronizados. Total: ' . count($data['leiloes']));
            }

            if (isset($data['bens']) && is_array($data['bens']) && count($data['bens'])) {
                $io->note('Sincronizando bens de venda direta');
                foreach ($data['bens'] as $bem) {
                    $this->apiService->processLote($bem, true, false);
                }
                $io->note('Bens em Venda Direta sincronizados. Total: ' . count($data['bens']));
            }

            //

            if (isset($data['banners']) && is_array($data['banners']) && count($data['banners'])) {
                $io->note('Sincronizando banners');
                foreach ($data['banners'] as $banner) {
                    $this->apiService->processBanner($banner, true);
                }
                $io->note('Banners sincronizados. Total: ' . count($data['banners']));
            }

            if (isset($data['posts']) && is_array($data['posts']) && count($data['posts'])) {
                $io->note('Sincronizando posts');
                foreach ($data['posts'] as $post) {
                    $this->apiService->processPost($post, true);
                }
                $io->note('Posts sincronizados. Total: ' . count($data['posts']));
            }

            if (isset($data['contents']) && is_array($data['contents']) && count($data['contents'])) {
                $io->note('Sincronizando contents');
                foreach ($data['contents'] as $content) {
                    $this->apiService->processContent($content, true);
                }
                $io->note('Contents sincronizados. Total: ' . count($data['contents']));
            }

            //

            $this->em->flush();
            $this->em->clear();
            $this->apiService->geraCacheLotes();
            // Carrega os leilões e bens ativos
        } catch (\Throwable $e){
            dump($e->getMessage());
            $io->error(sprintf('%s (file %s line %s)', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        $io->writeln('');

        $io->success(sprintf('Comando finalizado com: Sucesso'));

        return 0;
    }

}
