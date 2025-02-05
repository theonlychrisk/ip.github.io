<?php
// IP and host resolution with caching
function getHostWithCache($IP) {
    $cacheFile = sys_get_temp_dir() . '/ip_cache_' . md5($IP);
    if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - 86400))) {
        return file_get_contents($cacheFile);
    } else {
        $resolvedHost = @gethostbyaddr($IP);
        if ($resolvedHost && $resolvedHost !== $IP) {
            file_put_contents($cacheFile, $resolvedHost);
            return $resolvedHost;
        }
    }
    return $IP;
}

// Get IP and host
if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
    $proxy = $_SERVER["REMOTE_ADDR"];
} else {
    $IP = $_SERVER["REMOTE_ADDR"];
    $proxy = null;
}
$host = getHostWithCache($IP);

// Geolocation (using a placeholder API - replace with a real API in production)
$geoData = json_decode(file_get_contents("http://ip-api.com/json/{$IP}"), true);

// Timestamp formatting
$userLocale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$timestamp = time();
if (class_exists('IntlDateFormatter')) {
    try {
        $dateFormat = new IntlDateFormatter(
            $userLocale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::LONG,
            null,
            null,
            "EEEE, d MMMM y HH:mm:ss"
        );
        $formattedDate = $dateFormat->format($timestamp);
    } catch (Exception $e) {
        $formattedDate = date('l, d F Y H:i:s');
        error_log("IntlDateFormatter error: " . $e->getMessage());
    }
} else {
    $formattedDate = date('l, d F Y H:i:s');
    error_log("Intl extension not available. Using default date format.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Information</title>
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
        .copy-tooltip {
            position: absolute;
            background-color: #34495e;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
            opacity: 0;
            transition: opacity 0.3s;
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
        <h1>IP Information</h1>
        <div class="ip-display" onclick="copyIP(event)" aria-label="Click to copy your IP Address">
            <?php echo htmlspecialchars($IP); ?>
        </div>
        <div class="details">
            <h2>Details</h2>
            <ul>
                <li><strong>Remote Port:</strong> <?php echo htmlspecialchars($_SERVER["REMOTE_PORT"]); ?></li>
                <li><strong>Request Method:</strong> <?php echo htmlspecialchars($_SERVER["REQUEST_METHOD"]); ?></li>
                <li><strong>Server Protocol:</strong> <?php echo htmlspecialchars($_SERVER["SERVER_PROTOCOL"]); ?></li>
                <li><strong>Server Host:</strong> <?php echo htmlspecialchars($host); ?></li>
                <li><strong>User Agent:</strong> <?php echo htmlspecialchars($_SERVER["HTTP_USER_AGENT"]); ?></li>
                <?php if (!empty($proxy)): ?>
                    <li><strong>Proxy:</strong> <?php echo htmlspecialchars($proxy); ?></li>
                <?php endif; ?>
                <?php if (!empty($geoData['country'])): ?>
                    <li><strong>Location:</strong> <?php echo htmlspecialchars($geoData['country']); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <p class="timestamp">
            <?php echo htmlspecialchars($formattedDate); ?>
        </p>
    </div>
    <script>
        function copyIP(event) {
            const ip = "<?php echo addslashes($IP); ?>";
            navigator.clipboard.writeText(ip).then(function() {
                showTooltip(event, "IP copied!");
            }, function(err) {
                console.error('Could not copy text: ', err);
                showTooltip(event, "Copy failed");
            });
        }

        function showTooltip(event, message) {
            const tooltip = document.createElement('div');
            tooltip.textContent = message;
            tooltip.className = 'copy-tooltip';
            
            // Position the tooltip
            tooltip.style.left = event.pageX + 'px';
            tooltip.style.top = (event.pageY - 40) + 'px';
            
            document.body.appendChild(tooltip);
            
            // Fade in
            setTimeout(() => tooltip.style.opacity = 1, 0);
            
            // Fade out and remove
            setTimeout(() => {
                tooltip.style.opacity = 0;
                setTimeout(() => document.body.removeChild(tooltip), 300);
            }, 2000);
        }
    </script>
</body>
</html>