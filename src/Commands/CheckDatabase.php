<?php
 
namespace LucaRigutti\CheckmodeldatabaseLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
 
class CheckDatabase extends Command
{

    protected $signature = 'model:check';
 

    protected $description = 'Send a marketing email to a user';

    public function __construct() {
        parent::__construct();
    }
 
    private function getAllModels()
    {
        // https://www.itsolutionstuff.com/post/how-to-get-all-models-in-laravelexample.html
        $modelList = [];
        $path = app_path() . "/Models";
        $results = scandir($path);
 
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;
  
            if (is_dir($filename)) {
                $modelList = array_merge($modelList, getModels($filename));
            }else{
                $modelList[] = substr($filename,0,-4);
            }
        }
  
        return $modelList;
    }

    public function handle()
    {
        $modelClasses = $this->getAllModels();


        foreach($modelClasses as $model)
        {
            echo("\n".$model."\n");
            $class = "App\Models\\".$model;
            $loadModel = new $class;
            $fillable = $loadModel->getFillable();

            // https://stackoverflow.com/questions/37157270/how-to-select-all-column-name-from-a-table-in-laravel
            //echo DB::getSchemaBuilder()->getColumnListing($loadModel->getTable());
            $tableColumn = Schema::getColumnListing($loadModel->getTable());
            foreach($fillable as $fil)
                if (!in_array($fil,$tableColumn))
                    echo "\n Column ".$fil." not exist";

        }
    }
}