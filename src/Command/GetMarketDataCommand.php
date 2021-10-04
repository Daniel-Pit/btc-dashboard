<?php
namespace App\Command;

use App\Entity\BitfinexEurHistory;
use App\Entity\BitfinexUsdHistory;
use App\Entity\BitstampEurHistory;
use App\Entity\BitstampUsdHistory;
use App\Entity\BtcMarketData;
use App\Entity\GdaxEurHistory;
use App\Entity\GdaxUsdHistory;
use App\Repository\BtcMarketDataRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;
use WebSocket\BadOpcodeException;

class GetMarketDataCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:get-market-data')
            ->setDescription('update market table from all historical data.')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'What do you want? usd or eur?', 'usd')
            ->addOption('market', null, InputOption::VALUE_REQUIRED, 'What do you want? Bitstamp or Bitfinex, GDAX?', 'Bitstamp')
            ->setHelp('This command allows you to update market data.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $currency_pair = $input->getOption('currency');
        $market_name = $input->getOption('market');

        if ( ($currency_pair == "usd" || $currency_pair == 'eur') && ($market_name == "Bitstamp" || $market_name == "Bitfinex" || $market_name == "GDAX") ){

            $output->writeln([
                'Updating Market ' . $market_name . ' ' . $currency_pair .' Cache Data',
                '============',
            ]);
            $current_time = time();
            $currency_pair = strtoupper($currency_pair);

            $this->getMarketData($current_time, $market_name, $currency_pair);

        } else {
            $output->writeln('Invalid Currency or Market!!!');
        }


        $output->writeln('End!');

    }

    private function getMarketData($current_time_interval, $market_name, $currency){

		echo "current time = " . $current_time_interval . PHP_EOL;

        if ( $currency == "EUR" ) {
            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampEurHistory::class)
                        ->findLastData();

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexEurHistory::class)
                        ->findLastData();

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxEurHistory::class)
                        ->findLastData();

                    break;
            }

        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampUsdHistory::class)
                        ->findLastData();

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findLastData();

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxUsdHistory::class)
                        ->findLastData();

                    break;
            }

        }

        $current_spot = 0;

        if ($marketData) {
            $current_spot = $marketData[0]['spot'];
        }

        $current_spot = number_format($current_spot, 2, '.', '');

        //$ago_24h_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1d");
        $ago_1w_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1w");
        $ago_1m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1m");
        $ago_3m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "3m");
        $ago_6m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "6m");
        $ago_1y_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1y");


		echo "ago_1w_change = " . $ago_1w_change . PHP_EOL;
		echo "ago_1m_change = " . $ago_1m_change . PHP_EOL;
		echo "ago_3m_change = " . $ago_3m_change . PHP_EOL;
		echo "ago_6m_change = " . $ago_6m_change . PHP_EOL;
		echo "ago_1y_change = " . $ago_1y_change . PHP_EOL;

        //$ago_24h_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1d");
        //$ago_24h_high = $ago_24h_high_low['high_value'];
        //$ago_24h_low = $ago_24h_high_low['low_value'];
        $ago_1w_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1w");
        $ago_1w_high = $ago_1w_high_low['high_value'];
        $ago_1w_low = $ago_1w_high_low['low_value'];
        $ago_1m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1m");
        $ago_1m_high = $ago_1m_high_low['high_value'];
        $ago_1m_low = $ago_1m_high_low['low_value'];
        $ago_3m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "3m");
        $ago_3m_high = $ago_3m_high_low['high_value'];
        $ago_3m_low = $ago_3m_high_low['low_value'];
        $ago_6m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "6m");
        $ago_6m_high = $ago_6m_high_low['high_value'];
        $ago_6m_low = $ago_6m_high_low['low_value'];
        $ago_1y_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1y");
        $ago_1y_high = $ago_1y_high_low['high_value'];
        $ago_1y_low = $ago_1y_high_low['low_value'];

		echo "ago_1w_high = " . $ago_1w_high . PHP_EOL;
		echo "ago_1w_low = " . $ago_1w_low . PHP_EOL;
		echo "ago_1m_high = " . $ago_1m_high . PHP_EOL;
		echo "ago_1m_low = " . $ago_1m_low . PHP_EOL;
		echo "ago_3m_high = " . $ago_3m_high . PHP_EOL;
		echo "ago_3m_low = " . $ago_3m_low . PHP_EOL;
		echo "ago_6m_high = " . $ago_6m_high . PHP_EOL;
		echo "ago_6m_low = " . $ago_6m_low . PHP_EOL;
		echo "ago_1y_high = " . $ago_1y_high . PHP_EOL;
		echo "ago_1y_low = " . $ago_1y_low . PHP_EOL;


        $btcMarketCurrencyData = $this->getContainer()->get('doctrine')
            ->getRepository(BtcMarketData::class)
            ->findOneBy([
                'market' => $market_name,
                'currency' => $currency,
            ]);

        if ( !$btcMarketCurrencyData ){
            $btcMarketCurrencyData = new BtcMarketData();

            $btcMarketCurrencyData->setMarket($market_name);
            $btcMarketCurrencyData->setCurrency($currency);
        }


        $btcMarketCurrencyData->setAgo24hChange(0.00);
        $btcMarketCurrencyData->setAgo24hHigh(0.00);
        $btcMarketCurrencyData->setAgo24hLow(0.00);

        $btcMarketCurrencyData->setAgo1wChange($ago_1w_change);
        $btcMarketCurrencyData->setAgo1wHigh($ago_1w_high);
        $btcMarketCurrencyData->setAgo1wLow($ago_1w_low);

        $btcMarketCurrencyData->setAgo1mChange($ago_1m_change);
        $btcMarketCurrencyData->setAgo1mHigh($ago_1m_high);
        $btcMarketCurrencyData->setAgo1mLow($ago_1m_low);

        $btcMarketCurrencyData->setAgo3mChange($ago_3m_change);
        $btcMarketCurrencyData->setAgo3mHigh($ago_3m_high);
        $btcMarketCurrencyData->setAgo3mLow($ago_3m_low);

        $btcMarketCurrencyData->setAgo6mChange($ago_6m_change);
        $btcMarketCurrencyData->setAgo6mHigh($ago_6m_high);
        $btcMarketCurrencyData->setAgo6mLow($ago_6m_low);

        $btcMarketCurrencyData->setAgo1yChange($ago_1y_change);
        $btcMarketCurrencyData->setAgo1yHigh($ago_1y_high);
        $btcMarketCurrencyData->setAgo1yLow($ago_1y_low);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($btcMarketCurrencyData);

        $em->flush();


    }

    private function getChangePeriod($market_name, $currency, $current_value, $current_time_inteval, $period){


        $period_first = $current_value;

        switch ($period){
            case "0d":
                $periodFirstTimestamp = strtotime(date('Y-m-d 00:00:00', $current_time_inteval));
                break;
            case "1d":
                $periodFirstTimestamp = $current_time_inteval - 86400;//24 * 60 * 60;
                break;
            case "1w":
                $periodFirstTimestamp = $current_time_inteval - 604800;//7 * 24 * 60 * 60;
                break;
            case "1m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-1 month", $current_time_inteval)));
                break;
            case "3m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-3 month", $current_time_inteval)));
                break;
            case "6m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-6 month", $current_time_inteval)));
                break;
            case "1y":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-1 year", $current_time_inteval)));
                break;
        }


		echo "Time Period = " . $period . " TimeStamp = " . $periodFirstTimestamp . PHP_EOL;
        if ( $currency == "EUR" ) {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
            }


        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampUsdHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxUsdHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
            }

        }

        if ( $marketData ){

            $period_first = $marketData[0]['spot'];

        }

        $period_changed = ($current_value - $period_first)/$period_first * 100;

        return number_format($period_changed, 2, '.', '');
    }

    private function getHighLowPeriod($market_name, $currency, $current_value, $current_time_inteval, $period){

        $high_low_value = [
            'high_value' => $current_value,
            'low_value' => $current_value
        ];

        switch ($period){
            case "0d":
                $periodFirstTimestamp = strtotime(date('Y-m-d 00:00:00', $current_time_inteval));
                break;
            case "1d":
                $periodFirstTimestamp = $current_time_inteval - 86400;//24 * 60 * 60;
                break;
            case "1w":
                $periodFirstTimestamp = $current_time_inteval - 604800;//7 * 24 * 60 * 60;
                break;
            case "1m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-1 month", $current_time_inteval)));
                break;
            case "3m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-3 month", $current_time_inteval)));
                break;
            case "6m":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-6 month", $current_time_inteval)));
                break;
            case "1y":
                $periodFirstTimestamp = strtotime(date("Y-m-d H:i:s", strtotime("-1 year", $current_time_inteval)));
                break;
        }



        if ( $currency == "EUR" ) {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
            }



        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitstampUsdHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'GDAX':
                    $marketData = $this->getContainer()->get('doctrine')
                        ->getRepository(GdaxUsdHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
            }


        }

        if ( $marketData ){

            $high_low_value['high_value'] = $marketData[0]['high_value'];
            $high_low_value['low_value'] = $marketData[0]['low_value'];

        }

        $high_low_value['high_value'] = number_format($high_low_value['high_value'], 2, '.', '');
        $high_low_value['low_value'] = number_format($high_low_value['low_value'], 2, '.', '');

        return $high_low_value;
    }
}