<?php
namespace App\Command;

use App\Entity\BitstampEurHistory;
use App\Entity\BitstampUsdHistory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ApiClients\Client\Pusher\Event;
use React\EventLoop\Factory;
use ApiClients\Client\Pusher\AsyncClient;

class GetBitstampWssCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-bitstamp-wss')
            ->setDescription('Get a new data from bitstamp pusher app.')
            ->setHelp('This command allows you to get a new data from bitstamp pusher app.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Getting BitStamp Data',
            '============',
        ]);

        $this->getBitstampWssData();

        // outputs a message followed by a "\n"
        $output->writeln('End!');

    }

    private function getBitstampWssData(){

        $loop = Factory::create();
        /**
         * The App ID isn't a secret and comes from the bitstamp docs:
         * @link https://www.bitstamp.net/websocket/
         */
        $client = AsyncClient::create($loop, 'de504dc5763aeef9ff52');
        $channelitems = array('live_trades', 'live_trades_btceur');
        $channels = \Rx\Observable::fromArray($channelitems)
            ->flatMap(function ($channelitem) use ($client) {
                return $client->channel($channelitem);
            });
        $channels->subscribe(function (Event $event) {

            $channel = $event->getChannel();
            $eventData = $event->getData();

            $spotData = $eventData['price'];

            if ( $channel == "live_trades" ){
                $newMarketData = new BitstampUsdHistory();
                echo "Received BTC-USD Data = " . $spotData . PHP_EOL;
            } else {
                $newMarketData = new BitstampEurHistory();
                echo "Received BTC-EUR Data = " . $spotData . PHP_EOL;
            }

            $newMarketData->setSpot($spotData);
            $newMarketData->setInsertTime(time());

            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($newMarketData);

            $em->flush();


        });

        $loop->run();


    }
}