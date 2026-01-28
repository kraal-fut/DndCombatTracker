@extends('layouts.app')

@section('title', $combat->name)

@section('content')
    @include('combats._board')

    @push('scripts')
        <script type="module">
            window.copyShareLink = function() {
                const copyText = document.getElementById("share-link");
                if (!copyText) return;
                
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                
                const showToast = () => {
                    let toast = document.getElementById('copy-toast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'copy-toast';
                        toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50';
                        toast.innerText = 'Link copied to clipboard!';
                        document.body.appendChild(toast);
                    }
                    
                    // Force a reflow to ensure the transition works
                    toast.classList.remove('opacity-0');
                    
                    setTimeout(() => {
                        toast.classList.add('opacity-0');
                    }, 2000);
                };

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(copyText.value).then(showToast);
                } else {
                    document.execCommand('copy');
                    showToast();
                }
            }

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