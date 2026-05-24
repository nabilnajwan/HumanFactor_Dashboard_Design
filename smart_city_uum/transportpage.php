<?php
// Enhanced mock data generation mirroring comprehensive dashboard-data.php rules
$transportRoutes = [
    [
        'route' => 'T23',
        'name' => 'MyBas Changlun Town Loop',
        'path' => 'Stesen Bas → C-Mart → Kolej Universiti → Stesen Bas',
        'scheduled' => '11:15 AM',
        'status' => 'Ongoing',
        'eta' => 'On Time',
        'color' => '#00ff87',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4215, 100.4230]
    ],
    [
        'route' => 'K40',
        'name' => 'Changlun - UUM Sintok Express',
        'path' => 'Stesen Bas → Pekan Changlun → UUM Welcome Center',
        'scheduled' => '11:30 AM',
        'status' => 'Delayed',
        'eta' => '11:48 AM (18m delay)',
        'color' => '#ff4b2b',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4600, 100.4215]
    ],
    [
        'route' => 'T24',
        'name' => 'Changlun Border Shuttle',
        'path' => 'Stesen Bas → Bukit Kayu Hitam ICQS',
        'scheduled' => '11:45 AM',
        'status' => 'Ongoing',
        'eta' => 'On Time',
        'color' => '#00f2fe',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4480, 100.4210]
    ],
    [
        'route' => 'GB01',
        'name' => 'GITBUS Campus Shuttle',
        'path' => 'UUM Changlun Housing → Main Highway Corridor',
        'scheduled' => '11:50 AM',
        'status' => 'Ongoing',
        'eta' => 'On Time',
        'color' => '#4facfe',
        'start_coord' => [6.4250, 100.4340],
        'end_coord' => [6.4380, 100.4260]
    ],
    [
        'route' => 'K42',
        'name' => 'Changlun Rural Connection',
        'path' => 'Stesen Bas → Kg. Sawa → Felda Laka Selatan',
        'scheduled' => '12:00 PM',
        'status' => 'Scheduled',
        'eta' => '--',
        'color' => '#fec163',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4150, 100.4410]
    ]
];

// Calculations for KPI Cards
$totalBuses = count($transportRoutes);
$delayedBuses = 0;
$ongoingBuses = 0;

