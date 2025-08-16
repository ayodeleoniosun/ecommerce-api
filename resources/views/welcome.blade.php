<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name', 'Ecommerce API') }} — Welcome</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b1020;
            --panel: #111833;
            --muted: #93a2c7;
            --text: #e8eeff;
            --brand: #6ea8fe;
            --brand-2: #8f73ff;
            --ok: #22c55e;
            --warn: #f59e0b;
            --danger: #ef4444;
            --ring: rgba(110, 168, 254, .35);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
            background: linear-gradient(180deg, #0b1020 0%, #0d1226 100%);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a {
            color: var(--brand);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 24px;
            align-items: center;
            padding: 56px 0 32px;
        }

        .card {
            background: radial-gradient(80% 120% at 100% 0%, rgba(143, 115, 255, .12), rgba(110, 168, 254, .08)), var(--panel);
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35), inset 0 1px 0 rgba(255, 255, 255, .05);
        }

        .pill {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(110, 168, 254, .12);
            border: 1px solid rgba(110, 168, 254, .25);
            color: var(--text);
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .02em;
        }

        .h1 {
            font-size: clamp(28px, 4.2vw, 46px);
            line-height: 1.1;
            margin: 16px 0 8px;
            font-weight: 800;
        }

        .lead {
            color: var(--muted);
            font-size: 15.5px;
            line-height: 1.65;
        }

        .cta {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .08);
            background: linear-gradient(180deg, #2a3b72, #1c2750);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 6px 16px rgba(110, 168, 254, .25);
        }

        .btn.secondary {
            background: #141b36;
            border-color: rgba(255, 255, 255, .12);
            box-shadow: none;
            color: var(--text);
        }

        .grid {
            display: grid;
            gap: 16px;
        }

        .grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .section {
            margin-top: 36px;
        }

        .section h2 {
            margin: 0 0 12px;
            font-size: 22px;
        }

        .list {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.7;
        }

        .kbd {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            background: #0e1430;
            border: 1px solid rgba(255, 255, 255, .08);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12.5px;
            color: #cfe4ff;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
        }

        .code {
            background: #0b0f22;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 12px;
            padding: 14px 16px;
            overflow: auto;
            white-space: pre;
            color: #cfe4ff;
        }

        .badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: #0e1430;
            color: #b9c8ee;
        }

        .tag {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #101736;
            border: 1px solid rgba(255, 255, 255, .08);
            color: #c5d6ff;
            font-size: 12px;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .footer {
            margin-top: 44px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
            padding: 18px 0 40px;
        }

        @media (max-width: 980px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="hero">
        <div>
            <span class="pill">{{ config('app.name', 'Ecommerce API') }} · Laravel · DDD</span>
            <h1 class="h1">Laravel Ecommerce API</h1>
            <p class="lead">A robust e‑commerce API built with <strong>Laravel</strong> and structured using <strong>Domain‑Driven
                    Design</strong>. Designed for modularity, maintainability, and scalability. Dockerized for a
                consistent developer experience and bundled with an extensive automated test suite.</p>
            <div class="cta">
                <a class="btn" href="/postman_collection.json" title="Project documentation">📚 Open Docs</a>
                <a class="btn secondary" href="https://github.com/ayodeleoniosun/laravel-ecommerce-api" target="_blank"
                   rel="noopener">⭐ GitHub Repo</a>
            </div>
        </div>
        <div class="card" style="padding:18px">
            <div class="section">
                <h2>Quick Start</h2>
                <div class="code mono">
                    make serve
                </div>
                <p class="lead" style="margin-top:10px">The command above builds the Docker images, installs
                    dependencies, and boots the stack. Set your <span class="kbd">.env</span> first by copying <span
                        class="kbd">.env.example</span> → <span class="kbd">.env</span> and updating variables.</p>
            </div>
            <div class="section">
                <h2>Base URL</h2>
                <div class="code mono">http://localhost:8000</div>
                <p class="lead" style="margin-top:10px">Use the Base URL above when importing the provided Postman
                    collection to explore endpoints.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-3 section">
        <div class="card" style="padding:16px 16px 6px">
            <h2>Features</h2>
            <ul class="list">
                <li>Laravel with expressive, elegant syntax</li>
                <li>Domain‑Driven Design modular architecture</li>
                <li>Clean layers: <span class="kbd">Application</span>, <span class="kbd">Domain</span>, <span
                        class="kbd">Infrastructure</span></li>
                <li>Docker support for consistent environments</li>
                <li>Event‑driven architecture & event sourcing</li>
                <li>Well‑written unit & integration tests</li>
            </ul>
        </div>
        <div class="card" style="padding:16px 16px 6px">
            <h2>Tech Stack</h2>
            <ul class="list">
                <li>PHP · Laravel</li>
                <li>PostgreSQL</li>
                <li>Docker &amp; Docker Compose</li>
                <li>Testing with Pest</li>
            </ul>
            <div style="margin-top:10px">
                <span class="tag">Laravel</span>
                <span class="tag">PostgreSQL</span>
                <span class="tag">DDD</span>
                <span class="tag">Event Sourcing</span>
                <span class="tag">Docker</span>
            </div>
        </div>
        <div class="card" style="padding:16px 16px 6px">
            <h2>Project Scripts</h2>
            <ul class="list">
                <li><span class="kbd">make serve</span> — build containers and start services</li>
                <li><span class="kbd">docker compose run test</span> — run all tests (Pest)</li>
            </ul>
        </div>
    </div>

    <div class="grid grid-2 section">
        <div class="card" style="padding:18px">
            <h2>Installation</h2>
            <ol class="list">
                <li>Step 1 - Clone the repository:
                    <div class="code mono">git clone https://github.com/ayodeleoniosun/laravel-ecommerce-api.git</div>
                </li>
                <li>Step 2 - Switch to the repo folder
                    <div class="code mono">cd laravel-ecommerce-api</div>
                </li>
                <li>Step 3 - Setup environment variable:
                    <div class="code mono">cp .env.example .env</div>
                    and update variables
                </li>
                <li>Step 4 - Setup docker containers and install all dependencies:
                    <div class="code mono">make serve</div>
                    <br/>
                </li>
            </ol>
        </div>
        <div class="card" style="padding:18px">
            <h2>Testing</h2>
            <p class="lead">Run the full test suite (unit + integration):</p>
            <div class="code mono">docker compose run test</div>
            <p class="lead" style="margin-top:10px">The project ships with an extensive test suite to ensure domain
                correctness and infrastructure stability.</p>
        </div>
    </div>

    <div class="card section" style="padding:18px">
        <h2>Postman Collection</h2>
        <p class="lead">A local Postman collection is included in the repository. Import it and set the Base URL to
            <span class="kbd">http://localhost:8000</span> to start exploring endpoints.</p>
    </div>

    <p class="footer">© {{ now()->year }} {{ config('app.name', 'Ecommerce API') }} · Built with Laravel &middot;
        Domain‑Driven Design</p>
</div>
</body>
</html>
