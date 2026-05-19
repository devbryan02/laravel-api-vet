<?php

namespace App\Features\User\Services;

use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function findByRole(RoleName $role): Collection
    {
        return User::with("roles")
            ->withRole($role)
            ->orderBy("name")
            ->get();
    }

    public function createVeterinarian(array $data): User
    {
        return $this->createWithRole($data, RoleName::VETERINARIAN);
    }

    public function createOwner(array $data): User
    {
        $data["password"] = $data["dni"];

        return $this->createWithRole($data, RoleName::OWNER);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->load("roles");
    }

    public function delete(User $user): bool
    {
        return (bool) DB::transaction(function () use ($user) {
            $user->roles()->detach();

            return $user->delete();
        });
    }

    public function assertCanManage(?User $actor, RoleName $managedRole): void
    {
        $allowed = match ($managedRole) {
            RoleName::VETERINARIAN => $actor?->hasRole(RoleName::ADMIN) === true,
            RoleName::OWNER => $actor?->hasRole(RoleName::VETERINARIAN) === true || $actor?->hasRole(RoleName::ADMIN) === true,
            RoleName::ADMIN => false,
        };

        if (! $allowed) {
            throw new AuthorizationException("No tienes permisos para gestionar usuarios con este rol.");
        }
    }

    public function managedRoleFor(User $user): RoleName
    {
        $user->loadMissing("roles");
        $roleName = $user->primaryRole();

        return RoleName::tryFrom($roleName ?? "")
            ?? throw new AuthorizationException("El usuario no tiene un rol gestionable.");
    }

    private function createWithRole(array $data, RoleName $roleName): User
    {
        return DB::transaction(function () use ($data, $roleName) {
            $role = Role::firstOrCreate(
                ["name" => $roleName->value],
                ["description" => $this->descriptionFor($roleName)]
            );

            $user = User::create($data);
            $user->roles()->sync([$role->id]);

            return $user->load("roles");
        });
    }

    private function descriptionFor(RoleName $roleName): string
    {
        return match ($roleName) {
            RoleName::ADMIN => "Administrador del sistema",
            RoleName::VETERINARIAN => "Veterinario",
            RoleName::OWNER => "Dueño de mascota",
        };
    }
}
