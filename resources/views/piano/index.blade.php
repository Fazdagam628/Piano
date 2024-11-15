<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Piano Game</title>
</head>
<style>
    body {
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #203037;
        background-image: linear-gradient(#000000, #203037, #000000);
    }

    button {
        border-style: solid;
        border-color: #9f7e7e;
        border-radius: 10px;
        background-color: bisque;
        padding: 5px;
    }

    .p {
        color: whitesmoke;
    }

    .track {
        position: absolute;
        bottom: 5px;
        width: 100%;
        height: calc(100% - 100px);
        border: 4px;
    }

    .note {
        position: absolute;
        width: 80px;
        height: 10px;
        background: white;
        border-radius: 8px;
    }

    .key-container {
        position: absolute;
        bottom: 0;
        width: 320px;
        display: flex;
        justify-content: center;
        align-items: center;
        left: 50%;
        transform: translateX(-50%);
    }

    .key {
        width: 80px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        color: white;
        cursor: pointer;
        border: 2px #0048ff63;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        margin: 0;
    }

    .key-d {
        background: #0064ff36;
    }

    .key-f {
        background: #0093ff73;
    }

    .key-j {
        background: #0064ff36;
    }

    .key-k {
        background: #0093ff73;
    }

    .lane-divider {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #0048ff63;
    }

    .lane-d {
        left: calc(50% - 160px);
    }

    .lane-f {
        left: calc(50% - 80px);
    }

    .lane-j {
        left: calc(50% + 0px);
    }

    .lane-k {
        left: calc(50% + 80px);
    }

    .lane-l {
        left: calc(50% + 160px);
    }

    #score {
        position: absolute;
        top: 20px;
        left: 0;
        color: ghostwhite;
        font: 14px;
    }

    #timer {
        position: absolute;
        top: 20px;
        right: 0;
        color: whitesmoke;
        font-size: 14px;
    }

    #game-over {
        position: absolute;
        top: 50%;
        left: 491px;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.8);
        padding: 20px;
        border-radius: 10px;
        color: white;
        text-align: center;
        display: none;
    }

    #game-container {
        width: 1080px;
        height: 600px;
        position: relative;
        overflow: hidden;
    }
</style>

<body>
    <div id="game-container">
        <button id="start-button">START</button>
        <div id="score">
            <p>Score: 0.00%</p>
        </div>
        <div id="timer">
            <p>Time: 02:00</p>
        </div>
        <div class="track">
            <div class="lane-divider lane-d"></div>
            <div class="lane-divider lane-f"></div>
            <div class="lane-divider lane-j"></div>
            <div class="lane-divider lane-k"></div>
            <div class="lane-divider lane-l"></div>
        </div>
        <div class="key-container">
            <div class="key key-d" data-key="D">D</div>
            <div class="key key-f" data-key="F">F</div>
            <div class="key key-j" data-key="J">J</div>
            <div class="key key-k" data-key="K">K</div>
        </div>
        <div id="game-over">
            <h2>Game Over</h2>
            <p>Final Score: <span id="final-score">0.00%</span></p>
            <button onclick="location.reload()">Play Again</button>
        </div>
        <audio data-key="68" src="/audio/awesome-house-kick-98685.mp3"></audio>
        <audio data-key="70" src="/audio/awesome-house-kick-98685.mp3"></audio>
        <audio data-key="74" src="/audio/awesome-house-kick-98685.mp3"></audio>
        <audio data-key="75" src="/audio/awesome-house-kick-98685.mp3"></audio>
        <audio id="background-music" src="/audio/audio.mp3" loop></audio>
    </div>
