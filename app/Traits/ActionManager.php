<?php

namespace App\Traits;

use App\Models\ActionLog;

trait ActionManager
{
    /**
     * Define the morphMany relationship for action logs.
     */
    public function actionLogs()
    {
        return $this->morphMany(ActionLog::class, 'entity');
    }

    /**
     * Define the morphMany relationship for secondary entity action logs.
     */
    public function secondaryActionLogs()
    {
        return $this->morphMany(ActionLog::class, 'secondaryEntity');
    }

    /**
     * Define the morphMany relationship for tertiary entity action logs.
     */
    public function tertiaryActionLogs()
    {
        return $this->morphMany(ActionLog::class, 'tertiaryEntity');
    }


    /**
     * Get the default actions for the model.
     * This reads from the `$defaultActions` property defined in each model.
     */
    public static function getDefaultActions(): array
    {
        return property_exists(static::class, 'defaultActions') ? static::$defaultActions : [];
    }
}
