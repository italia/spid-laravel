<?php

namespace Italia\SPIDAuth\Tests;

use Illuminate\Support\Facades\Artisan;

use Orchestra\Testbench\TestCase;

class CommandExampleTest extends TestCase
{
    
    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }

    protected function setUp()
    {
        parent::setUp();
        
        $this->app->setBasePath(__DIR__.'/test_app');
        mkdir(__DIR__.'/test_app/app/Http/Controllers', 0755, true);
        mkdir(__DIR__.'/test_app/routes', 0755, true);
        touch(__DIR__.'/test_app/routes/web.php');
        copy(__DIR__.'/fakes/composer.json', __DIR__.'/test_app/composer.json');
    }
    
    protected function tearDown()
    {
        $this->rrmdir(__DIR__.'/test_app');
    }
    
    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        $this->rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object); 
                }
            }
            rmdir($dir);
        } 
    }
    
    public function testHandle()
    {
        $returnCode = Artisan::call('spid-auth:example');
        $this->assertEquals($returnCode, 0);
    }
    
    public function testOverwrite()
    {
      $returnCode = Artisan::call('spid-auth:example', ['-n' => 1]);
      $returnCode = Artisan::call('spid-auth:example', ['-n' => 1]);
      $this->assertEquals($returnCode, 0);
    }
}