<?php
namespace App\Command;

use App\Entity\GdaxEurHistory;
use App\Entity\GdaxUsdHistory;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;
use WebSocket\BadOpcodeException;

class GetGdaxWssCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-gdax-wss')
            ->setDescription('Get a new data from GDAX Wss.')
            ->setHelp('This command allows you to get a new data from GDAX Wss api.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		$output->writeln([
			'Getting GDAX Data',
			'============',
		]);

        $this->getGdaxWssData();


        $output->writeln('End!');

    }

    private function getGdaxWssData(){

        $send_param = [
            "type" => "subscribe",
		    "channels" => [
				[ 
					"name" => "ticker", 
					"product_ids" => ["BTC-USD", "BTC-EUR"] 
				]
			]
        ];
        $pay_load = json_encode($send_param);

		$client = new Client("wss://ws-feed.gdax.com", ['timeout' => 600]);
        try {

            $client->send($pay_load);
            echo PHP_EOL;
            while (true) {
                try {
                    $result_string = trim($client->receive());

                    if ( !empty($result_string) ){

                        $json_data = json_decode($result_string, true);

                        if ( isset($json_data['type']) && $json_data['type'] == "ticker" ) {

                            $currency_pair = $json_data['product_id'];

                            if ( $currency_pair == "BTC-USD" ){
                                $newMarketData = new GdaxUsdHistory();
                            } else {
                                $newMarketData = new GdaxEurHistory();
                            }

                            $spotData = $json_data['price'];
                            echo "Received ". $currency_pair ." Data = " . $spotData . PHP_EOL;

                            $newMarketData->setSpot($spotData);
                            $newMarketData->setInsertTime(time());

                            $em = $this->getContainer()->get('doctrine')->getManager();
                            $em->persist($newMarketData);

                            $em->flush();

                        }
                    }

                }
                catch (Exception $e) {
                    echo "Receive Error:";
                    echo $e->getMessage();
                    echo PHP_EOL;
                    $client->close();
                    break;
                }
            }

        } catch ( BadOpcodeException $ex ){
            echo "BadOpcodeException Error:";
            echo $ex->getMessage();
            echo PHP_EOL;
        }

        usleep(1000);
        $this->getGdaxWssData();

    }

}