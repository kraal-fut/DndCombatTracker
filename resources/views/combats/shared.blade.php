@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-100 leading-tight">
        {{ $combat->name }} <span class="text-sm text-gray-400">(Shared View)</span>
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @include('combats._shared_board')
        </div>
    </div>

    @push('scripts')
        <script type="module">
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Echo) {
                    const channel = window.Echo.channel('combat.{{ $combat->id }}');

                    channel.listen('.combat.updated', (e) => {
                        fetch(window.location.href, {
                            headers: {
                                'X-Partial-Board': 'true'
                            }
                        })
                            .then(response => response.text())
                            .then(html => {
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