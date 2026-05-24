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

    /* STRICT 100VH LOCK ON BODY */
    body {
        background-color: var(--bg-main);
        background-image: 
            radial-gradient(circle at 15% 50%, rgba(79, 172, 254, 0.15), transparent 25%),
            radial-gradient(circle at 85% 30%, rgba(0, 242, 254, 0.15), transparent 25%);
        background-attachment: fixed;
        color: var(--text);
        height: 100vh;
        width: 100vw;
        overflow: hidden; 
    }

    a { text-decoration: none; }

    .glass-panel {
        background: var(--card-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow);
        border-radius: 20px;
    }

    /* OVERFLOW HIDDEN ON DASHBOARD WRAPPER */
    .dashboard { display: flex; height: 100vh; width: 100vw; overflow: hidden; }

    /* SIDEBAR */
    .sidebar {
        width: 260px;
        background: rgba(11, 15, 25, 0.8);
        backdrop-filter: blur(16px);
        border-right: 1px solid var(--card-border);
        padding: 25px 20px;
        display: flex;
        flex-direction: column;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        flex-shrink: 0;
    }
    .sidebar.collapsed { margin-left: -260px; }

    .brand { display: flex; align-items: center; gap: 15px; margin-bottom: 35px; flex-shrink: 0; }
    .brand-logo { width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, var(--secondary), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; box-shadow: 0 0 20px rgba(0, 242, 254, 0.4); }
    .brand h4 { margin: 0; font-size: 18px; font-weight: 700; background: -webkit-linear-gradient(#fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
    .brand span { color: var(--primary); font-size: 11px; letter-spacing: 1px; text-transform: uppercase;}

    .sidebar-menu { display: flex; flex-direction: column; gap: 6px; overflow-y: auto; }
    .sidebar-menu a { color: var(--muted); padding: 12px 16px; border-radius: 12px; transition: all 0.3s ease; display: flex; align-items: center; gap: 12px; font-weight: 500; font-size: 15px;}
    .sidebar-menu a.active { background: rgba(0, 242, 254, 0.1); color: var(--primary); border-left: 3px solid var(--primary); }

    /* MAIN CONTAINER - strictly locked to screen height */
    .main { 
        flex: 1; 
        padding: 15px 20px; 
        display: flex; 
        flex-direction: column; 
        height: 100vh; 
        min-width: 0; /* Prevents flex children from blowing out width */
        overflow: hidden; 
        transition: padding 0.4s;
    }

    /* TOPBAR */
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-shrink: 0; }
    .topbar-left { display: flex; align-items: center; gap: 15px; }
    
    .menu-toggle-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text);
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s ease;
        font-size: 18px;
    }
    .menu-toggle-btn:hover { background: rgba(0, 242, 254, 0.2); color: var(--primary); border-color: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.3); }

    .topbar h1 { font-size: 26px; font-weight: 700; margin-bottom: 2px; }
    .topbar p { color: var(--muted); margin:0; font-size: 13px;}
    .date-box { padding: 8px 16px; color: var(--primary); font-weight: 600; font-size: 13px; letter-spacing: 1px; border-radius: 12px;}

    /* STAT CARDS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 15px;
        flex-shrink: 0; /* Protects top row from getting crushed */
    }
    .stat-card { display: flex; align-items: center; color: var(--text); padding: 12px 15px; transition: 0.3s; position: relative; overflow: hidden; cursor: pointer; }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: 0 10px 25px rgba(0, 242, 254, 0.15); color: var(--text); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px; flex-shrink: 0; background: rgba(255,255,255,0.05); }
    .icon-blue { color: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.3); }
    .icon-orange { color: var(--warning); box-shadow: 0 0 15px rgba(254, 193, 99, 0.3); }
    .icon-green { color: var(--success); box-shadow: 0 0 15px rgba(0, 255, 135, 0.3); }
    .icon-red { color: var(--danger); box-shadow: 0 0 15px rgba(255, 75, 43, 0.3); }
    .stat-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 2px; line-height: 1; }
    .stat-card p { color: var(--muted); font-size: 11px; margin: 0; white-space: nowrap; }

    /* MAIN SPLIT CONTENT - Changed to Flex to enforce min-height: 0 */
    .split-content { 
        display: flex; 
        gap: 15px; 
        flex: 1; 
        min-height: 0; /* The magic property that prevents bottom cutoff */
        min-width: 0; 
    }

    /* BOTH PANELS MUST BE FLEX EQUAL */
    .cctv-panel, .map-wrapper { 
        flex: 1; 
        display: flex; 
        flex-direction: column; 
        min-height: 0; 
        min-width: 0; 
    }

    /* CCTV SECTION */
    .cctv-panel { padding: 15px; }
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; flex-shrink: 0; }
    .panel-header h3 { font-size: 16px; font-weight: 600; margin: 0; }
    .status-chip { padding: 4px 10px; border-radius: 6px; font-size: 10px; background: rgba(255, 75, 43, 0.15); color: var(--danger); font-weight: 600; text-transform: uppercase; border: 1px solid rgba(255, 75, 43, 0.3); }

    /* Ensure exactly 2 equal rows to prevent vertical blowout */
    .cctv-grid-2x2 { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        grid-template-rows: 1fr 1fr; 
        gap: 10px; 
        flex: 1; 
        min-height: 0; 
    }
    .cctv-card { background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 8px; display: flex; flex-direction: column; min-height: 0; }
    .cctv-screen { flex: 1; min-height: 0; border-radius: 8px; background: #000; position: relative; margin-bottom: 8px; border: 1px solid rgba(255,255,255,0.15); overflow: hidden; }
    .cctv-video { width: 100%; height: 100%; object-fit: cover; filter: grayscale(80%) contrast(120%) brightness(70%) sepia(20%) hue-rotate(180deg); }
    .cctv-screen::after { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 1px, transparent 3px); pointer-events: none; }
    .live-badge { position: absolute; top: 6px; left: 6px; z-index: 2; background: rgba(255, 75, 43, 0.9); color: white; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; display: flex; align-items: center; gap: 4px; box-shadow: 0 0 10px rgba(255, 75, 43, 0.5); }
    .live-dot { width: 5px; height: 5px; background: white; border-radius: 50%; animation: blink 1s infinite; }
    
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

    /* MAP SECTION */
    .map-wrapper { position: relative; overflow: hidden; border: 1px solid var(--card-border); box-shadow: 0 0 30px rgba(0, 242, 254, 0.1); }
    #cityMap { flex: 1; width: 100%; height: 100%; z-index: 1; }
    
    .map-overlay-title { position: absolute; top: 15px; left: 15px; z-index: 2; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); font-size: 14px; }

    /* MAP FLOATING FILTER BAR */
    .map-filter-bar { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); z-index: 2; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); padding: 6px 12px; border-radius: 12px; display: flex; gap: 8px; box-shadow: var(--shadow); }
    .filter-btn { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--muted); padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 600; cursor: pointer; transition: 0.3s ease; display: flex; align-items: center; gap: 5px; }
    .filter-btn:hover { background: rgba(255,255,255,0.1); color: var(--text); }
    .filter-btn.active[data-cat="attractive"] { background: rgba(0, 242, 254, 0.2); color: var(--primary); border-color: var(--primary); }
    .filter-btn.active[data-cat="food"] { background: rgba(0, 255, 135, 0.2); color: var(--success); border-color: var(--success); }
    .filter-btn.active[data-cat="hotels"] { background: rgba(179, 255, 171, 0.2); color: #b3ffab; border-color: #b3ffab; }
    .filter-btn.active[data-cat="transit"] { background: rgba(254, 193, 99, 0.2); color: var(--warning); border-color: var(--warning); }

    /* CUSTOM HOVER TOOLTIP */
    .leaflet-tooltip.custom-hover-tooltip { background: rgba(15, 23, 42, 0.95) !important; color: var(--text) !important; border: 1px solid var(--card-border) !important; border-radius: 12px !important; padding: 10px !important; font-family: 'Outfit', sans-serif !important; box-shadow: var(--shadow) !important; font-size: 12px !important; }
    .leaflet-tooltip-top.custom-hover-tooltip::before { border-top-color: rgba(15, 23, 42, 0.95) !important; }

    /* MOBILE RESPONSIVENESS */
    @media(max-width: 1200px) {
        body, .dashboard, .main { height: auto; overflow: auto; min-height: 100vh; }
        .split-content { flex-direction: column; }
        .cctv-screen { min-height: 180px; }
        #cityMap { min-height: 400px; }
    }
    @media(max-width: 900px) { 
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .sidebar { position: fixed; margin-left: -260px; height: 100vh; }
        .sidebar.active-mobile { margin-left: 0; box-shadow: 10px 0 30px rgba(0,0,0,0.5); }
    }
</style>
</head>

<body>

<div class="dashboard">

    <aside class="sidebar" id="appSidebar">
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
            <a href="weather.php"><i class="fa-solid fa-cloud-sun"></i> Weather</a>
            <a href="alertspage.php"><i class="fa-solid fa-triangle-exclamation"></i> Alerts</a>
        </div>
    </aside>

    <main class="main">

        <div class="topbar">
            <div class="topbar-left">
                <button id="menuToggleBtn" class="menu-toggle-btn glass-panel">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1>Command Center</h1>
                    <p>Live Overview</p>
                </div>
            </div>

            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <a href="trafficpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-blue"><i class="fa-solid fa-car-side"></i></div>
                <div>
                    <h2><?php echo $avgFlow; ?>%</h2>
                    <p>Avg Traffic Flow</p>
                </div>
            </a>
            <a href="transportpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-orange"><i class="fa-solid fa-bus"></i></div>
                <div>
                    <h2><?php echo htmlspecialchars($transportRoutes[0]['arrival']); ?></h2>
                    <p>Next MyBas Arrival</p>
                </div>
            </a>
            <a href="weatherpage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-green"><i class="fa-solid fa-cloud-sun"></i></div>
                <div>
                    <h2><?php echo htmlspecialchars($weather['temperature']); ?></h2>
                    <p><?php echo htmlspecialchars($weather['condition']); ?></p>
                </div>
            </a>
            <a href="alertspage.php" class="stat-card glass-panel">
                <div class="stat-icon icon-red"><i class="fa-solid fa-bell"></i></div>
                <div>
                    <h2><?php echo count($safetyAlerts); ?></h2>
                    <p>City Alerts Active</p>
                </div>
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
                                <video class="cctv-video" autoplay loop muted playsinline>
                                    <source src="<?php echo htmlspecialchars($feed['video_url']); ?>" type="video/mp4">
                                </video>
                                <span class="live-badge"><span class="live-dot"></span>LIVE</span>
                            </div>
                            <strong class="text-white d-block mb-1" style="font-size: 13px;">
                                <i class="fa-solid fa-video me-1 text-muted"></i> 
                                <?php echo htmlspecialchars($feed['location']); ?>
                            </strong>
                            <span class="text-secondary" style="font-size: 11px;">
                                <?php echo htmlspecialchars($feed['activity']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="map-wrapper glass-panel">
                <div class="map-overlay-title">
                    <h6 class="mb-0 text-white"><i class="fa-solid fa-map-location-dot text-info me-2"></i>Live City Grid Explorer</h6>
                </div>

                <div class="map-filter-bar">
                    <button class="filter-btn active" data-cat="attractive"><i class="fa-solid fa-star"></i> Attractive</button>
                    <button class="filter-btn active" data-cat="food"><i class="fa-solid fa-utensils"></i> Food</button>
                    <button class="filter-btn active" data-cat="hotels"><i class="fa-solid fa-bed"></i> Hotels</button>
                    <button class="filter-btn active" data-cat="transit"><i class="fa-solid fa-bus"></i> Transit</button>
                </div>

                <div id="cityMap"></div>
            </div>

        </div>

    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // -----------------------------------------------------
    // DRAWER MENU LOGIC
    // -----------------------------------------------------
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.getElementById('appSidebar');

    menuToggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            sidebar.classList.toggle('collapsed');
        } else {
            sidebar.classList.toggle('active-mobile');
        }

        // Wait for the slide animation (0.4s) to finish, then recalculate map layout
        setTimeout(() => {
            map.invalidateSize();
        }, 400); 
    });


    // -----------------------------------------------------
    // MAP INITIALIZATION
    // -----------------------------------------------------
    var map = L.map('cityMap', { zoomControl: false }).setView([6.4340, 100.4285], 14);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap', subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // CHANGUN BOUNDARY OUTLINE
    var changlunAreaCoordinates = [
        [6.4520, 100.4120], [6.4550, 100.4350], [6.4400, 100.4500], 
        [6.4180, 100.4420], [6.4140, 100.4250], [6.4250, 100.4080]
    ];
    L.polygon(changlunAreaCoordinates, {
        color: 'rgba(0, 242, 254, 0.6)', fillColor: 'rgba(0, 242, 254, 0.03)', 
        weight: 3, dashArray: '5, 5', lineJoin: 'round'
    }).addTo(map);

    // MARKER DATA & HOVER LOGIC
    var pointsOfInterest = [
        { name: "Taman Awam Changlun", cat: "attractive", lat: 6.4365, lng: 100.4220, desc: "Local recreational park and public green space.", color: "#00f2fe" },
        { name: "Laman Kayu Changlun", cat: "attractive", lat: 6.4312, lng: 100.4245, desc: "Popular local landmark and community gathering spot.", color: "#00f2fe" },
        { name: "Nasi Kandar Yasmeen", cat: "food", lat: 6.4290, lng: 100.4295, desc: "Famous local 24-hour Malaysian Mamak spot.", color: "#00ff87" },
        { name: "KFC Changlun", cat: "food", lat: 6.4302, lng: 100.4272, desc: "Quick-service dining option in town center.", color: "#00ff87" },
        { name: "Restoran Pokok Sawa", cat: "food", lat: 6.4275, lng: 100.4302, desc: "Local favorite traditional breakfast and lunch place.", color: "#00ff87" },
        { name: "T Hotel Changlun", cat: "hotels", lat: 6.4328, lng: 100.4262, desc: "Modern budget accommodation option in town.", color: "#b3ffab" },
        { name: "The Grand Hotel", cat: "hotels", lat: 6.4295, lng: 100.4312, desc: "Comfortable hotel staging point towards the northern border.", color: "#b3ffab" },
        { name: "Stesen Bas Changlun", cat: "transit", lat: 6.4260, lng: 100.4320, desc: "Central intercity express bus terminal hub.", color: "#fec163" },
        { name: "Taxi Stand Changlun", cat: "transit", lat: 6.4271, lng: 100.4308, desc: "Local taxi hub servicing surrounding areas and borders.", color: "#fec163" }
    ];

    var mapLayers = {
        attractive: L.layerGroup().addTo(map), food: L.layerGroup().addTo(map),
        hotels: L.layerGroup().addTo(map), transit: L.layerGroup().addTo(map)
    };

    pointsOfInterest.forEach(function(point) {
        var markerIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color: ' + point.color + '; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 10px 3px ' + point.color + '; border: 2px solid white;"></div>',
            iconSize: [12, 12], iconAnchor: [6, 6]
        });

        var tooltipContent = '<div style="line-height: 1.3;">' +
                             '<b style="color:' + point.color + '; font-size:13px; display:block; margin-bottom:3px;">' + point.name + '</b>' +
                             '<span style="color:#94a3b8; font-size:11px;">' + point.desc + '</span></div>';

        var marker = L.marker([point.lat, point.lng], { icon: markerIcon })
                      .bindTooltip(tooltipContent, { direction: 'top', sticky: false, className: 'custom-hover-tooltip', opacity: 0.95 });
        marker.addTo(mapLayers[point.cat]);
    });

    // FILTER BUTTON LOGIC
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var category = this.getAttribute('data-cat');
            if (this.classList.contains('active')) {
                this.classList.remove('active'); map.removeLayer(mapLayers[category]);
            } else {
                this.classList.add('active'); map.addLayer(mapLayers[category]);
            }
        });
    });
</script>

</body>
</html>