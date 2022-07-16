<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Resources\LiveNewsResource;

class LiveNewsController extends BaseController
{
    public function live_news(Request $request)
    {
        $queryString = http_build_query([
            'access_key' => '18cbb8ef7da6e4963d0998a2e9febbed',
            'keywords' => 'money', // the word "wolf" will be
            'categories' => 'business',
            'sort' => 'popularity',
            'country'=>'africa',
          ]);

          $ch = curl_init(sprintf('%s?%s', 'http://api.mediastack.com/v1/news', $queryString));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $json = curl_exec($ch);

          curl_close($ch);

          $apiResult = json_decode($json, true);

          //print_r($apiResult);

          return $this->SendResponse(new LiveNewsResource($apiResult),'News successfully retrieved');
    }

    public function trendingNews(Request $request)
    {
        {
            $queryString = http_build_query([
                'access_key' => '18cbb8ef7da6e4963d0998a2e9febbed',
                'keywords' => 'money', // the word "wolf" will be
                'categories' => 'business',
                'sort' => 'popularity',
                'country'=>'africa',
              ]);

              $ch = curl_init(sprintf('%s?%s', 'http://api.mediastack.com/v1/news', $queryString));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

              $json = curl_exec($ch);

              curl_close($ch);

              $apiResult = json_decode($json, true);

              //print_r($apiResult);

              return $this->SendResponse(new LiveNewsResource($apiResult),'News successfully retrieved');
        }
    }

    public function topics(Request $request)
    {
        {
            $queryString = http_build_query([
                'access_key' => '18cbb8ef7da6e4963d0998a2e9febbed',
                'keywords' => 'money', // the word "wolf" will be
                'categories' => $request->topics,
                'sort' => 'popularity',
                'country'=>'africa',
              ]);

              $ch = curl_init(sprintf('%s?%s', 'http://api.mediastack.com/v1/news', $queryString));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

              $json = curl_exec($ch);

              curl_close($ch);

              $apiResult = json_decode($json, true);

              //print_r($apiResult);

              return $this->SendResponse(new LiveNewsResource($apiResult),'News successfully retrieved');
        }
    }
}
