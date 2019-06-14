<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UpdatePasswordController extends Controller
{
    /**
     * Update the password for the user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update()
    {
        // Validate the new password length...

        echo Hash::make('Secret123admin');
    }
}