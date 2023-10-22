<?php
/**
 * Creator: MD.HARUN-UR-RASHID
 * Date: 25/01/2020
 * Website: laravelarticle.com
 */

namespace Haruncpi\LaravelLogReader;

class LaravelLogReader
{
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
        $files = glob(storage_path('logs/laravel-*.log'));
        $files = array_reverse($files);
        foreach ($files as $path) {
            $fileName = basename($path);
            preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
            $date = $dtMatch[0];
            array_push($dates, $date);
        }

        return $dates;
    }

    public function get()
    {

        $availableDates = $this->getLogFileDates();

        if (count($availableDates) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No log available'
            ]);
        }

        $configDate = $this->config['date'];
        if ($configDate == null) {
            $configDate = $availableDates[0];
        }

        if (!in_array($configDate, $availableDates)) {
            return response()->json([
                'success' => false,
                'message' => 'No log file found with selected date ' . $configDate
            ]);
        }

        $fileName = 'laravel-' . $configDate . '.log';
        $content = file_get_contents(storage_path('logs/' . $fileName));

        // splitting by regexp in order to get the whole message between 2 log entries
        $chars = preg_split('/\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):/i', $content, -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        // chunking - every chung will contain all needed data
        $matches = array_chunk($chars, 4, false);

        $logs = [];
        foreach ($matches as [$date, $env, $type, $message]) {
            $logs[] = [
                'timestamp' => $date,
                'env' => $env,
                'type' => $type,
                'message' => trim($message),
            ];
        }

        preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
        $date = $dtMatch[0];

        $data = [
            'available_log_dates' => $availableDates,
            'date' => $date,
            'filename' => $fileName,
            'logs' => $logs
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

}
