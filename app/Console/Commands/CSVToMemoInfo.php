<?php

namespace App\Console\Commands;

use App\Models\Issuances;
use App\Models\Memo;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class CSVToMemoInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csvtomemo {filepath}';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load CSV source into memo circulars table';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('filepath');

        if (!file_exists($path)) {
            echo "File not found.";
            return 1;
        }

        $file = fopen($path, 'r');
        $hasHeader = true; // Set this to true if your CSV file has a header row
        $firstRow = true;

        while (($data = fgetcsv($file)) !== false) {
            if ($firstRow && $hasHeader) {
                // Skip the first row if it's a header row
                $firstRow = false;
                continue;
            }
            if (empty(array_filter($data))) {
                continue;
            }

            // Ensure the row has enough columns
            if (count($data) < 7) {
                echo "Skipping row: " . implode(',', $data) . " - Insufficient data\n";
                continue;
            }
            $date = $data[4];
            $mysql_date = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
            $source = [
                'title' => $data[1],
                'reference_no' => $data[2],
                'url_link' => $data[5],
                'keyword' => $data[6],
                'date' => $data[4] == '0000-00-00' ? null : $mysql_date,
                'type' => "Memo Circulars",
                'responsible_office' => $data[3] == '' ? null : $data[3]
            ];

                try {
                    $issuance = Issuances::create([
                        'title' => $source['title'],
                        'type' => $source['type'],
                        'reference_no' => $source['reference_no'],
                        'url_link' => $source['url_link'],
                        'date' => $source['date'],
                        'keyword' => $source['keyword'],
                    ]);

                    $memo = Memo::create([
                        'issuance_id' => $issuance->id,
                        'responsible_office' => $source['responsible_office'],

                    ]);

                    echo "Created Issuance with ID: $issuance->id and Memo with ID: $memo->id\n";
                } catch (QueryException $ex) {
                    echo $ex->getMessage() . "\n";
                }

        }

        fclose($file);

        return 0;
    }
}
