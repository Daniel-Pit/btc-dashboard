<?php
namespace App\Command;

use App\Entity\BitstampEurHistory;
use App\Entity\BitstampUsdHistory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GetBitstampCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-bitstamp')
            ->setDescription('Get a new data from bitstamp.')
            ->setHelp('This command allows you to get a new data from bitstamp api.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Getting BitStamp Data',
            '============',
            '',
        ]);

        $this->getBitstampData('btcusd');
        usleep(1000);
        $this->getBitstampData('btceur');

        $output->writeln('End!');

    }

    private function getBitstampData($currency){

        if ( $currency == "btcusd" ){
            $newMarketData = new BitstampUsdHistory();
        } else {
            $newMarketData = new BitstampEurHistory();
        }

        $client = new \GuzzleHttp\Client();
        $baseUrl = 'https://www.bitstamp.net/api/v2/ticker/'. $currency;
        $res = $client->request('GET', $baseUrl);
        if ( $res->getStatusCode() == 200 ){
            $response = $res->getBody();
            $jsonData = json_decode($response, true);

            $spotData = $jsonData['last'];

            $newMarketData->setSpot($spotData);
            $newMarketData->setInsertTime(time());

            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($newMarketData);

            $em->flush();

        }

    }
}