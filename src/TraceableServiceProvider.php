<?php

namespace MJM\Traceable;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class TraceableServiceProvider extends ServiceProvider {

    public function register()
    {
        Blueprint::macro('traceable', function() {
            $this->integer('created_by')->unsigned()->nullable();
            $this->integer('updated_by')->unsigned()->nullable();
        }); 
        Blueprint::macro('softDeletesTraceable', function() {
            $this->integer('deleted_by')->unsigned()->nullable();
        });
        Blueprint::macro('dropTraceable', function() {
            $this->dropColumn('created_by');
            $this->dropColumn('updated_by');
        });
        Blueprint::macro('dropSoftDeletesTraceable', function() {
            $this->dropColumn('deleted_by');
        });
    }
}
