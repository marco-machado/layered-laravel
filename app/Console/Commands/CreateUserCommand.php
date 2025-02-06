<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create 
                          {--name= : The name of the user}
                          {--email= : The email of the user}
                          {--password= : The password for the user}';

    protected $description = 'Create a new user';

    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->option('name') ?: $this->askForName();
        $email = $this->option('email') ?: $this->askForEmail();
        $password = $this->option('password') ?: $this->askForPassword();

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        try {
            $user = $this->userService->create($data);
            $this->info("User created successfully! ID: {$user->id}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create user: {$e->getMessage()}");
            return 1;
        }
    }

    private function askForName(): string
    {
        return $this->ask('What is the user\'s name?');
    }

    private function askForEmail(): string
    {
        return $this->ask('What is the user\'s email?');
    }

    private function askForPassword(): string
    {
        return $this->secret('What is the user\'s password?');
    }
} 