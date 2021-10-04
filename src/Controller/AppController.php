<?php

namespace App\Controller;

use App\Entity\BitfinexEurHistory;
use App\Entity\BitfinexUsdHistory;
use App\Entity\BitstampEurHistory;
use App\Entity\BitstampUsdHistory;
use App\Entity\BtcMarketData;
use App\Entity\GdaxEurHistory;
use App\Entity\GdaxUsdHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\FunctionHelper;

class AppController extends Controller
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {

        $btc_usd_data = $this->getViewData("USD");
        $btc_usd_template = $this->getViewTemplate("USD", $btc_usd_data);

        $btc_eur_data = $this->getViewData("EUR");
        $btc_eur_template = $this->getViewTemplate("EUR", $btc_eur_data);

        return $this->render('app/index.html.twig', [
            'usdDataText' => $btc_usd_template,
            'eurDataText' => $btc_eur_template,
            'usdData' => json_encode($btc_usd_data),
            'eurData' => json_encode($btc_eur_data),
            'content_title' => "BTC - Dashboard"
        ]);
    }


    /**
     * @Route("/btc-usd", name="btc-usd")
     */
    public function btcusd(Request $request){

        $prevData = array();
        if ($content = $request->getContent())
        {
            $prevData = json_decode($content, true);
        }

        $data = $this->getViewData("USD", $prevData);
        $template = $this->getViewTemplate("USD", $data);
        $response = [
            "data" => json_encode($data),
            "text" => $template
        ];
        $json = json_encode($response);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/btc-eur", name="btc-eur")
     */
    public function btceur(Request $request){

        $prevData = array();
        if ($content = $request->getContent())
        {
            $prevData = json_decode($content, true);
        }

        $data = $this->getViewData("EUR", $prevData);
        $template = $this->getViewTemplate("EUR", $data);
        $response = [
            "data" => json_encode($data),
            "text" => $template
        ];
        $json = json_encode($response);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    private function getViewTemplate($currency, $data){

        $viewTemplate = "";

        foreach ($data as $each_data){

            if ( $currency == "USD" ) {
                $viewTemplate .= $this->get('twig')->render('app/display.html.twig',
                    $each_data
                );
            } else {
                $viewTemplate .= $this->get('twig')->render('app/display3m.html.twig',
                    $each_data
                );
            }

        }

        return $viewTemplate;

    }

    private function getViewData($currency, $prevData=array()){

        $current_time = time();
        $merge_market_data = [];

        $prev_bitstamp_data = isset($prevData['Bitstamp'])?$prevData['Bitstamp']:array();
        $bitstamp_data = $this->getMarketData($current_time, "Bitstamp", $currency, $prev_bitstamp_data);

        $prev_bitfinex_data = isset($prevData['Bitfinex'])?$prevData['Bitfinex']:array();
        $bitfinex_data = $this->getMarketData($current_time, "Bitfinex", $currency, $prev_bitfinex_data);

        $prev_gdax_data = isset($prevData['GDAX'])?$prevData['GDAX']:array();
        $gdax_data = $this->getMarketData($current_time, "GDAX", $currency, $prev_gdax_data);


        array_push($merge_market_data, $bitstamp_data);
        array_push($merge_market_data, $bitfinex_data);
        array_push($merge_market_data, $gdax_data);

        $averageData = FunctionHelper::getAverageData($merge_market_data);

        return [
            "Average" => $averageData,
            "Bitstamp" => $bitstamp_data,
            "Bitfinex" => $bitfinex_data,
            "GDAX" => $gdax_data
        ];
    }

    private function getMarketData($current_time_interval, $market_name, $currency, $old_data=array()){


        if ( $currency == "EUR" ) {
            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampEurHistory::class)
                        ->findLastData();

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexEurHistory::class)
                        ->findLastData();

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
                        ->getRepository(GdaxEurHistory::class)
                        ->findLastData();

                    break;
            }

        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampUsdHistory::class)
                        ->findLastData();

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findLastData();

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
                        ->getRepository(GdaxUsdHistory::class)
                        ->findLastData();

                    break;
            }

        }

        $current_spot = 0;

        if ($marketData) {
            $current_spot = $marketData[0]['spot'];
        }

        $prev_current_spot = isset($old_data['display_spot'])?$old_data['display_spot']:$current_spot;

        $current_spot = number_format($current_spot, 2, '.', '');
        $prev_current_spot = number_format($prev_current_spot, 2, '.', '');


        $today_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "0d");
        $today_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "0d");
        $today_high = $today_high_low['high_value'];
        $today_low = $today_high_low['low_value'];


        $ago_24h_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1d");
        $ago_24h_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1d");
        $ago_24h_high = $ago_24h_high_low['high_value'];
        $ago_24h_low = $ago_24h_high_low['low_value'];


