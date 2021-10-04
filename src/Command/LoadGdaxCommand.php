<?php
namespace App\Command;

use App\Entity\GdaxEurHistory;
use App\Entity\GdaxUsdHistory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\UsdHistory;
use App\Entity\EurHistory;

class LoadGdaxCommand extends ContainerAwareCommand
{

    public function __construct()
    {
        // you *must* call the parent constructor
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:load-gdax')
            ->setDescription('Load new datas from gdax.csv.')
			->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to csv file')
			->addOption('currency', null, InputOption::VALUE_REQUIRED, 'Currency Pair btcusd or btceur')
            ->setHelp('This command allows you to load large data from gdax.csv.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);

		$path = $input->getOption('path');
		$currency = $input->getOption('currency');

        $output->writeln([
            'Loading '. $currency .' Gdax Data from ' . $path,
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
                    $this->loadGdaxData($insert_time, $spot_value, $currency);
                }
            }
        }
        fclose($file);



        // outputs a message followed by a "\n"
        $output->writeln('End!');

    }

    private function loadGdaxData($time_value, $value, $currency){

        if ( $currency == "btcusd" ){
            $newMarketData = new GdaxUsdHistory();
        } else {
            $newMarketData = new GdaxEurHistory();
        }

        $newMarketData->setSpot($value);
        $newMarketData->setInsertTime($time_value);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($newMarketData);

        $em->flush();

    }
}