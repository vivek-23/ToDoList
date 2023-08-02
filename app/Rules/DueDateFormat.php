<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DateTime;

class DueDateFormat implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){
        $d = DateTime::createFromFormat('Y/m/d', $value);
        $current = DateTime::createFromFormat('Y/m/d', date("Y/m/d"));
        return $d && $d->format('Y/m/d') == $value && $d >= $current;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(){
        return 'The due date must be a valid future date and must follow the format of YYYY/mm/dd.';
    }
}
