<?php
namespace App\Command;

use App\Entity\BitfinexEurHistory;
use App\Entity\BitfinexUsdHistory;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;
use WebSocket\BadOpcodeException;

class GetBitfinexCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-bitfinex')
            ->setDescription('Get a new data from bitfinex.')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'What do you want? btcusd or btceur?', 'btcusd')
            ->setHelp('This command allows you to get a new data from bitfinex wss api.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $current_pair = $input->getOption('currency');

        if ( $current_pair == "btcusd" || $current_pair == 'btceur' ){

            $output->writeln([
                'Getting Bitfinex '. $current_pair .' Data',
                '============',
            ]);

            $this->getBitfinexWssData($current_pair);
        } else {
            $output->writeln('Invalid Currency!!!');
        }


        $output->writeln('End!');

    }

    private function getBitfinexWssData($currency_pair){

        $send_param = [
            "event" => "subscribe",
            "channel" => "ticker",
            "pair" => strtoupper($currency_pair)
        ];
        $pay_load = json_encode($send_param);

		$client = new Client("wss://api.bitfinex.com/ws/", ['timeout' => 600]);
        try {

            $client->send($pay_load);
            echo PHP_EOL;
            while (true) {
                try {
                    $result_string = trim($client->receive());

                    if ( !empty($result_string) ){

                        $json_data = json_decode($result_string, true);

                        if ( isset($json_data[7]) ) {

                            $spotData = $json_data[7];
                            echo "Received " . $currency_pair . " Data = " . $spotData . PHP_EOL;

                            if ( $currency_pair == "btcusd" ){
                                $newMarketData = new BitfinexUsdHistory();
                            } else {
                                $newMarketData = new BitfinexEurHistory();
                            }

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
        $this->getBitfinexWssData($currency_pair);

    }

}