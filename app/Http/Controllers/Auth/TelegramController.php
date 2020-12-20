<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Auth;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TelegramController extends Controller
{
    protected TelegramLoginAuth $telegram;
    protected UserRepositoryInterface $userRepository;

    public function __construct(TelegramLoginAuth $telegram, UserRepositoryInterface $userRepository)
    {
        $this->telegram = $telegram;
        $this->userRepository = $userRepository;
    }

    public function handleTelegramCallback(Request $request): \Illuminate\Http\RedirectResponse
    {
        if ($telegramUser = $this->telegram->validate($request)) {
            $user = $this->userRepository->find($telegramUser->getId());

            if (!$user) {
                $user = $this->userRepository->create([
                    'telegram_id' => $telegramUser->getId(),
                    'first_name' => $telegramUser->getFirstName(),
                    'last_name' => $telegramUser->getLastName(),
                    'username' => $telegramUser->getUsername()
                ]);
            }
            Auth::login($user, true);
        }

        return Redirect::route('dashboard');
    }
}
