<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\GdaxEurHistory;
use App\Entity\GdaxUsdHistory;

class GetGdaxCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-gdax')
            ->setDescription('Get a new data from GDAX.')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'What do you want? btcusd or btceur?', 'btcusd')
            ->setHelp('This command allows you to get a new data from GDAX api.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $current_pair = $input->getOption('currency');

        if ( $current_pair == "btcusd" || $current_pair == 'btceur' ){

            $output->writeln([
                'Getting GDAX '. $current_pair .' Data',
                '============',
                '',
            ]);

            $this->getGdaxData($current_pair);
        } else {
            $output->writeln('Invalid Currency!!!');
        }


        $output->writeln('End!');

    }

    private function getGdaxData($currency){

        if ( $currency == "btcusd" ){
            $newMarketData = new GdaxUsdHistory();
			$api_currency = "BTC-USD";
        } else {
            $newMarketData = new GdaxEurHistory();
			$api_currency = "BTC-EUR";
        }

        $client = new \GuzzleHttp\Client();
        $baseUrl = 'https://api.gdax.com/products/' . $api_currency . '/ticker';
        $res = $client->request('GET', $baseUrl);
        if ( $res->getStatusCode() == 200 ){
            $response = $res->getBody();
            $jsonData = json_decode($response, true);

            $spotData = $jsonData['price'];
            echo $spotData . PHP_EOL;
            $newMarketData->setSpot($spotData);
            $newMarketData->setInsertTime(time());

            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($newMarketData);

            $em->flush();

        }

    }
}