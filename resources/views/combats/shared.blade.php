@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-100 leading-tight">
        {{ $combat->name }} <span class="text-sm text-gray-400">(Shared View)</span>
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('combats._shared_board')
        </div>
    </div>

    @push('scripts')
        <script type="module">
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Echo) {
                    const channel = window.Echo.channel('combat.{{ $combat->id }}');

                    channel.listen('.combat.updated', (e) => {
                        // If user is authenticated, we might need to redirect to main board
                        const isAuth = @json(auth()->check());

                        fetch(window.location.href, {
                            headers: {
                                'X-Partial-Board': 'true'
                            }
                        })
                            .then(response => {
                                // If the server returned a redirect (e.g. status 302/200 but different URL)
                                if (response.redirected && isAuth) {
                                    window.location.href = response.url;
                                    return;
                                }
                                return response.text();
                            })
                            .then(html => {
                                if (!html) return;
                                const board = document.getElementById('shared-combat-board');
                                if (board) {
                                    board.outerHTML = html;
                                }
                            });
                    });
                }
            });
        </script>
    @endpush
    </div>
    </div>
@endsection