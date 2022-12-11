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
 
   protected $showDebug = false;
 
    private function getAllModels($path)
    {
        // https://www.itsolutionstuff.com/post/how-to-get-all-models-in-laravelexample.html
        $modelList = [];
        
        $results = scandir($path);
        if($this->showDebug)
            echo("\nPath scanned: ".$path."\n");
 
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $path ."/" . $result;
            if($this->showDebug)
                echo("\nFile: ".$filename);
  
            if (is_dir($filename)) {
                $modelList = array_merge($modelList, $this->getAllModels($filename));
            }else{
                $modelList[] = substr($filename,0,-4);
            }
        }
  
        return $modelList;
    }

    public function handle()
    {
        $path = app_path() . "/Models"; //TODO: to add like parameter
        $modelClasses = $this->getAllModels($path);

        $modelClasses = array_map(fn($value) :
            string => str_replace("/","\\",str_replace(app_path(),"App",$value)) , $modelClasses
        );


        foreach($modelClasses as $model)
        {
            if($this->showDebug)
                echo($model."\n");
            
            $loadModel = new $model;
            $fillable = $loadModel->getFillable();

            // https://stackoverflow.com/questions/37157270/how-to-select-all-column-name-from-a-table-in-laravel
            //echo DB::getSchemaBuilder()->getColumnListing($loadModel->getTable());
            $tableColumn = Schema::getColumnListing($loadModel->getTable());
            foreach($fillable as $fil)
                if (!in_array($fil,$tableColumn))
                    echo "\n Column ".$fil." not exist inside model: ".$model;

        }
        echo "\n";
    }
}
