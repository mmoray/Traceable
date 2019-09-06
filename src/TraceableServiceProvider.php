<?php

namespace MJM\Traceable;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use MJM\Traceable\Configuration\Constants;

class TraceableServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Configuration/traceable.php' => config_path('traceable.php')
        ]);
    }

    public function register()
    {
        $userClass = config(Constants::USER_CLASS_REFERENCE);
        $table = $primaryKey = null;
        if ($userClass) {
            $user = new $userClass;
            $table = $user->getTable();
            $primaryKey = $user->getKeyName();
        }
        Blueprint::macro('traceable', function() use ($table, $primaryKey) {
            $this->integer('created_by')->unsigned()->nullable();
            $this->integer('updated_by')->unsigned()->nullable();
            if ($table && $primaryKey) {
                Schema::table($table, function(Blueprint $bp) use ($primaryKey) {
                    $bp->increments($primaryKey)->change();
                });
                $this->foreign('created_by', sprintf('fk_%s_created_by_%s_%s', $this->getTable(), $table, $primaryKey))->references($primaryKey)->on($table);
                $this->foreign('updated_by', sprintf('fk_%s_updated_by_%s_%s', $this->getTable(), $table, $primaryKey))->references($primaryKey)->on($table);
            }
        }); 
        Blueprint::macro('softDeletesTraceable', function() use ($table, $primaryKey) {
            $this->integer('deleted_by')->unsigned()->nullable();
            if ($table && $primaryKey) {
                Schema::table($table, function(Blueprint $bp) use ($primaryKey) {
                    $bp->increments($primaryKey)->change();
                });
                $this->foreign('deleted_by', sprintf('fk_%s_deleted_by_%s_%s', $this->getTable(), $table, $primaryKey))->references($primaryKey)->on($table);
            }
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
