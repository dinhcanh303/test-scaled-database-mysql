<?php

namespace Database\Supports;

use Illuminate\Database\Schema\Blueprint;

class BlueprintExtended extends Blueprint
{
    function appendCommonFields()
    {
        $this->timestamp('created_at')->useCurrent();
        $this->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    }
}
