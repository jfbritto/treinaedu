@props(['videoUrl', 'provider', 'trainingId', 'initialProgress' => 0])

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

<div x-data="videoPlayer(@js($trainingId), @js($provider), @js($videoId), @js((int) $initialProgress))">
    @if($provider === 'youtube')
        <div class="relative aspect-video rounded-xl overflow-hidden bg-black">
            <div id="yt-player-{{ $trainingId }}" class="absolute inset-0 w-full h-full"></div>
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
</div>

@push('scripts')
<script>
    function videoPlayer(trainingId, provider, videoId, initialProgress) {
        return {
            progress: initialProgress,
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
                if (typeof YT !== 'undefined' && YT.Player) {
                    self.createYTPlayer(trainingId, videoId);
                } else {
                    const prev = window.onYouTubeIframeAPIReady;
                    window.onYouTubeIframeAPIReady = () => {
                        if (prev) prev();
                        self.createYTPlayer(trainingId, videoId);
                    };
                    if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
                        const tag = document.createElement('script');
                        tag.src = 'https://www.youtube.com/iframe_api';
                        document.head.appendChild(tag);
                    }
                }
            },

            createYTPlayer(trainingId, videoId) {
                const self = this;
                self.player = new YT.Player('yt-player-' + trainingId, {
                    videoId: videoId,
                    width: '100%',
                    height: '100%',
                    events: {
                        onStateChange(event) {
                            if (event.data === YT.PlayerState.PLAYING) {
                                self.checkYTProgress(); // imediato ao play/seek
                                clearInterval(self.interval);
                                self.interval = setInterval(() => self.checkYTProgress(), 3000);
                            } else {
                                clearInterval(self.interval);
                                // captura seek com pause, e fim do vídeo
                                if (event.data === YT.PlayerState.ENDED) {
                                    if (self.progress < 100) self.updateProgress(100);
                                } else if (event.data === YT.PlayerState.PAUSED ||
                                    event.data === YT.PlayerState.BUFFERING) {
                                    self.checkYTProgress();
                                }
                            }
                        }
                    }
                });
            },

            checkYTProgress() {
                if (!this.player) return;
                const duration = this.player.getDuration();
                const current  = this.player.getCurrentTime();
                if (duration > 0) {
                    let pct = Math.floor((current / duration) * 100);
                    if (pct >= 97) pct = 100;
                    if (pct > this.progress) {
                        this.updateProgress(pct);
                    }
                }
            },

            initVimeo(trainingId, videoId) {
                const iframe = document.getElementById('vimeo-player-' + trainingId);
                const self = this;
                window.addEventListener('message', (event) => {
                    if (event.origin !== 'https://player.vimeo.com') return;
                    let data;
                    try { data = JSON.parse(event.data); } catch (e) { return; }
                    // playProgress (playing) ou seek (arrastar barra)
                    if ((data.event === 'playProgress' || data.event === 'timeupdate' || data.event === 'seek') && data.data?.percent !== undefined) {
                        const pct = Math.floor(data.data.percent * 100);
                        if (pct > self.progress && (pct % 5 === 0 || pct >= 90)) {
                            self.updateProgress(pct);
                        }
                    }
                });
                ['playProgress', 'seek'].forEach(evt => {
                    iframe.contentWindow.postMessage(
                        JSON.stringify({ method: 'addEventListener', value: evt }),
                        'https://player.vimeo.com'
                    );
                });
            },

            updateProgress(pct) {
                this.progress = pct;
                // Notify parent Alpine components listening on window
                window.dispatchEvent(new CustomEvent('video-progress', { detail: { percent: pct } }));
                this.sendProgress(pct);
            },

            sendProgress(pct) {
                fetch('/api/lesson-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lesson_id: trainingId, progress_percent: pct })
                });
            }
        }
    }
</script>
@endpush