</body>
<script>
    class Piano {
        constructor() {
            this.score = 0;
            this.totalNotes = 0;
            this.hitNotes = 0;
            this.gameTime = 270;
            this.isPlaying = false;
            this.track = document.querySelector('.track');
            this.scoreElement = document.getElementById('score');
            this.timerElement = document.getElementById('timer');
            this.gameOverElement = document.getElementById('game-over');

            this.noteSpeed = 2;
            this.notes = [];

            this.backgroundMusic = document.getElementById('background-music');

            this.init();
        }

        init() {
            document.addEventListener('keydown', (e) => this.handleKeyPress(e));
            this.startBackgroundMusic();
            //start game loop
            this.lastTime = performance.now();
            this.gameLoop();
            //start spawning note
            this.spawnNotes();
            this.isPlaying = true;
        }

        startBackgroundMusic() {
            this.backgroundMusic.currentTime = 0;//mulai dari awal
            this.backgroundMusic.play();
        }

        spawnNotes() {
            const lanes = ['D', 'F', 'J', 'K'];
            const spawnInterval = 1000; //1000 per 1 detik

            this.spawnInterval = setInterval(() => {
                if (!this.isPlaying) return;

                const lane = lanes[Math.floor(Math.random() * lanes.length)];
                this.createNote(lane);
            }, spawnInterval);
        }

        createNote(lane) {
            const note = document.createElement('div');
            note.className = 'note';
            note.dataset.lane = lane;

            const lanePosition = {
                'D': 'calc(50% - 160px)',
                'F': 'calc(50% - 80px)',
                'J': 'calc(50% + 0px)',
                'K': 'calc(50% + 80px)',
            };

            note.style.left = lanePosition[lane];
            note.style.top = '-20px';

            this.track.appendChild(note);
            this.notes.push({
                element: note,
                position: 0,
                lane: lane
            });
            this.totalNotes++;
        }
        handleKeyPress(e) {
            const audio = document.querySelector(`audio[data-key="${e.keyCode}"]`);
            const key = e.key.toUpperCase();
            if (!['D', 'F', 'J', 'K'].includes(key)) return;

            audio.currentTime = 0;
            audio.play();
            const keyElement = document.querySelector(`.key-${key.toLowerCase()}`);
            keyElement.style.transform = 'scale(0.9)';
            setTimeout(() => keyElement.style.transform = 'scale(1)', 100);

            const hitWindow = 25;
            const targetY = 450;

            for (let note of this.notes) {
                if (note.lane === key &&
                    Math.abs(note.position - targetY) < hitWindow) {
                    this.hitNotes++;
                    note.element.remove();
                    this.notes = this.notes.filter(n => n !== note);
                    this.updateScore();
                    break;
                }
            }
        }
        updateScore() {
            this.score = [this.hitNotes / this.totalNotes] * 100;
            this.scoreElement.textContent = `Score: ${this.score.toFixed(2)}%`;
        }

        gameLoop() {
            const currentTime = performance.now();
            const deltaTime = (currentTime - this.lastTime) / 1000;
            this.lastTime = currentTime;

            if (this.isPlaying) {
                this.gameTime -= deltaTime;
                if (this.gameTime <= 0) {
                    this.gameOver();
                } else {
                    this.updateTimer();
                    this.updateNotes(deltaTime);
                }
            }
            requestAnimationFrame(() => this.gameLoop());
        }
        updateTimer() {
            const minutes = Math.floor(this.gameTime / 60);
            const seconds = Math.floor(this.gameTime % 60);
            this.timerElement.textContent = `Time: ${minutes.toString().padStart(2, '0')}: ${seconds.toString().padStart(2, '0')}`;
        }
        stopBackgroundMusic() {
            this.backgroundMusic.pause();
            this.backgroundMusic.currentTime = 0;
        }
        gameOver() {
            this.isPlaying = false;
            clearInterval(this.spawnInterval);

            this.stopBackgroundMusic();

            document.getElementById('final-score').textContent = this.score.toFixed(2) + '%';
            this.gameOverElement.style.display = 'block';

            this.notes.array.forEach(note => note.element.remove());
            this.notes = [];
        }
        updateNotes(deltaTime) {
            const noteSpeed = 300; //pixel per seconds

            for (let i = this.notes.length - 1; i >= 0; i--) {
                const note = this.notes[i];
                note.position += noteSpeed * deltaTime;
                note.element.style.top = note.position + 'px';

                if (note.position > 600) {
                    note.element.remove();
                    this.notes.splice(i, 1);
                    this.updateScore();
                }
            }
        }
    }
    window.addEventListener('load', () => {
        const startButton = document.getElementById('start-button');
        const gameContainer = document.getElementById('game-container');
        const backgroundMusic = document.getElementById('background-music');

        startButton.addEventListener('click', () => {
            startButton.style.display = 'none';
            backgroundMusic.play();
            new Piano();
        })
    })
</script>

</html>