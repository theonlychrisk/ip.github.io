<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Info</title>
    <link rel="icon" href="/favicon.svg" sizes="any" type="image/svg+xml">
    <style>
        :root {
            --bg-color: #f4f4f4;
            --text-color: #333;
            --container-bg: #fff;
            --ip-bg: #ecf0f1;
            --tooltip-bg: #34495e;
            --details-bg: #f9f9f9;
        }

        /* Dark mode variables */
        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --container-bg: #1e1e1e;
            --ip-bg: #2e2e2e;
            --tooltip-bg: #666;
            --details-bg: #2c2c2c;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            margin: 0;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: var(--container-bg);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        h1 {
            color: var(--text-color);
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .ip-display {
            background-color: var(--ip-bg);
            padding: 20px;
            border-radius: 5px;
            font-size: 1.5em;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .ip-display:hover {
            background-color: #d0d3d4;
        }

        .ip-display:active {
            background-color: #bdc3c7;
        }

        .copy-tooltip {
            position: fixed;
            background-color: var(--tooltip-bg);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .details {
            background-color: var(--details-bg);
            padding: 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .details ul {
            list-style-type: none;
            padding: 0;
        }

        .details li {
            margin-bottom: 10px;
        }

        .details strong {
            color: #2980b9;
        }

        .timestamp {
            text-align: right;
            font-size: 0.8em;
            color: #7f8c8d;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 2em;
            }

            .ip-display {
                font-size: 1.2em;
            }
        }

        /* Dark Mode Toggle Button */
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .theme-toggle:hover {
            background-color: #3498db;
        }
    </style>
</head>

<body>
    <button class="theme-toggle" id="theme-toggle">Dark</button>

    <div class="container">
        <h1>IP Info</h1>
        <div id="ip-display" class="ip-display" aria-label="Click to copy your IP Address">
            <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?>
        </div>
        <div class="details">
            <h2>Details</h2>
            <ul>
                <li><strong>Remote Port:</strong> <?php echo htmlspecialchars($_SERVER['REMOTE_PORT']); ?></li>
                <li><strong>Request Method:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_METHOD']); ?></li>
                <li><strong>Server Protocol:</strong> <?php echo htmlspecialchars($_SERVER['SERVER_PROTOCOL']); ?></li>
                <li><strong>User Agent:</strong> <?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></li>
            </ul>
        </div>
        <p class="timestamp">
            <?php echo date('l, d F Y H:i:s'); ?>
        </p>
    </div>

    <script>
        // Toggle between light and dark modes
        const toggleButton = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Apply saved theme on page load
        document.documentElement.setAttribute('data-theme', currentTheme);
        toggleButton.textContent = currentTheme === 'dark' ? 'Light' : 'Dark';

        toggleButton.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            toggleButton.textContent = newTheme === 'dark' ? 'Light' : 'Dark';

            // Save theme in local storage
            localStorage.setItem('theme', newTheme);
        });

        // Copy IP address functionality
        document.getElementById('ip-display').addEventListener('click', function(event) {
            var ip = this.textContent.trim();
            navigator.clipboard.writeText(ip).then(function() {
                showTooltip(event, "IP copied!");
            }, function(err) {
                console.error('Could not copy text: ', err);
                showTooltip(event, "Copy failed");
            });
        });

        function showTooltip(event, message) {
            var tooltip = document.createElement('div');
            tooltip.textContent = message;
            tooltip.className = 'copy-tooltip';

            // Position the tooltip
            tooltip.style.left = event.clientX + 'px';
            tooltip.style.top = (event.clientY - 40) + 'px';

            document.body.appendChild(tooltip);

            // Fade in
            setTimeout(function() {
                tooltip.style.opacity = 1;
            }, 0);

            // Fade out and remove
            setTimeout(function() {
                tooltip.style.opacity = 0;
                setTimeout(function() {
                    document.body.removeChild(tooltip);
                }, 300);
            }, 2000);
        }
    </script>
</body>

</html>