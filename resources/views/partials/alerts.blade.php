@php
    use Illuminate\Support\MessageBag;

    $alertDefinitions = [
        'success' => [
            'title' => 'Berhasil',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm-1.05 14.3-3.25-3.2 1.4-1.42 1.85 1.82 4.75-4.8 1.42 1.4Z"/></svg>',
            'autoDismiss' => true,
            'type' => 'success',
        ],
        'info' => [
            'title' => 'Informasi',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-6h2Zm0-8h-2V7h2Z"/></svg>',
            'autoDismiss' => true,
            'type' => 'info',
        ],
        'warning' => [
            'title' => 'Perhatian',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 21h22L12 2Zm12-3h-2v-2h2Zm0-3h-2v-4h2Z"/></svg>',
            'autoDismiss' => false,
            'type' => 'warning',
        ],
        'danger' => [
            'title' => 'Terjadi Kesalahan',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm3.54 12.46-1.08 1.08L12 13.08l-2.46 2.46-1.08-1.08L10.92 12 8.46 9.54l1.08-1.08L12 10.92l2.46-2.46 1.08 1.08L13.08 12Z"/></svg>',
            'autoDismiss' => false,
            'type' => 'danger',
        ],
    ];

    $sessionToAlertMap = [
        'success' => 'success',
        'status' => 'info',
        'info' => 'info',
        'message' => 'info',
        'warning' => 'warning',
        'alert' => 'warning',
        'error' => 'danger',
        'danger' => 'danger',
    ];

    $alertStack = [];

    foreach ($sessionToAlertMap as $sessionKey => $alertKey) {
        $payload = session($sessionKey);
        if (blank($payload)) {
            continue;
        }

        $messages = [];
        if ($payload instanceof MessageBag) {
            $messages = $payload->all();
        } elseif (is_array($payload)) {
            $messages = $payload;
        } else {
            $messages = [$payload];
        }

        foreach ($messages as $message) {
            $alertStack[] = [
                'type' => $alertDefinitions[$alertKey]['type'],
                'title' => $alertDefinitions[$alertKey]['title'],
                'icon' => $alertDefinitions[$alertKey]['icon'],
                'autoDismiss' => $alertDefinitions[$alertKey]['autoDismiss'],
                'message' => $message,
                'isList' => false,
            ];
        }
    }

    if ($errors->any()) {
        $alertStack[] = [
            'type' => 'danger',
            'title' => 'Silakan periksa kembali',
            'icon' => $alertDefinitions['danger']['icon'],
            'autoDismiss' => false,
            'message' => $errors->all(),
            'isList' => true,
        ];
    }
@endphp

