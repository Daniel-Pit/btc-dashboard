<?php
namespace App\Command;

use App\Entity\BitfinexEurHistory;
use App\Entity\BitfinexUsdHistory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class LoadBitfinexCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:load-bitfinex')
            ->setDescription('Load new datas from bitfinex.csv.')
			->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to csv file')
			->addOption('currency', null, InputOption::VALUE_REQUIRED, 'Currency Pair btcusd or btceur')
            ->setHelp('This command allows you to load large data from bitfinex.csv.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);

		$path = $input->getOption('path');
		$currency = $input->getOption('currency');

        $output->writeln([
            'Loading '. $currency .' Bitfinex Data from ' . $path,
            '============',
        ]);

        $file = fopen($path, "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        while(!feof($file))
        {
            $line_contents = trim(fgets($file));
            echo $line_contents. PHP_EOL;
            if ( $line_contents ){
                $line_values = explode(',', $line_contents);
                if ( count($line_values) > 1 ){
                    $insert_time = trim($line_values[0]);
                    $spot_value = trim($line_values[1]);

                    $output->writeln('insert_time = ' . $insert_time);
                    $output->writeln('spot_value = ' . $spot_value);
                    $this->loadBitfinexData($insert_time, $spot_value, $currency);
                }
            }
        }
        fclose($file);



        // outputs a message followed by a "\n"
        $output->writeln('End!');

    }

    private function loadBitfinexData($time_value, $value, $currency){

        if ( $currency == "btcusd" ){
            $newMarketData = new BitfinexUsdHistory();
        } else {
            $newMarketData = new BitfinexEurHistory();
        }

        $newMarketData->setSpot($value);
        $newMarketData->setInsertTime($time_value);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($newMarketData);

        $em->flush();

    }
}