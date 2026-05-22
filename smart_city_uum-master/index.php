<?php
require __DIR__ . '/data/dashboard-data.php';

// Calculate Average Traffic Flow
$totalFlow = 0;
foreach($trafficSensors as $sensor) {
    $totalFlow += (int)$sensor['flow'];
}
$avgFlow = count($trafficSensors) > 0 ? round($totalFlow / count($trafficSensors)) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Changlun Smart City</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    :root {
        --bg-main: #0b0f19;
        --card-glass: rgba(20, 27, 45, 0.6);
        --card-border: rgba(255, 255, 255, 0.1);
        --primary: #00f2fe;
        --secondary: #4facfe;
        --success: #00ff87;
        --warning: #fec163;
        --danger: #ff4b2b;
        --text: #f8fafc;
        --muted: #94a3b8;
        --shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

    body {
        background-color: var(--bg-main);
        background-image: 
            radial-gradient(circle at 15% 50%, rgba(79, 172, 254, 0.15), transparent 25%),
            radial-gradient(circle at 85% 30%, rgba(0, 242, 254, 0.15), transparent 25%);
        background-attachment: fixed;
        color: var(--text);
        min-height: 100vh;
    }

    a { text-decoration: none; }

    .glass-panel {
        background: var(--card-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow);
        border-radius: 24px;
    }

    .dashboard { display: flex; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar {
        width: 280px;
        background: rgba(11, 15, 25, 0.8);
        backdrop-filter: blur(16px);
        border-right: 1px solid var(--card-border);
        padding: 30px 20px;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .brand { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
    .brand-logo { width: 50px; height: 50px; border-radius: 16px; background: linear-gradient(135deg, var(--secondary), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 20px; box-shadow: 0 0 20px rgba(0, 242, 254, 0.4); }
    .brand h4 { margin: 0; font-size: 20px; font-weight: 700; background: -webkit-linear-gradient(#fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
    .brand span { color: var(--primary); font-size: 12px; letter-spacing: 1px; text-transform: uppercase;}

    .sidebar-menu { display: flex; flex-direction: column; gap: 8px; }
    .sidebar-menu a { color: var(--muted); padding: 14px 18px; border-radius: 14px; transition: all 0.3s ease; display: flex; align-items: center; gap: 12px; font-weight: 500; }
    .sidebar-menu a.active { background: rgba(0, 242, 254, 0.1); color: var(--primary); border-left: 3px solid var(--primary); }

    /* MAIN */
    .main { flex: 1; padding: 30px; display: flex; flex-direction: column; }

    /* TOPBAR */
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .topbar h1 { font-size: 36px; font-weight: 700; margin-bottom: 5px; }
    .topbar p { color: var(--muted); margin:0; }
    .date-box { padding: 12px 24px; color: var(--primary); font-weight: 600; letter-spacing: 1px; }

    /* STAT CARDS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card { display: block; color: var(--text); padding: 24px; transition: 0.3s; position: relative; overflow: hidden; cursor: pointer; }
    .stat-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 30px rgba(0, 242, 254, 0.15); color: var(--text); }
    .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 20px; background: rgba(255,255,255,0.05); }
    .icon-blue { color: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.3); }
    .icon-orange { color: var(--warning); box-shadow: 0 0 15px rgba(254, 193, 99, 0.3); }
    .icon-green { color: var(--success); box-shadow: 0 0 15px rgba(0, 255, 135, 0.3); }
    .icon-red { color: var(--danger); box-shadow: 0 0 15px rgba(255, 75, 43, 0.3); }
    .stat-card h2 { font-size: 32px; font-weight: 700; margin-bottom: 5px; }
    .stat-card p { color: var(--muted); font-size: 14px; margin: 0; }

    /* MAIN SPLIT CONTENT: CCTV Left, Map Right */
    .split-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        flex: 1; /* Pushes to bottom of screen */
    }

    /* CCTV SECTION */
    .cctv-panel { padding: 25px; display: flex; flex-direction: column; }
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px; }
    .panel-header h3 { font-size: 20px; font-weight: 600; margin: 0; }
    .status-chip { padding: 6px 12px; border-radius: 8px; font-size: 11px; background: rgba(255, 75, 43, 0.15); color: var(--danger); font-weight: 600; text-transform: uppercase; border: 1px solid rgba(255, 75, 43, 0.3); }

    /* 2x2 TILE GRID FOR CCTV */
    .cctv-grid-2x2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        flex: 1;
    }
    .cctv-card {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 12px;
        display: flex;
        flex-direction: column;
    }
    .cctv-screen {
        flex: 1;
        min-height: 120px;
        border-radius: 10px;
        background: repeating-linear-gradient(0deg, rgba(255,255,255,0.03) 0 1px, #111 1px 4px);
        position: relative;
        margin-bottom: 10px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .live-badge {
        position: absolute; top: 8px; left: 8px;
        background: rgba(255, 75, 43, 0.8);
        color: white; padding: 4px 8px; border-radius: 6px;
        font-size: 10px; font-weight: 700; display: flex; align-items: center; gap: 5px;
    }
    .live-dot { width: 6px; height: 6px; background: white; border-radius: 50%; animation: blink 1s infinite; }
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

    /* MAP SECTION */
    .map-wrapper {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--card-border);
        box-shadow: 0 0 30px rgba(0, 242, 254, 0.1);
        display: flex;
        flex-direction: column;
    }
    #cityMap { flex: 1; width: 100%; height: 100%; z-index: 1; min-height: 400px; }
    .map-overlay-title {
        position: absolute; top: 20px; left: 20px; z-index: 2;
        background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
        padding: 10px 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);
    }

    @media(max-width: 1200px) {
        .split-content { grid-template-columns: 1fr; }
        .cctv-screen { min-height: 150px; }
    }
    @media(max-width: 900px) {
        .sidebar { display:none; }
    }
</style>
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo"><i class="fa-solid fa-location-dot"></i></div>
            <div>
                <h4>Changlun City</h4>
                <span>Command Center</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="index.php" class="active"><i class="fa-solid fa-border-all"></i> Main Dashboard</a>
            <a href="trafficpage.php"><i class="fa-solid fa-car-burst"></i> Traffic & Map</a>
            <a href="transportpage.php"><i class="fa-solid fa-bus-simple"></i> Transit</a>
            <a href="weatherpage.php"><i class="fa-solid fa-cloud-sun"></i> Weather</a>
            <a href="alertspage.php"><i class="fa-solid fa-triangle-exclamation"></i> Alerts</a>
        </div>
    </aside>

    <main class="main">

        <div class="topbar">
            <div>
                <h1>Command Center</h1>
                <p>Live Overview</p>
            </div>
            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <a href="trafficpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-blue"><i class="fa-solid fa-car-side"></i></div>
                <h2><?php echo $avgFlow; ?>%</h2>
                <p>Avg Traffic Flow</p>
            </a>
            <a href="transportpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-orange"><i class="fa-solid fa-bus"></i></div>
                <h2><?php echo htmlspecialchars($transportRoutes[0]['arrival']); ?></h2>
                <p>Next MyBas Arrival</p>
            </a>
            <a href="weatherpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-green"><i class="fa-solid fa-cloud-sun"></i></div>
                <h2><?php echo htmlspecialchars($weather['temperature']); ?></h2>
                <p><?php echo htmlspecialchars($weather['condition']); ?></p>
            </a>
            <a href="alertspage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-red"><i class="fa-solid fa-bell"></i></div>
                <h2><?php echo count($safetyAlerts); ?></h2>
                <p>City Alerts Active</p>
            </a>
        </div>

        <div class="split-content">
            
            <div class="cctv-panel glass-panel">
                <div class="panel-header">
                    <h3><i class="fa-solid fa-video me-2 text-success"></i>Live Feeds</h3>
                    <span class="status-chip">4 Active</span>
                </div>
                <div class="cctv-grid-2x2">
                    <?php foreach ($cctvFeeds as $feed): ?>
                        <div class="cctv-card">
                            <div class="cctv-screen">
                                <span class="live-badge"><span class="live-dot"></span>LIVE</span>
                            </div>
                            <strong class="text-white d-block mb-1" style="font-size: 14px;">
                                <?php echo htmlspecialchars($feed['location']); ?>
                            </strong>
                            <span class="text-secondary" style="font-size: 12px;">
                                <?php echo htmlspecialchars($feed['activity']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="map-wrapper glass-panel">
                <div class="map-overlay-title">
                    <h6 class="mb-0 text-white"><i class="fa-solid fa-route text-info me-2"></i>Live Traffic Routing</h6>
                </div>
                <div id="cityMap"></div>
            </div>

        </div>

    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Initialize map centered on Changlun
    var map = L.map('cityMap', { zoomControl: false }).setView([6.4350, 100.4285], 14);

    // 2. Add Dark Mode tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // 3. Custom Glowing Icon for Map Markers
    var pulsingIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #00f2fe; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 15px 5px rgba(0, 242, 254, 0.6); border: 2px solid white;"></div>',
        iconSize: [12, 12], iconAnchor: [6, 6]
    });

    // Add Markers
    L.marker([6.4285, 100.4285], {icon: pulsingIcon}).addTo(map).bindPopup('<b>C-Mart Junction</b>');
    L.marker([6.4450, 100.4450], {icon: pulsingIcon}).addTo(map).bindPopup('<b>Route to UUM</b>');

    // -----------------------------------------------------
    // 4. ADDING THE "WAZE-LIKE" TRAFFIC ROUTE LINES
    // -----------------------------------------------------

    // Segment 1: Smooth traffic coming from Toll (Cyan Color)
    var routeSmooth = [
        [6.4500, 100.4200], // North area
        [6.4400, 100.4220],
        [6.4320, 100.4260]  // Nearing town
    ];
    L.polyline(routeSmooth, {
        color: '#00f2fe', // Bright Cyan
        weight: 6,
        opacity: 0.9,
        lineJoin: 'round'
    }).addTo(map);

    // Segment 2: Heavy Traffic / Jam in the middle of town (Red Color)
    var routeJam = [
        [6.4320, 100.4260], // Start of jam
        [6.4285, 100.4285], // Center of town (C-Mart)
        [6.4250, 100.4310]  // End of jam near bus stop
    ];
    L.polyline(routeJam, {
        color: '#ff4b2b', // Waze Red/Orange for Heavy Traffic
        weight: 6,
        opacity: 0.9,
        lineJoin: 'round'
    }).addTo(map);

    // Segment 3: Moderate traffic heading to UUM Sintok (Yellow/Green)
    var routeModerate = [
        [6.4250, 100.4310], // Leaving town
        [6.4300, 100.4380], // Highway curve
        [6.4450, 100.4450]  // Arriving near UUM
    ];
    L.polyline(routeModerate, {
        color: '#fec163', // Yellow/Orange
        weight: 6,
        opacity: 0.9,
        lineJoin: 'round'
    }).addTo(map);

</script>

</body>
</html>