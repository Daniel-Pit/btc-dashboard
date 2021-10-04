<?php

namespace App\Utils;


class FunctionHelper
{

    public static function getAverageData($marketDataArray=array()){

        $market_count = count($marketDataArray);
        $averageData = [
            'display_title' => "Average",
            'display_spot' => 0,
            'prev_display_spot' => 0,
            'display_spot_class' => '',
            'today_change' => 0,
            'today_high' => 0,
            'prev_today_high' => 0,
            'today_high_class' => '',
            'today_low' => 0,
            'prev_today_low' => 0,
            'today_low_class' => '',
            'ago_24h_change' => 0,
            'ago_24h_high' => 0,
            'prev_ago_24h_high' => 0,
            'ago_24h_high_class' => '',
            'ago_24h_low' => 0,
            'prev_ago_24h_low' => 0,
            'ago_24h_low_class' => '',
            'ago_1w_change' => 0,
            'ago_1w_high' => 0,
            'prev_ago_1w_high' => 0,
            'ago_1w_high_class' => '',
            'ago_1w_low' => 0,
            'prev_ago_1w_low' => 0,
            'ago_1w_low_class' => '',
            'ago_1m_change' => 0,
            'ago_1m_high' => 0,
            'prev_ago_1m_high' => 0,
            'ago_1m_high_class' => '',
            'ago_1m_low' => 0,
            'prev_ago_1m_low' => 0,
            'ago_1m_low_class' => '',
            'ago_3m_change' => 0,
            'ago_3m_high' => 0,
            'prev_ago_3m_high' => 0,
            'ago_3m_high_class' => '',
            'ago_3m_low' => 0,
            'prev_ago_3m_low' => 0,
            'ago_3m_low_class' => '',
            'ago_6m_change' => 0,
            'ago_6m_high' => 0,
            'prev_ago_6m_high' => 0,
            'ago_6m_high_class' => '',
            'ago_6m_low' => 0,
            'prev_ago_6m_low' => 0,
            'ago_6m_low_class' => '',
            'ago_1y_change' => 0,
            'ago_1y_high' => 0,
            'prev_ago_1y_high' => 0,
            'ago_1y_high_class' => '',
            'ago_1y_low' => 0,
            'prev_ago_1y_low' => 0,
            'ago_1y_low_class' => '',
        ];

        foreach ($averageData as $dataKey => $data){

            if ( $dataKey == "display_title" || substr($dataKey, -5) === "class" )
                continue;

            foreach ($marketDataArray as $itemData){
                $averageData[$dataKey] += $itemData[$dataKey];
            }


            $averageData[$dataKey] = $averageData[$dataKey]/$market_count;

            $averageData[$dataKey] = number_format($averageData[$dataKey], 2, '.', '');

            if ( substr($dataKey, 0, 5) === "prev_" ){
                $current_value_datakey = str_replace("prev_", "", $dataKey);

                $data_class_key = $current_value_datakey."_class";
                if ( $averageData[$current_value_datakey] > $averageData[$dataKey] ){
                    $averageData[$data_class_key] = "elementGreenToFadeInAndOut";
                }
                if ( $averageData[$current_value_datakey] < $averageData[$dataKey] ){
                    $averageData[$data_class_key] = "elementRedToFadeInAndOut";
                }
            }

        }


        return $averageData;

    }

}