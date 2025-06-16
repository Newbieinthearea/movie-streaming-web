@extends('layouts.public')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Edit Profile</h1>
        <a href="{{ route('profile.show') }}" class="text-sm text-purple-400 hover:underline">
            &larr; Back to Profile
        </a>
    </div>

    <div class="space-y-6">
        <div class="p-6 sm:p-8 bg-gray-800/50 rounded-lg">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-6 sm:p-8 bg-gray-800/50 rounded-lg">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-6 sm:p-8 bg-gray-800/50 rounded-lg">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection