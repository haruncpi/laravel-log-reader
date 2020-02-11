<?php
/**
 * Creator: MD.HARUN-UR-RASHID
 * Date: 25/01/2020
 * Website: laravelarticle.com
 */
namespace Successdev\LaravelLogReader;

class LaravelLogReader
{
    protected $final = [];
    protected $config = [];


    public function __construct($config = [])
    {
        if (array_key_exists('date', $config)) {
            $this->config['date'] = $config['date'];
        } else {
            $this->config['date'] = null;
        }

    }


    public function getLogFileDates()
    {
        $dates = [];
        $dtMatch = [];
        $files = glob(storage_path('logs/*.log'));
        $log_files = array_reverse($files);

        foreach ($log_files as $path) {
            $fileName = basename($path);
            preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
            if (isset($dtMatch[0])) 
            {
                $date = $dtMatch[0];
                array_push($dates, $date);
            }
        }

        return $dates;
    }

    public function get()
    {

        $availableDates = $this->getLogFileDates();

        if (count($availableDates) == 0) {
            return response()->json([
                'success' => false,
                'message' => trans('LaravelLogReader::laravel-log-reader.no_log')
            ]);
        }

        $configDate = $this->config['date'];
        if ($configDate == null) {
            $configDate = $availableDates[0];
        }

        if (!in_array($configDate, $availableDates)) {
            return response()->json([
                'success' => false,
                'message' => trans('LaravelLogReader::laravel-log-reader.no_log_by_date') . $configDate
            ]);
        }


        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        $fileName = 'laravel-' . $configDate . '.log';
        $content = file_get_contents(storage_path('logs/' . $fileName));
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'timestamp' => $match['date'],
                'env' => $match['env'],
                'type' => $match['type'],
                'message' => trim($match['message'])
            ];
        }

        preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
        
        $date = '';
        if (isset($dtMatch[0])) 
        {
            $date = $dtMatch[0];
        }
        
        $data = [
            'available_log_dates' => $availableDates,
            'date' => $date,
            'filename' => $fileName,
            'logs' => $logs
        ];
        
        return response()->json(['success' => true, 'data' => $data]);
    }

}