//        $ago_1w_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1w");
//        $ago_1w_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1w");
//        $ago_1w_high = $ago_1w_high_low['high_value'];
//        $ago_1w_low = $ago_1w_high_low['low_value'];
//
//        $ago_1m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1m");
//        $ago_1m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1m");
//        $ago_1m_high = $ago_1m_high_low['high_value'];
//        $ago_1m_low = $ago_1m_high_low['low_value'];
//
//        $ago_3m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "3m");
//        $ago_3m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "3m");
//        $ago_3m_high = $ago_3m_high_low['high_value'];
//        $ago_3m_low = $ago_3m_high_low['low_value'];
//
//        $ago_6m_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "6m");
//        $ago_6m_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "6m");
//        $ago_6m_high = $ago_6m_high_low['high_value'];
//        $ago_6m_low = $ago_6m_high_low['low_value'];
//
//        $ago_1y_change = $this->getChangePeriod($market_name, $currency, $current_spot, $current_time_interval, "1y");
//        $ago_1y_high_low = $this->getHighLowPeriod($market_name, $currency, $current_spot, $current_time_interval, "1y");
//        $ago_1y_high = $ago_1y_high_low['high_value'];
//        $ago_1y_low = $ago_1y_high_low['low_value'];



        $ago_1w_change = 0;
        $ago_1m_change = 0;
        $ago_3m_change = 0;
        $ago_6m_change = 0;
        $ago_1y_change = 0;

        $ago_1w_high = 0;
        $ago_1w_low = 0;
        $ago_1m_high = 0;
        $ago_1m_low = 0;
        $ago_3m_high = 0;
        $ago_3m_low = 0;
        $ago_6m_high = 0;
        $ago_6m_low = 0;
        $ago_1y_high = 0;
        $ago_1y_low = 0;


        $btcCacheMarketData = $this->getDoctrine()
            ->getRepository(BtcMarketData::class)
            ->findMarketCurrencyData($market_name, $currency);

        if ( $btcCacheMarketData ){

            $ago_1w_change = $btcCacheMarketData[0]['ago_1w_change'];
            $ago_1m_change = $btcCacheMarketData[0]['ago_1m_change'];
            $ago_3m_change = $btcCacheMarketData[0]['ago_3m_change'];
            $ago_6m_change = $btcCacheMarketData[0]['ago_6m_change'];
            $ago_1y_change = $btcCacheMarketData[0]['ago_1y_change'];

            $ago_1w_high = $btcCacheMarketData[0]['ago_1w_high'];
            $ago_1w_low = $btcCacheMarketData[0]['ago_1w_low'];
            $ago_1m_high = $btcCacheMarketData[0]['ago_1m_high'];
            $ago_1m_low = $btcCacheMarketData[0]['ago_1m_low'];
            $ago_3m_high = $btcCacheMarketData[0]['ago_3m_high'];
            $ago_3m_low = $btcCacheMarketData[0]['ago_3m_low'];
            $ago_6m_high = $btcCacheMarketData[0]['ago_6m_high'];
            $ago_6m_low = $btcCacheMarketData[0]['ago_6m_low'];
            $ago_1y_high = $btcCacheMarketData[0]['ago_1y_high'];
            $ago_1y_low = $btcCacheMarketData[0]['ago_1y_low'];

        }



        $prev_today_high = isset($old_data['today_high'])?$old_data['today_high']:$today_high;
        $prev_today_low = isset($old_data['today_low'])?$old_data['today_low']:$today_low;
        $prev_ago_24h_high = isset($old_data['ago_24h_high'])?$old_data['ago_24h_high']:$ago_24h_high;
        $prev_ago_24h_low = isset($old_data['ago_24h_low'])?$old_data['ago_24h_low']:$ago_24h_low;
        $prev_ago_1w_high = isset($old_data['ago_1w_high'])?$old_data['ago_1w_high']:$ago_1w_high;
        $prev_ago_1w_low = isset($old_data['ago_1w_low'])?$old_data['ago_1w_low']:$ago_1w_low;
        $prev_ago_1m_high = isset($old_data['ago_1m_high'])?$old_data['ago_1m_high']:$ago_1m_high;
        $prev_ago_1m_low = isset($old_data['ago_1m_low'])?$old_data['ago_1m_low']:$ago_1m_low;
        $prev_ago_3m_high = isset($old_data['ago_3m_high'])?$old_data['ago_3m_high']:$ago_3m_high;
        $prev_ago_3m_low = isset($old_data['ago_3m_low'])?$old_data['ago_3m_low']:$ago_3m_low;
        $prev_ago_6m_high = isset($old_data['ago_6m_high'])?$old_data['ago_6m_high']:$ago_6m_high;
        $prev_ago_6m_low = isset($old_data['ago_6m_low'])?$old_data['ago_6m_low']:$ago_6m_low;
        $prev_ago_1y_high = isset($old_data['ago_1y_high'])?$old_data['ago_1y_high']:$ago_1y_high;
        $prev_ago_1y_low = isset($old_data['ago_1y_low'])?$old_data['ago_1y_low']:$ago_1y_low;


        $display_market_data = [
            'display_title' => $market_name,
            'display_spot' => $current_spot,
            'prev_display_spot' => $prev_current_spot,
            'display_spot_class' => '',
            'today_change' => $today_change,
            'today_high' => $today_high,
            'prev_today_high' => $prev_today_high,
            'today_high_class' => '',
            'today_low' => $today_low,
            'prev_today_low' => $prev_today_low,
            'today_low_class' => '',
            'ago_24h_change' => $ago_24h_change,
            'ago_24h_high' => $ago_24h_high,
            'prev_ago_24h_high' => $prev_ago_24h_high,
            'ago_24h_high_class' => '',
            'ago_24h_low' => $ago_24h_low,
            'prev_ago_24h_low' => $prev_ago_24h_low,
            'ago_24h_low_class' => '',
            'ago_1w_change' => $ago_1w_change,
            'ago_1w_high' => $ago_1w_high,
            'prev_ago_1w_high' => $prev_ago_1w_high,
            'ago_1w_high_class' => '',
            'ago_1w_low' => $ago_1w_low,
            'prev_ago_1w_low' => $prev_ago_1w_low,
            'ago_1w_low_class' => '',
            'ago_1m_change' => $ago_1m_change,
            'ago_1m_high' => $ago_1m_high,
            'prev_ago_1m_high' => $prev_ago_1m_high,
            'ago_1m_high_class' => '',
            'ago_1m_low' => $ago_1m_low,
            'prev_ago_1m_low' => $prev_ago_1m_low,
            'ago_1m_low_class' => '',
            'ago_3m_change' => $ago_3m_change,
            'ago_3m_high' => $ago_3m_high,
            'prev_ago_3m_high' => $prev_ago_3m_high,
            'ago_3m_high_class' => '',
            'ago_3m_low' => $ago_3m_low,
            'prev_ago_3m_low' => $prev_ago_3m_low,
            'ago_3m_low_class' => '',
            'ago_6m_change' => $ago_6m_change,
            'ago_6m_high' => $ago_6m_high,
            'prev_ago_6m_high' => $prev_ago_6m_high,
            'ago_6m_high_class' => '',
            'ago_6m_low' => $ago_6m_low,
            'prev_ago_6m_low' => $prev_ago_6m_low,
            'ago_6m_low_class' => '',
            'ago_1y_change' => $ago_1y_change,
            'ago_1y_high' => $ago_1y_high,
            'prev_ago_1y_high' => $prev_ago_1y_high,
            'ago_1y_high_class' => '',
            'ago_1y_low' => $ago_1y_low,
            'prev_ago_1y_low' => $prev_ago_1y_low,
            'ago_1y_low_class' => '',
        ];

        foreach ($display_market_data as $dataKey => $data){

            if ( substr($dataKey, 0, 5) === "prev_" ){
                $current_value_datakey = str_replace("prev_", "", $dataKey);

                $data_class_key = $current_value_datakey."_class";
                if ( $display_market_data[$current_value_datakey] > $display_market_data[$dataKey] ){
                    $display_market_data[$data_class_key] = "elementGreenToFadeInAndOut";
                }
                if ( $display_market_data[$current_value_datakey] < $display_market_data[$dataKey] ){
                    $display_market_data[$data_class_key] = "elementRedToFadeInAndOut";
                }
            }

        }

        return $display_market_data;

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



        if ( $currency == "EUR" ) {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
                        ->getRepository(GdaxEurHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
            }


        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampUsdHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findPeriodFirstSpot($periodFirstTimestamp);

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
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
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
                        ->getRepository(GdaxEurHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
            }



        } else {

            switch ($market_name){
                case 'Bitstamp':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitstampUsdHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'Bitfinex':
                    $marketData = $this->getDoctrine()
                        ->getRepository(BitfinexUsdHistory::class)
                        ->findPeriodHighLowSpot($periodFirstTimestamp, $current_time_inteval);

                    break;
                case 'GDAX':
                    $marketData = $this->getDoctrine()
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
