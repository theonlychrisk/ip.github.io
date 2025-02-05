<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Info</title>
    <link rel="icon" href="/favicon.svg" sizes="any" type="image/svg+xml">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .ip-display {
            background-color: #ecf0f1;
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
            background-color: #34495e;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
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
    </style>
</head>
<body>
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
                <!-- Add more details as needed -->
            </ul>
        </div>
        <p class="timestamp">
            <?php echo date('l, d F Y H:i:s'); ?>
        </p>
    </div>
    <script>
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
            setTimeout(function() { tooltip.style.opacity = 1; }, 0);
            
            // Fade out and remove
            setTimeout(function() {
                tooltip.style.opacity = 0;
                setTimeout(function() { document.body.removeChild(tooltip); }, 300);
            }, 2000);
        }
    </script>
</body>
</html>