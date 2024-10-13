<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandleFile;          
use App\Traits\HasPermissions;          

class UserController extends Controller
{
    use HandleFile;

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
}
