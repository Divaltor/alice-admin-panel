<?php


namespace App\Repositories\Interfaces;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface UserRepositoryInterface
 * @package App\Repositories\Interfaces
 * @mixin EloquentRepositoryInterface
 * @method null|User find($id)
 * @method User create(array $attributes)
 */
interface UserRepositoryInterface
{
    public function all(): Collection;
    public function findByTelegramId($id): ?User;
}