@once('ui-alert-styles')
    <style>
        .ui-alert-region {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 2050;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: min(360px, calc(100% - 2.5rem));
            pointer-events: none;
        }

        .ui-alert {
            display: flex;
            gap: 0.85rem;
            align-items: flex-start;
            padding: 0.95rem 1.15rem;
            border-radius: 0.9rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            backdrop-filter: blur(4px);
            border-left: 6px solid transparent;
            animation: ui-alert-enter 320ms ease forwards;
            pointer-events: auto;
            background: #fff;
            color: #1f2933;
        }

        .ui-alert__icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.6);
        }

        .ui-alert__icon svg {
            width: 18px;
            height: 18px;
        }

        .ui-alert__content {
            flex: 1;
            min-width: 0;
        }

        .ui-alert__title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
        }

        .ui-alert__message {
            font-size: 0.9rem;
            line-height: 1.35;
            margin: 0;
        }

        .ui-alert__message ul {
            padding-left: 1.2rem;
            margin: 0.3rem 0 0;
        }

        .ui-alert__close {
            border: none;
            background: transparent;
            color: inherit;
            font-size: 1rem;
            cursor: pointer;
            margin-left: 0.25rem;
            opacity: 0.6;
            transition: opacity 150ms ease, transform 150ms ease;
        }

        .ui-alert__close:hover {
            opacity: 1;
            transform: scale(1.05);
        }

        .ui-alert--success {
            background: #edf9ef;
            border-left-color: #2d9c55;
        }

        .ui-alert--success .ui-alert__icon {
            background: rgba(45, 156, 85, 0.12);
            color: #2d9c55;
        }

        .ui-alert--info {
            background: #eef6ff;
            border-left-color: #1d6fa5;
        }

        .ui-alert--info .ui-alert__icon {
            background: rgba(29, 111, 165, 0.12);
            color: #1d6fa5;
        }

        .ui-alert--warning {
            background: #fff7e6;
            border-left-color: #c68b00;
        }

        .ui-alert--warning .ui-alert__icon {
            background: rgba(198, 139, 0, 0.12);
            color: #c68b00;
        }

        .ui-alert--danger {
            background: #ffeef0;
            border-left-color: #c5344e;
        }

        .ui-alert--danger .ui-alert__icon {
            background: rgba(197, 52, 78, 0.12);
            color: #c5344e;
        }

        .ui-alert--leaving {
            animation: ui-alert-leave 240ms ease forwards;
        }

        @keyframes ui-alert-enter {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes ui-alert-leave {
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        @media (max-width: 768px) {
            .ui-alert-region {
                top: auto;
                bottom: 1rem;
                left: 1rem;
                right: 1rem;
                width: auto;
            }

            .ui-alert {
                border-radius: 0.75rem;
            }
        }
    </style>
@endonce
@if (!empty($alertStack))
    <div class="ui-alert-region" role="region" aria-live="assertive">
        @foreach ($alertStack as $index => $alert)
            <div class="ui-alert ui-alert--{{ $alert['type'] }}" role="alert" data-alert-index="{{ $index }}" data-alert-auto-dismiss="{{ $alert['autoDismiss'] ? 'true' : 'false' }}">
                <div class="ui-alert__icon">{!! $alert['icon'] !!}</div>
                <div class="ui-alert__content">
                    <p class="ui-alert__title">{{ $alert['title'] }}</p>
                    @if ($alert['isList'])
                        <div class="ui-alert__message">
                            <ul>
                                @foreach ($alert['message'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="ui-alert__message">{{ $alert['message'] }}</p>
                    @endif
                </div>
                <button type="button" class="ui-alert__close" aria-label="Tutup notifikasi" data-alert-close>&times;</button>
            </div>
        @endforeach
    </div>

@endif

@once('ui-alert-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ICONS = {
                success: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm-1.05 14.3-3.25-3.2 1.4-1.42 1.85 1.82 4.75-4.8 1.42 1.4Z"/></svg>',
                info: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-6h2Zm0-8h-2V7h2Z"/></svg>',
                warning: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1 21h22L12 2Zm12-3h-2v-2h2Zm0-3h-2v-4h2Z"/></svg>',
                danger: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm3.54 12.46-1.08 1.08L12 13.08l-2.46 2.46-1.08-1.08L10.92 12 8.46 9.54l1.08-1.08L12 10.92l2.46-2.46 1.08 1.08L13.08 12Z"/></svg>',
            };

            const REGION_SELECTOR = '.ui-alert-region';

            const dismissAlert = (alertEl) => {
                if (!alertEl || alertEl.classList.contains('ui-alert--leaving')) {
                    return;
                }

                alertEl.classList.add('ui-alert--leaving');
                alertEl.addEventListener('animationend', () => alertEl.remove(), { once: true });
            };

            const registerAlert = (alertEl, autoDismiss) => {
                const closeBtn = alertEl.querySelector('[data-alert-close]');
                closeBtn?.addEventListener('click', () => dismissAlert(alertEl));

                const shouldDismiss = typeof autoDismiss === 'boolean'
                    ? autoDismiss
                    : alertEl.dataset.alertAutoDismiss === 'true';

                if (shouldDismiss) {
                    setTimeout(() => dismissAlert(alertEl), 5000);
                }
            };

            const ensureRegion = () => {
                let region = document.querySelector(REGION_SELECTOR);
                if (!region) {
                    region = document.createElement('div');
                    region.className = 'ui-alert-region';
                    region.setAttribute('role', 'region');
                    region.setAttribute('aria-live', 'assertive');
                    document.body.appendChild(region);
                }
                return region;
            };

            const createAlertElement = ({ type = 'info', title = 'Informasi', message = '', autoDismiss = true }) => {
                const alertEl = document.createElement('div');
                alertEl.className = `ui-alert ui-alert--${type}`;
                alertEl.setAttribute('role', 'alert');
                alertEl.dataset.alertAutoDismiss = autoDismiss ? 'true' : 'false';

                alertEl.innerHTML = `
                    <div class="ui-alert__icon">${ICONS[type] || ICONS.info}</div>
                    <div class="ui-alert__content">
                        <p class="ui-alert__title">${title}</p>
                        <p class="ui-alert__message">${message}</p>
                    </div>
                    <button type="button" class="ui-alert__close" aria-label="Tutup notifikasi" data-alert-close>&times;</button>
                `;

                return alertEl;
            };

            document.querySelectorAll('.ui-alert').forEach(alertEl => registerAlert(alertEl));

            window.UiAlert = {
                push({ type = 'info', title = 'Informasi', message = '', autoDismiss = true } = {}) {
                    const region = ensureRegion();
                    const alertEl = createAlertElement({ type, title, message, autoDismiss });
                    region.appendChild(alertEl);
                    registerAlert(alertEl, autoDismiss);
                    return alertEl;
                },
                dismiss: dismissAlert,
            };
        });
    </script>
@endonce

