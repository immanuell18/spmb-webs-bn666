@props(['roles'])

@php
    $userRole = auth()->user()->role ?? null;
    $allowedRoles = is_array($roles) ? $roles : explode(',', $roles);
    $hasAccess = $userRole && in_array($userRole, $allowedRoles);
@endphp

@if($hasAccess)
    {{ $slot }}
@endif