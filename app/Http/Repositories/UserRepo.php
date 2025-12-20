<?php

// user/auth repository
namespace App\Http\Repositories;
use App\Models\User;

class UserRepo
{
    protected $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function createUser($userData)
    {
        return $this->userModel->create($userData);
    }

    public function findUserByEmail($email)
    {
        return $this->userModel->where('email', $email)->first();
    }
}