foreach ($transportRoutes as $route) {
    if (strtolower($route['status']) === 'delayed') $delayedBuses++;
    if (strtolower($route['status']) === 'ongoing') $ongoingBuses++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Changlun Smart City - Transit Control</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet Routing Machine CSS Plugin Integration -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

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
    .sidebar-menu a:hover { color: var(--primary); background: rgba(0, 242, 254, 0.05); }
    .sidebar-menu a.active { background: rgba(0, 242, 254, 0.1); color: var(--primary); border-left: 3px solid var(--primary); }

    /* MAIN CONTAINER */
    .main { 
        flex: 1; 
        padding: 15px 20px; 
        display: flex; 
        flex-direction: column; 
        height: 100vh; 
        min-width: 0; 
        overflow: hidden; 
    }

    /* TOPBAR */
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-shrink: 0; }
    .topbar-left { display: flex; align-items: center; gap: 15px; }
    .menu-toggle-btn {
        background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text);
        width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: 0.3s ease; font-size: 18px;
    }
    .menu-toggle-btn:hover { background: rgba(0, 242, 254, 0.2); color: var(--primary); border-color: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.3); }
    .topbar h1 { font-size: 26px; font-weight: 700; margin-bottom: 2px; }
    .topbar p { color: var(--muted); margin:0; font-size: 13px;}
    .date-box { padding: 8px 16px; color: var(--primary); font-weight: 600; font-size: 13px; letter-spacing: 1px; border-radius: 12px;}

    /* GRID ARCHITECTURE */
    .transit-workspace {
        display: grid;
        grid-template-columns: 260px 1.2fr 1.2fr;
        gap: 15px;
        flex: 1;
        min-height: 0;
        margin-bottom: 10px;
    }

    /* COLUMN 1: KPI STACK */
    .kpi-column {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .kpi-box {
        flex: 1;
        padding: 22px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .kpi-box:hover { transform: translateY(-2px); }
    .kpi-box::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .kpi-ongoing::before { background: var(--success); }
    .kpi-ongoing:hover { border-color: rgba(0, 255, 135, 0.3); }
    .kpi-delay::before { background: var(--danger); }
    .kpi-delay:hover { border-color: rgba(255, 75, 43, 0.3); }
    .kpi-total::before { background: var(--primary); }
    .kpi-total:hover { border-color: rgba(0, 242, 254, 0.3); }

    .kpi-value { font-size: 38px; font-weight: 700; line-height: 1.1; margin-bottom: 4px; }
    .kpi-title { font-size: 13px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-icon { position: absolute; right: 20px; bottom: 20px; font-size: 36px; opacity: 0.4; transition: opacity 0.3s; }
    .kpi-box:hover .kpi-icon { opacity: 0.5; }

    /* COLUMN 2: SCHEDULE TABLE */
    .schedule-column {
        padding: 20px;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .panel-title { font-size: 16px; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; flex-shrink: 0; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 12px;}
    .table-container { flex: 1; overflow-y: auto; min-height: 0; padding-right: 4px; }
    
    .table-container::-webkit-scrollbar, .sidebar-menu::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-track, .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
    .table-container::-webkit-scrollbar-thumb, .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .custom-table th { font-size: 11px; text-transform: uppercase; color: var(--muted); padding: 0 12px 2px 12px; font-weight: 600; border: none; letter-spacing: 0.5px; }
    .custom-table tr { background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.02); transition: all 0.2s ease; }
    .custom-table tr:hover { background: rgba(255, 255, 255, 0.03); transform: scale(1.002); }
    .custom-table td { padding: 14px 12px; font-size: 13px; vertical-align: middle; border-top: 1px solid rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.03); }
    .custom-table td:first-child { border-left: 1px solid rgba(255,255,255,0.03); border-radius: 12px 0 0 12px; }
    .custom-table td:last-child { border-right: 1px solid rgba(255,255,255,0.03); border-radius: 0 12px 12px 0; }

    .route-badge { background: rgba(0, 242, 254, 0.08); padding: 5px 10px; border-radius: 8px; font-weight: 700; color: var(--primary); font-size: 12px; border: 1px solid rgba(0, 242, 254, 0.15); display: inline-block; min-width: 55px; text-align: center; }
    
    .status-pill { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-block; text-transform: uppercase; }
    .status-pill.ongoing { background: rgba(0, 255, 135, 0.12); color: var(--success); border: 1px solid rgba(0, 255, 135, 0.25); }
    .status-pill.delayed { background: rgba(255, 75, 43, 0.12); color: var(--danger); border: 1px solid rgba(255, 75, 43, 0.25); }
    .status-pill.scheduled { background: rgba(254, 193, 99, 0.12); color: var(--warning); border: 1px solid rgba(254, 193, 99, 0.25); }

    /* COLUMN 3: MAP CONTAINER */
    .map-column { 
        position: relative; 
        overflow: hidden; 
        min-height: 0; 
        display: flex; 
        flex-direction: column; 
        border-radius: 20px;
        border: 1px solid var(--card-border);
    }
    
    .map-overlay-title {
        position: absolute; top: 15px; left: 15px;
        background: rgba(11, 15, 25, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15); padding: 10px 18px; border-radius: 12px;
        z-index: 1000; box-shadow: var(--shadow); display: flex; align-items: center; gap: 10px; pointer-events: none;
    }
    .map-overlay-title h6 { font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; margin: 0; }
    
    #transitMap { flex: 1; width: 100%; height: 100%; border-radius: 20px; z-index: 1; }

    /* Hidden element for Leaflet Routing Machine Interface Panels */
    .leaflet-routing-container { display: none !important; }

    .leaflet-tooltip-transit {
        background: rgba(11, 15, 25, 0.95) !important; border: 1px solid rgba(0, 242, 254, 0.3) !important;
        color: #fff !important; font-family: 'Outfit', sans-serif !important; font-size: 12px !important;
        padding: 8px 14px !important; border-radius: 10px !important; box-shadow: var(--shadow) !important;
    }

    /* RESPONSIVENESS BREAKPOINTS */
    @media(max-width: 1400px) {
        .transit-workspace { grid-template-columns: 1fr; grid-template-rows: auto 380px 450px; height: auto; overflow: auto; }
        body, .dashboard, .main { height: auto; overflow: auto; }
        .kpi-column { flex-direction: row; }
        .kpi-box { min-height: 120px; }
        #transitMap { min-height: 450px; }
    }
    @media(max-width: 900px) {
        .kpi-column { flex-direction: column; }
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
            <a href="index.php"><i class="fa-solid fa-border-all"></i> Main Dashboard</a>
            <a href="trafficpage.php"><i class="fa-solid fa-car-burst"></i> Traffic & Map</a>
            <a href="transportpage.php" class="active"><i class="fa-solid fa-bus-simple"></i> Transit</a>
            <a href="weatherpage.php"><i class="fa-solid fa-cloud-sun"></i> Weather</a>
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
                    <h1>Transit Management</h1>
                    <p>Live Fleet Telemetry & Real Road Corridors</p>
                </div>
            </div>
            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <div class="transit-workspace">
            
            <div class="kpi-column">
                <div class="kpi-box glass-panel kpi-ongoing">
                    <div class="kpi-value text-success"><?php echo $ongoingBuses; ?></div>
                    <div class="kpi-title">Buses Active</div>
                    <i class="fa-solid fa-circle-play kpi-icon text-success"></i>
                </div>
                <div class="kpi-box glass-panel kpi-delay">
                    <div class="kpi-value text-danger"><?php echo $delayedBuses; ?></div>
                    <div class="kpi-title">Incidents / Delays</div>
                    <i class="fa-solid fa-triangle-exclamation kpi-icon text-danger"></i>
                </div>
                <div class="kpi-box glass-panel kpi-total">
                    <div class="kpi-value text-info"><?php echo $totalBuses; ?></div>
                    <div class="kpi-title">Total Monitored Fleet</div>
                    <i class="fa-solid fa-bus kpi-icon text-info"></i>
                </div>
            </div>

            <div class="schedule-column glass-panel">
                <div class="panel-title text-white">
                    <i class="fa-solid fa-table-list text-primary"></i> Live Regional Schedules
                </div>
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 15%">ID</th>
                                <th style="width: 55%">Transit Corridor Route</th>
                                <th style="width: 30%">Status / ETA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transportRoutes as $route): 
                                $statusClass = strtolower($route['status']);
                                $textStatusColor = '';
                                if($statusClass === 'delayed') $textStatusColor = 'text-danger';
                                if($statusClass === 'ongoing') $textStatusColor = 'text-success';
                            ?>
                                <tr>
                                    <td><span class="route-badge" style="border-color: <?php echo $route['color']; ?>33; color: <?php echo $route['color']; ?>;"><?php echo htmlspecialchars($route['route']); ?></span></td>
                                    <td>
                                        <div class="text-white fw-semibold" style="font-size:13px;"><?php echo htmlspecialchars($route['name']); ?></div>
                                        <div class="text-white" style="font-size:11px; margin-top:2px;"><i class="fa-solid fa-angle-right me-1 text-primary"></i> <?php echo htmlspecialchars($route['path']); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill <?php echo $statusClass; ?> mb-1">
                                            <?php echo htmlspecialchars($route['status']); ?>
                                        </span>
                                        <?php if($route['eta'] !== '--'): ?>
                                            <div class="<?php echo $textStatusColor; ?>" style="font-size:11px; font-weight:600;"><i class="fa-regular fa-clock me-1"></i><?php echo htmlspecialchars($route['eta']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="map-column">
                <div class="map-overlay-title">
                    <i class="fa-solid fa-satellite-dish text-primary fa-spin" style="--fa-animation-duration: 3s;"></i>
                    <h6 class="text-white">Active Fleet Geolocation (Real-Road Paths)</h6>
                </div>
                <div id="transitMap"></div>
            </div>

        </div>

    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet Routing Machine Core JS Engine Implementation -->
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.getElementById('appSidebar');

    menuToggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            sidebar.classList.toggle('collapsed');
        } else {
            sidebar.classList.toggle('active-mobile');
        }
        setTimeout(() => { map.invalidateSize(); }, 400); 
    });

    // Center map over Changlun Main Interchange Node
    var map = L.map('transitMap', { zoomControl: false }).setView([6.4310, 100.4290], 14);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap', subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Dynamic Fleet Custom Div Icon Generator
    function createBusMarker(color, label) {
        return L.divIcon({
            className: 'custom-transit-marker',
            html: `<div style="position: relative; display: flex; align-items: center; justify-content: center;">
                    <div style="background-color: ${color}; width: 16px; height: 16px; border-radius: 50%; box-shadow: 0 0 14px 5px ${color}; border: 2px solid white; z-index: 2;"></div>
                    <div style="position: absolute; top: -20px; background: rgba(11,15,25,0.9); color: ${color}; font-size: 10px; font-weight:800; padding: 2px 6px; border-radius: 5px; border: 1px solid ${color}44; white-space: nowrap; z-index: 3; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">${label}</div>
                   </div>`,
            iconSize: [16, 16], iconAnchor: [8, 8]
        });
    }

    // Capture explicit backend routing profiles to process inside JavaScript Engine
    const PHP_ROUTES = <?php echo json_encode($transportRoutes); ?>;
    
    // Core telemetry loop array containing runtime simulation markers
    let liveFleetTelemetry = [];

    PHP_ROUTES.forEach((routeData) => {
        // Exclude completely stationary/scheduled lines from tracking
        if (routeData.status.toLowerCase() === 'scheduled') return;

        // Instantiation of the Leaflet Routing Engine targeting the real-world Changlun road infrastructure
        let routingControl = L.Routing.control({
            waypoints: [
                L.latLng(routeData.start_coord[0], routeData.start_coord[1]),
                L.latLng(routeData.end_coord[0], routeData.end_coord[1])
            ],
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1' // Queries OpenStreetMap topology structures
            }),
            lineOptions: {
                styles: [{ color: routeData.color, opacity: 0.4, weight: 4, dashArray: '6, 8' }]
            },
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: false
        }).addTo(map);

        // Once the real-road network coordinates calculation concludes, kick off real-time telemetry animation
        routingControl.on('routesfound', function(e) {
            let routes = e.routes;
            let realRoadCoordinates = routes[0].coordinates; // Exact mapped curve parameters
            
            // Generate telemetry marker instance inside space
            let vehicleMarker = L.marker(realRoadCoordinates[0], {
                icon: createBusMarker(routeData.color, routeData.route)
            }).addTo(map);

            // Bind informative modal dialog popups
            vehicleMarker.bindPopup(`
                <div style="color: #fff; font-family:'Outfit'; padding:2px;">
                    <strong style="color:${routeData.color}; font-size:14px;">Bus ${routeData.route}</strong><br>
                    <small style="color:#94a3b8;">${routeData.name}</small><br>
                    <hr style="border-top:1px solid rgba(255,255,255,0.1); margin:6px 0;">
                    <b>Status:</b> ${routeData.status}<br>
                    <b>ETA:</b> ${routeData.eta}
                </div>
            `);

            liveFleetTelemetry.push({
                marker: vehicleMarker,
                coords: realRoadCoordinates,
                currentIndex: Math.floor(Math.random() * (realRoadCoordinates.length / 2)), // Scatter variations on initialize
                speedFactor: routeData.status.toLowerCase() === 'delayed' ? 0.2 : 0.5, // KNOB 1: Step sizing per tick (lower is slower)
                direction: 1 // 1 = forward route direction, -1 = reverse back to origin
            });
        });
    });

    // Simulated Central GPS Engine ticking at regular intervals for asset rendering
    setInterval(() => {
        liveFleetTelemetry.forEach((vehicle) => {
            // Adjust step array positioning dynamically based on direction coefficient
            vehicle.currentIndex += (vehicle.speedFactor * vehicle.direction);
            
            // Forward edge checking bounding parameters
            if (vehicle.currentIndex >= vehicle.coords.length - 1) {
                vehicle.currentIndex = vehicle.coords.length - 1;
                vehicle.direction = -1; // Toggle direction vector backwards
            } 
            // Backward coordinate limit bounding parameters
            else if (vehicle.currentIndex <= 0) {
                vehicle.currentIndex = 0;
                vehicle.direction = 1; // Toggle direction vector forward
            }

            // Target mapping node parsing index assignment
            let roundedIndex = Math.floor(vehicle.currentIndex);
            let nextNodePosition = vehicle.coords[roundedIndex];
            
            if (nextNodePosition) {
                vehicle.marker.setLatLng([nextNodePosition.lat, nextNodePosition.lng]);
            }
        });
    }, 100); // KNOB 2: Refresh clock ticks in ms (higher value is slower execution)
</script>

</body>
</html>