@extends('monet::layouts.blank')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-end py-4">
            <ul class="flex items-center space-x-4">
                @auth
                    @can('view admin')
                        <li>
                            <a
                                href="{{route('filament.pages.dashboard')}}"
                                class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                            >
                                Administration
                            </a>
                        </li>
                    @endcan
                    <li>
                        <form
                            action="#"
                            method="post"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                            >
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a
                            href="#"
                            class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Login
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Register
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="w-full py-36 text-center">
            <div>
                <h1
                    class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-primary-700 drop-shadow-lg"
                >
                    Monet
                </h1>
            </div>

            <div class="flex items-center justify-center py-12">
                <ul class="flex items-center justify-center space-x-4">
                    <li>
                        <a
                            href="https://github.com/monet-php/framework"
                            target="_blank"
                            class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Documentation
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://github.com/monet-php/framework"
                            target="_blank"
                            class="font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            GitHub
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
