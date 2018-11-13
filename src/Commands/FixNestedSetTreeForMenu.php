<?php
namespace GreenBar\MenuBuilder\Commands;

use Illuminate\Console\Command;

class FixNestedSetTreeForMenu extends Command
{
    /**
     * The environment this command is running in
     *
     * @var boolean
     */
    private $app_env = 'production';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menus:fix-tree {menu_id}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ReIndexes the nested set tree for the specified menu';

    /**
     * Should we enable debug messaging?
     *
     * @var boolean
     */
    protected $debug_messaging = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // TODO: DISCUSS 2018-11-13: Do this???
        $this->app_env = strtolower(env('APP_ENV', 'production'));

        // TODO: 2018-11-23: Change this to check config values instead of env values
        $this->debug_messaging = ($this->app_env !== 'production') ? true : false;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $menu_class = config('menu_builder.models.menu');
        $menu_item_class = config('menu_builder.models.menu_item');

        $menu_id = (int) $this->argument('menu_id');

        if (!$menu_id) {
            return $this->error('Must enter a integer for a menu id');
        }

        $menu = $menu_class::find($menu_id);

        if (!$menu) {
            return $this->error('Could not find the the specified menu.');
        }

        try {
            $menu_item_class::scoped([ 'menu_id' => $menu_id ])->fixTree(); 
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
        
        return 0;
    }
}
