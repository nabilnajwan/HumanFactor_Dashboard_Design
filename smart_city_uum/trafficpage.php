<?php
require __DIR__ . '/data/dashboard-data.php';

// Reusable function for status badge colors
function badgeClass(string $value): string {
    $normalized = strtolower($value);
    if (in_array($normalized, ['busy', 'caution', 'heavy'], true)) return 'status-chip caution';
    if (in_array($normalized, ['notice', 'moderate'], true)) return 'status-chip notice';
    return 'status-chip success';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Traffic Management - Changlun Smart City</title>

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

    /* STRICT SCREEN LOCK */
    body {
        background-color: var(--bg-main);
        background-image: radial-gradient(circle at 15% 50%, rgba(79, 172, 254, 0.15), transparent 25%), radial-gradient(circle at 85% 30%, rgba(0, 242, 254, 0.15), transparent 25%);
        background-attachment: fixed;
        color: var(--text);
        height: 100vh; width: 100vw; overflow: hidden; 
    }

    a { text-decoration: none; }

    .glass-panel {
        background: var(--card-glass); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--card-border); box-shadow: var(--shadow); border-radius: 20px;
    }

    .dashboard { display: flex; height: 100vh; width: 100vw; overflow: hidden; }

    /* SIDEBAR */
    .sidebar {
        width: 260px; background: rgba(11, 15, 25, 0.8); backdrop-filter: blur(16px);
        border-right: 1px solid var(--card-border); padding: 25px 20px; display: flex; flex-direction: column;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000; flex-shrink: 0;
    }
    .sidebar.collapsed { margin-left: -260px; }
    .brand { display: flex; align-items: center; gap: 15px; margin-bottom: 35px; flex-shrink: 0; }
    .brand-logo { width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, var(--secondary), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; box-shadow: 0 0 20px rgba(0, 242, 254, 0.4); }
    .brand h4 { margin: 0; font-size: 18px; font-weight: 700; background: -webkit-linear-gradient(#fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
    .brand span { color: var(--primary); font-size: 11px; letter-spacing: 1px; text-transform: uppercase;}
    .sidebar-menu { display: flex; flex-direction: column; gap: 6px; overflow-y: auto; }
    .sidebar-menu a { color: var(--muted); padding: 12px 16px; border-radius: 12px; transition: all 0.3s ease; display: flex; align-items: center; gap: 12px; font-weight: 500; font-size: 15px;}
    .sidebar-menu a.active { background: rgba(0, 242, 254, 0.1); color: var(--primary); border-left: 3px solid var(--primary); }

    /* MAIN CONTAINER */
    .main { flex: 1; padding: 15px 20px; display: flex; flex-direction: column; height: 100vh; min-width: 0; overflow: hidden; transition: padding 0.4s; }

    /* TOPBAR */
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-shrink: 0; }
    .topbar-left { display: flex; align-items: center; gap: 15px; }
    .menu-toggle-btn { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s ease; font-size: 18px; }
    .menu-toggle-btn:hover { background: rgba(0, 242, 254, 0.2); color: var(--primary); border-color: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.3); }
    .topbar h1 { font-size: 26px; font-weight: 700; margin-bottom: 2px; }
    .topbar p { color: var(--muted); margin:0; font-size: 13px;}

    /* TRAFFIC PAGE SPECIFIC LAYOUT */
    .traffic-top-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; /* Map gets 66% width, CCTV gets 33% */
        gap: 15px;
        flex: 1; /* Takes available top space */
        min-height: 0; 
        margin-bottom: 15px;
    }

    /* MAP PANEL */
    .map-wrapper { position: relative; overflow: hidden; border-radius: 20px; border: 1px solid var(--card-border); box-shadow: 0 0 30px rgba(0, 242, 254, 0.1); display: flex; flex-direction: column; }
    #trafficMap { flex: 1; width: 100%; height: 100%; z-index: 1; }
    .map-overlay-title { position: absolute; top: 15px; left: 15px; z-index: 2; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); font-size: 14px; }
    
    /* MAP LEGEND */
    .map-legend { position: absolute; bottom: 15px; right: 15px; z-index: 2; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); padding: 10px 15px; border-radius: 12px; font-size: 12px; }
    .legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .legend-line { width: 20px; height: 4px; border-radius: 2px; }
    .line-red { background: var(--danger); }
    .line-green { background: var(--success); }

    /* CCTV PANEL (Vertical stack) */
    .cctv-sidebar { display: flex; flex-direction: column; gap: 10px; overflow-y: auto; padding-right: 5px; }
    .cctv-sidebar::-webkit-scrollbar { width: 6px; }
    .cctv-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .cctv-card { background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 8px; display: flex; flex-direction: column; flex-shrink: 0; }
    .cctv-screen { height: 140px; border-radius: 8px; background: #000; position: relative; margin-bottom: 8px; border: 1px solid rgba(255,255,255,0.15); overflow: hidden; }
    .cctv-video { width: 100%; height: 100%; object-fit: cover; filter: grayscale(80%) contrast(120%) brightness(70%) sepia(20%) hue-rotate(180deg); }
    .cctv-screen::after { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 1px, transparent 3px); pointer-events: none; }
    .live-badge { position: absolute; top: 6px; left: 6px; z-index: 2; background: rgba(255, 75, 43, 0.9); color: white; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
    .live-dot { width: 5px; height: 5px; background: white; border-radius: 50%; animation: blink 1s infinite; }
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

    /* DETAILED TRAFFIC TABLE */
    .table-section { height: 35%; flex-shrink: 0; display: flex; flex-direction: column; padding: 20px; }
    .table-responsive { overflow-y: auto; flex: 1; padding-right: 10px; }
    .table-responsive::-webkit-scrollbar { width: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    
    .glass-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .glass-table th { background: rgba(255,255,255,0.03); padding: 12px 15px; font-weight: 600; text-transform: uppercase; font-size: 11px; color: var(--muted); border: none; letter-spacing: 0.5px; position: sticky; top: 0; z-index: 10; }
    .glass-table th:first-child { border-radius: 10px 0 0 10px; }
    .glass-table th:last-child { border-radius: 0 10px 10px 0; }
    
    .glass-table td { background: rgba(0,0,0,0.3); padding: 12px 15px; border: none; font-size: 14px; vertical-align: middle; border-top: 1px solid rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.02); }
    .glass-table tr td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid rgba(255,255,255,0.02); font-weight: 500; }
    .glass-table tr td:last-child { border-radius: 0 12px 12px 0; border-right: 1px solid rgba(255,255,255,0.02); }
    .glass-table tr:hover td { background: rgba(0,242,254,0.05); }

    .status-chip { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .status-chip.success { background: rgba(0, 255, 135, 0.15); color: var(--success); border: 1px solid rgba(0, 255, 135, 0.3); }
    .status-chip.notice { background: rgba(254, 193, 99, 0.15); color: var(--warning); border: 1px solid rgba(254, 193, 99, 0.3); }
    .status-chip.caution { background: rgba(255, 75, 43, 0.15); color: var(--danger); border: 1px solid rgba(255, 75, 43, 0.3); }

    /* Progress bar for flow */
    .flow-bar-bg { width: 100px; height: 6px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; margin-top: 6px; }
    .flow-bar-fill { height: 100%; border-radius: 10px; }

    @media(max-width: 1200px) {
        .traffic-top-grid { grid-template-columns: 1fr; }
        body, .dashboard, .main { height: auto; overflow: auto; min-height: 100vh; }
        .map-wrapper { min-height: 400px; }
        .table-section { height: auto; }
    }
    @media(max-width: 900px) { 
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
            <a href="trafficpage.php" class="active"><i class="fa-solid fa-car-burst"></i> Traffic & Map</a>
            <a href="transportpage.php"><i class="fa-solid fa-bus-simple"></i> Transit</a>
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
                    <h1>Traffic Control</h1>
                    <p>Live Route Optimization</p>
                </div>
            </div>
            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <div class="traffic-top-grid">
            
            <div class="map-wrapper glass-panel">
                <div class="map-overlay-title">
                    <h6 class="mb-0 text-white"><i class="fa-solid fa-map text-info me-2"></i>AI Route Suggestions</h6>
                </div>
                
                <div class="map-legend">
                    <div class="legend-item"><div class="legend-line line-red"></div> Heavy Congestion</div>
                    <div class="legend-item"><div class="legend-line line-green" style="border-bottom: 2px dashed #00ff87; background: transparent;"></div> Suggested Bypass</div>
                </div>

                <div id="trafficMap"></div>
            </div>

            <div class="cctv-sidebar">
                <h3 style="font-size: 16px; margin-bottom: 10px;"><i class="fa-solid fa-video text-success me-2"></i>Road Cams</h3>
                
                <?php for($i=0; $i<2; $i++): $feed = $cctvFeeds[$i]; ?>
                    <div class="cctv-card">
                        <div class="cctv-screen">
                            <video class="cctv-video" autoplay loop muted playsinline>
                                <source src="<?php echo htmlspecialchars($feed['video_url']); ?>" type="video/mp4">
                            </video>
                            <span class="live-badge"><span class="live-dot"></span>LIVE</span>
                        </div>
                        <strong class="text-white d-block mb-1" style="font-size: 13px;">
                            <?php echo htmlspecialchars($feed['location']); ?>
                        </strong>
                        <span class="text-secondary" style="font-size: 11px;">
                            <?php echo htmlspecialchars($feed['activity']); ?>
                        </span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="table-section glass-panel">
            <h3 style="font-size: 18px; margin-bottom: 15px;"><i class="fa-solid fa-table-list text-primary me-2"></i>Live Node Diagnostics</h3>
            
            <div class="table-responsive">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Location / Node</th>
                            <th>Status</th>
                            <th>Volume Flow</th>
                            <th>Avg Speed</th>
                            <th>Est. Delay</th>
                            <th>System Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trafficSensors as $sensor): ?>
                        <tr>
                            <td>
                                <i class="fa-solid fa-location-crosshairs text-primary me-2"></i>
                                <?php echo htmlspecialchars($sensor['area']); ?>
                            </td>
                            <td>
                                <span class="<?php echo badgeClass($sensor['status']); ?>">
                                    <?php echo htmlspecialchars($sensor['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div><?php echo (int)$sensor['flow']; ?>% Capacity</div>
                                <div class="flow-bar-bg">
                                    <div class="flow-bar-fill" style="width: <?php echo (int)$sensor['flow']; ?>%; background: <?php echo (int)$sensor['flow'] > 70 ? 'var(--danger)' : ((int)$sensor['flow'] > 40 ? 'var(--warning)' : 'var(--success)'); ?>"></div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($sensor['speed']); ?></td>
                            <td class="text-warning"><?php echo htmlspecialchars($sensor['delay']); ?></td>
                            <td class="text-muted" style="font-size: 12px; max-width: 200px;"><?php echo htmlspecialchars($sensor['note']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // DRAWER MENU LOGIC
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

    // MAP INITIALIZATION (Focused closer to Town/Highway split)
    var map = L.map('trafficMap', { zoomControl: false }).setView([6.4320, 100.4285], 15);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // ICONS
    var alertIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #ff4b2b; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 15px 5px rgba(255,75,43, 0.6); border: 2px solid white;"></div>',
        iconSize: [14, 14], iconAnchor: [7, 7]
    });
    
    var bypassIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #00ff87; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 15px 5px rgba(0,255,135, 0.6); border: 2px solid white;"></div>',
        iconSize: [14, 14], iconAnchor: [7, 7]
    });

    // 1. MAIN CONGESTED ROUTE (Red)
    var mainRoute = [ [6.4400, 100.4220], [6.4320, 100.4260], [6.4285, 100.4285], [6.4250, 100.4310] ];
    L.polyline(mainRoute, { color: '#ff4b2b', weight: 6, opacity: 0.8, lineJoin: 'round' }).addTo(map);
    
    L.marker([6.4285, 100.4285], {icon: alertIcon}).addTo(map)
     .bindPopup('<b style="color:#ff4b2b;">Accident / Heavy Jam</b><br>Avoid C-Mart Junction.');

    // 2. AI SUGGESTED BYPASS ROUTE (Green, Dashed)
    // Diverting traffic from the North, going around the town center, and reconnecting South
    var bypassRoute = [ [6.4400, 100.4220], [6.4350, 100.4150], [6.4250, 100.4180], [6.4200, 100.4280], [6.4250, 100.4310] ];
    L.polyline(bypassRoute, { color: '#00ff87', weight: 5, opacity: 0.9, dashArray: '10, 10', lineJoin: 'round' }).addTo(map);
    
    L.marker([6.4350, 100.4150], {icon: bypassIcon}).addTo(map)
     .bindPopup('<b style="color:#00ff87;">Suggested Bypass</b><br>Route via Jalan Lama (Clear flow).');

</script>

</body>
</html>