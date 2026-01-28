@extends('layouts.app')

@section('title', $combat->name)

@section('content')
    @include('combats._board')

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
                                const board = document.getElementById('combat-board');
                                if (board) {
                                    board.outerHTML = html;
                                }
                            });
                    });
                }
            });
        </script>
    @endpush
@endsection