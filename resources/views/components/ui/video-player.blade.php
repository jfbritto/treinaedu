@props(['videoUrl', 'provider', 'trainingId'])

@php
    $videoId = '';
    if ($provider === 'youtube') {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
        $videoId = $matches[1] ?? '';
    } elseif ($provider === 'vimeo') {
        preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches);
        $videoId = $matches[1] ?? '';
    }
@endphp

<div x-data="videoPlayer({{ $trainingId }}, '{{ $provider }}', '{{ $videoId }}')" class="space-y-4">

    @if($provider === 'youtube')
        <div class="relative aspect-video rounded-xl overflow-hidden bg-black">
            <div id="yt-player-{{ $trainingId }}"></div>
        </div>
    @elseif($provider === 'vimeo')
        <div class="relative aspect-video rounded-xl overflow-hidden bg-black">
            <iframe
                id="vimeo-player-{{ $trainingId }}"
                src="https://player.vimeo.com/video/{{ $videoId }}?api=1"
                class="w-full h-full"
                frameborder="0"
                allow="autoplay; fullscreen"
                allowfullscreen>
            </iframe>
        </div>
    @endif

    <div x-show="progress >= 90" class="text-center" x-cloak>
        <form method="POST" action="{{ route('employee.trainings.complete', $trainingId) }}">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg">
                Marcar como Concluído
            </button>
        </form>
    </div>

    <div class="text-center text-sm text-gray-500">
        Progresso: <span x-text="progress"></span>%
    </div>
</div>

@push('scripts')
<script>
    function videoPlayer(trainingId, provider, videoId) {
        return {
            progress: 0,
            interval: null,
            player: null,

            init() {
                if (provider === 'youtube') {
                    this.initYouTube(trainingId, videoId);
                } else if (provider === 'vimeo') {
                    this.initVimeo(trainingId, videoId);
                }
            },

            initYouTube(trainingId, videoId) {
                const self = this;
                if (typeof YT !== 'undefined') {
                    self.createYTPlayer(trainingId, videoId);
                } else {
                    window.onYouTubeIframeAPIReady = () => self.createYTPlayer(trainingId, videoId);
                    const tag = document.createElement('script');
                    tag.src = 'https://www.youtube.com/iframe_api';
                    document.head.appendChild(tag);
                }
            },

            createYTPlayer(trainingId, videoId) {
                const self = this;
                self.player = new YT.Player('yt-player-' + trainingId, {
                    videoId: videoId,
                    events: {
                        onStateChange(event) {
                            if (event.data === YT.PlayerState.PLAYING) {
                                self.interval = setInterval(() => self.checkYTProgress(), 5000);
                            } else {
                                clearInterval(self.interval);
                            }
                        }
                    }
                });
            },

            checkYTProgress() {
                if (!this.player) return;
                const duration = this.player.getDuration();
                const current = this.player.getCurrentTime();
                if (duration > 0) {
                    const pct = Math.floor((current / duration) * 100);
                    if (pct > this.progress) {
                        this.progress = pct;
                        this.sendProgress(pct);
                    }
                }
            },

            initVimeo(trainingId, videoId) {
                const iframe = document.getElementById('vimeo-player-' + trainingId);
                const self = this;
                window.addEventListener('message', (event) => {
                    if (event.origin !== 'https://player.vimeo.com') return;
                    const data = JSON.parse(event.data);
                    if (data.event === 'playProgress' || data.event === 'timeupdate') {
                        const pct = Math.floor(data.data.percent * 100);
                        if (pct > self.progress) {
                            self.progress = pct;
                            self.sendProgress(pct);
                        }
                    }
                });
                iframe.contentWindow.postMessage(JSON.stringify({ method: 'addEventListener', value: 'playProgress' }), 'https://player.vimeo.com');
            },

            sendProgress(pct) {
                fetch('/api/training-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ training_id: trainingId, progress_percent: pct })
                });
            }
        }
    }
</script>
@endpush
