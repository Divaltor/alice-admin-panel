<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Auth;
use Azate\LaravelTelegramLoginAuth\Contracts\Telegram\NotAllRequiredAttributesException;
use Azate\LaravelTelegramLoginAuth\Contracts\Validation\Rules\ResponseOutdatedException;
use Azate\LaravelTelegramLoginAuth\Contracts\Validation\Rules\SignatureException;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Redirect;

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
        try {
            $telegramUser = $this->telegram->validateWithError($request);
            $user = $this->userRepository->findByTelegramId($telegramUser->getId());

            if (!$user) {
                $user = $this->userRepository->create([
                    'telegram_id' => $telegramUser->getId(),
                    'first_name' => $telegramUser->getFirstName(),
                    'last_name' => $telegramUser->getLastName(),
                    'username' => $telegramUser->getUsername()
                ]);
            }
            Auth::login($user, true);
            return Redirect::route(RouteServiceProvider::HOME);
        } catch (SignatureException $exception) {
            return Redirect::route('login')->withErrors(['Token is invalid']);
        } catch (ResponseOutdatedException $exception) {
            return Redirect::route('login')->withErrors(['Response is outdated']);
        } catch (\Exception $exception) {
            return Redirect::route('login')->withErrors(['Unexcepted error']);
        }
    }
}
