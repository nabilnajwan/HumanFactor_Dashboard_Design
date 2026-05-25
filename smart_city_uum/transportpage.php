<?php
// Enhanced mock data generation mirroring comprehensive dashboard-data.php rules
$transportRoutes = [
    [
        'route' => 'T23',
        'name' => 'MyBas Changlun Town Loop',
        'path' => 'Stesen Bas → C-Mart → Kolej Universiti → Stesen Bas',
        'scheduled' => '11:15 AM',
        'status' => 'Ongoing',
        'eta' => '11:15 AM',
        'passenger_load' => 'Medium',
        'load_color' => '#fec163',
        'color' => '#00ff87',
        'start_name' => 'Stesen Bas Changlun',
        'end_name' => 'Kolej Universiti Hub',
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
        'passenger_load' => 'High',
        'load_color' => '#ff4b2b',
        'color' => '#ff4b2b',
        'start_name' => 'Stesen Bas Changlun',
        'end_name' => 'UUM Welcome Center',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4600, 100.4215]
    ],
    [
        'route' => 'T24',
        'name' => 'Changlun Border Shuttle',
        'path' => 'Stesen Bas → Bukit Kayu Hitam ICQS',
        'scheduled' => '11:45 AM',
        'status' => 'Ongoing',
        'eta' => '11:45 AM',
        'passenger_load' => 'Low',
        'load_color' => '#00ff87',
        'color' => '#00f2fe',
        'start_name' => 'Stesen Bas Changlun',
        'end_name' => 'Bukit Kayu Hitam ICQS',
        'start_coord' => [6.4310, 100.4290],
        'end_coord' => [6.4480, 100.4210]
    ],
    [
        'route' => 'GB01',
        'name' => 'GITBUS Campus Shuttle',
        'path' => 'UUM Changlun Housing → Main Highway Corridor',
        'scheduled' => '11:50 AM',
        'status' => 'Ongoing',
        'eta' => '11:50 AM',
        'passenger_load' => 'High',
        'load_color' => '#ff4b2b',
        'color' => '#4facfe',
        'start_name' => 'UUM Changlun Housing',
        'end_name' => 'Main Highway Corridor Hub',
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
        'passenger_load' => 'Low',
        'load_color' => '#00ff87',
        'color' => '#fec163',
        'start_name' => 'Stesen Bas Changlun',
        'end_name' => 'Felda Laka Selatan',
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

// Analytics Mock History for Majlis Bandaraya Reporting
$analyticsHours = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
$loadDataHigh = [45, 88, 65, 35, 42, 70, 78, 40, 44, 62, 95, 50]; // Rush hour tracking
$loadDataMedium = [20, 45, 30, 25, 28, 40, 55, 30, 35, 48, 60, 30];

$routeDelayMinutes = [
    'T23' => 4,
    'K40' => 18,
    'T24' => 2,
    'GB01' => 5,
    'K42' => 0
];
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
        overflow-y: auto; 
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
    .topbar h1 { font-size: 24px; font-weight: 700; margin-bottom: 2px; }
    .topbar p { color: var(--muted); margin:0; font-size: 13px;}
    .date-box { padding: 8px 16px; color: var(--primary); font-weight: 600; font-size: 13px; letter-spacing: 1px; border-radius: 12px;}

    /* HORIZONTAL SLIM KPI ROW */
    .kpi-row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        flex-shrink: 0;
    }
    .kpi-box {
        flex: 1;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .kpi-box:hover { transform: translateY(-1px); }
    .kpi-box::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .kpi-ongoing::before { background: var(--success); }
    .kpi-delay::before { background: var(--danger); }
    .kpi-total::before { background: var(--primary); }

    .kpi-text-group { display: flex; flex-direction: column; }
    .kpi-value { font-size: 26px; font-weight: 700; line-height: 1.1; }
    .kpi-title { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
    .kpi-icon { font-size: 24px; opacity: 0.4; }

    /* PRIORITY 1: MUNICIPAL ANALYTICS ROW */
    .analytics-row {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 15px;
        margin-bottom: 15px;
        flex-shrink: 0;
    }
    .analytics-card {
        padding: 18px;
        display: flex;
        flex-direction: column;
    }
    .chart-wrapper {
        position: relative;
        height: 190px;
        width: 100%;
    }

    /* PRIORITY 2: SIDE-BY-SIDE SPLIT WORKSPACE */
    .transit-workspace {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr; 
        gap: 15px;
        flex: 1;
        min-height: 380px;
        margin-bottom: 10px;
    }

    /* COLUMN 1: SCHEDULE TABLE */
    .schedule-column {
        padding: 20px;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .panel-header-block {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding-bottom: 10px;
        margin-bottom: 10px;
        gap: 15px;
    }
    .panel-title { font-size: 14px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px;}
    
    /* FILTER CONTAINER BUTTONS */
    .filter-btn-group {
        display: flex;
        gap: 6px;
        background: rgba(15, 23, 42, 0.6);
        padding: 4px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .filter-btn {
        background: transparent;
        border: none;
        color: var(--muted);
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 7px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }
    .filter-btn:hover { color: var(--text); background: rgba(255,255,255,0.03); }
    .filter-btn.active { background: rgba(0, 242, 254, 0.15); color: var(--primary); box-shadow: inset 0 0 8px rgba(0,242,254,0.1); }

    .table-container { flex: 1; overflow-y: auto; min-height: 0; padding-right: 4px; }
    .table-container::-webkit-scrollbar { width: 5px; }
    .table-container::-webkit-scrollbar-track { background: transparent; }
    .table-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .custom-table th { font-size: 11px; text-transform: uppercase; color: var(--muted); padding: 0 12px 2px 12px; font-weight: 600; border: none; letter-spacing: 0.5px; }
    .custom-table tr { background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.02); transition: all 0.2s ease; cursor: pointer; }
    .custom-table tr:hover { background: rgba(255, 255, 255, 0.04); }
    .custom-table tr.selected-row { background: rgba(0, 242, 254, 0.08) !important; border: 1px solid rgba(0, 242, 254, 0.3) !important; }
    .custom-table td { padding: 12px; font-size: 13px; vertical-align: middle; border-top: 1px solid rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.03); }
    .custom-table td:first-child { border-left: 1px solid rgba(255,255,255,0.03); border-radius: 12px 0 0 12px; }
    .custom-table td:last-child { border-right: 1px solid rgba(255,255,255,0.03); border-radius: 0 12px 12px 0; }

    .route-badge { background: rgba(0, 242, 254, 0.08); padding: 4px 8px; border-radius: 8px; font-weight: 700; color: var(--primary); font-size: 11px; border: 1px solid rgba(0, 242, 254, 0.15); display: inline-block; min-width: 52px; text-align: center; }
    
    .status-pill { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; display: inline-block; text-transform: uppercase; }
    .status-pill.ongoing { background: rgba(0, 255, 135, 0.12); color: var(--success); border: 1px solid rgba(0, 255, 135, 0.25); }
    .status-pill.delayed { background: rgba(255, 75, 43, 0.12); color: var(--danger); border: 1px solid rgba(255, 75, 43, 0.25); }
    .status-pill.scheduled { background: rgba(254, 193, 99, 0.12); color: var(--warning); border: 1px solid rgba(254, 193, 99, 0.25); }

    .load-indicator { padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 5px; width: fit-content; }
    .load-dot { width: 6px; height: 6px; border-radius: 50%; }

    /* COLUMN 2: MAP CONTAINER */
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
        position: absolute; top: 12px; left: 12px;
        background: rgba(11, 15, 25, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15); padding: 8px 14px; border-radius: 10px;
        z-index: 1000; box-shadow: var(--shadow); display: flex; align-items: center; gap: 8px; pointer-events: none;
    }
    .map-overlay-title h6 { font-size: 11px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; margin: 0; }
    
    #transitMap { flex: 1; width: 100%; height: 100%; border-radius: 20px; z-index: 1; }

    .leaflet-routing-container { display: none !important; }

    .leaflet-popup-content-wrapper {
        background: rgba(11, 15, 25, 0.95) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #fff !important; font-family: 'Outfit', sans-serif !important; border-radius: 12px !important;
        box-shadow: var(--shadow) !important;
    }
    .leaflet-popup-content { margin: 12px; }
    .leaflet-popup-tip { background: rgba(11, 15, 25, 0.95) !important; border: 1px solid rgba(255, 255, 255, 0.12); }

    /* RESPONSIVENESS BREAKPOINTS */
    @media(max-width: 1200px) {
        .analytics-row { grid-template-columns: 1fr; }
        .transit-workspace { grid-template-columns: 1fr; height: auto; }
        body { overflow-y: auto; height: auto; }
        #transitMap { min-height: 400px; }
    }
    @media(max-width: 768px) {
        .kpi-row { flex-direction: column; gap: 10px; }
        .sidebar { position: fixed; margin-left: -260px; height: 100vh; }
        .sidebar.active-mobile { margin-left: 0; box-shadow: 10px 0 30px rgba(0,0,0,0.5); }
        .panel-header-block { flex-direction: column; align-items: flex-start; gap: 10px; }
        .filter-btn-group { width: 100%; justify-content: space-between; }
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
            <a href="trafficpage.php"><i class="fa-solid fa-car-burst"></i> Traffic</a>
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
                    <p>Live Fleet Telemetry & Municipal Resource Analytics</p>
                </div>
            </div>
            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <!-- KPI OVERVIEW -->
        <div class="kpi-row">
            <div class="kpi-box glass-panel kpi-ongoing">
                <div class="kpi-text-group">
                    <div class="kpi-value text-success"><?php echo $ongoingBuses; ?></div>
                    <div class="kpi-title">Buses Active</div>
                </div>
                <i class="fa-solid fa-circle-play kpi-icon text-success"></i>
            </div>
            <div class="kpi-box glass-panel kpi-delay">
                <div class="kpi-text-group">
                    <div class="kpi-value text-danger"><?php echo $delayedBuses; ?></div>
                    <div class="kpi-title">Incidents / Delays</div>
                </div>
                <i class="fa-solid fa-triangle-exclamation kpi-icon text-danger"></i>
            </div>
            <div class="kpi-box glass-panel kpi-total">
                <div class="kpi-text-group">
                    <div class="kpi-value text-info"><?php echo $totalBuses; ?></div>
                    <div class="kpi-title">Total Fleet</div>
                </div>
                <i class="fa-solid fa-bus kpi-icon text-info"></i>
            </div>
        </div>

        <!-- PRIORITY 1: MUNICIPAL ANALYTICS HUB -->
        <div class="analytics-row">
            <div class="analytics-card glass-panel">
                <div class="panel-title text-white mb-2">
                    <i class="fa-solid fa-chart-line text-primary"></i>Hourly Peak Passenger Load
                </div>
                <div class="chart-wrapper">
                    <canvas id="passengerLoadChart"></canvas>
                </div>
            </div>
            <div class="analytics-card glass-panel">
                <div class="panel-title text-white mb-2">
                    <i class="fa-solid fa-chart-bar text-danger"></i> Route Punctuality & Delay Metrics
                </div>
                <div class="chart-wrapper">
                    <canvas id="routePunctualityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PRIORITY 2: OPERATIONAL SPLIT WORKSPACE -->
        <div class="transit-workspace">
            
            <div class="schedule-column glass-panel">
                <div class="panel-header-block">
                    <div class="panel-title text-white">
                        <i class="fa-solid fa-table-list text-primary"></i> Live Regional Schedules
                    </div>
                    <div class="filter-btn-group">
                        <button class="filter-btn active" onclick="filterTransitTable('all')">All</button>
                        <button class="filter-btn" onclick="filterTransitTable('ongoing')">Ongoing</button>
                        <button class="filter-btn" onclick="filterTransitTable('delayed')">Delayed</button>
                        <button class="filter-btn" onclick="filterTransitTable('scheduled')">Scheduled</button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="custom-table" id="schedulesTable">
                        <thead>
                            <tr>
                                <th style="width: 12%">ID</th>
                                <th style="width: 48%">Transit Corridor Route</th>
                                <th style="width: 20%">Passenger Load</th>
                                <th style="width: 20%">Status / ETA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transportRoutes as $index => $route): 
                                $statusClass = strtolower($route['status']);
                                $textStatusColor = '';
                                if($statusClass === 'delayed') $textStatusColor = 'text-danger';
                                if($statusClass === 'ongoing') $textStatusColor = 'text-success';
                            ?>
                                <tr data-status="<?php echo $statusClass; ?>" onclick="focusRouteLine('<?php echo $route['route']; ?>', this)">
                                    <td><span class="route-badge" style="border-color: <?php echo $route['color']; ?>33; color: <?php echo $route['color']; ?>;"><?php echo htmlspecialchars($route['route']); ?></span></td>
                                    <td>
                                        <div class="text-white fw-semibold" style="font-size:13px;"><?php echo htmlspecialchars($route['name']); ?></div>
                                        <div class="text-white" style="font-size:11px; margin-top:2px;"><i class="fa-solid fa-angle-right me-1 text-primary"></i> <?php echo htmlspecialchars($route['path']); ?></div>
                                    </td>
                                    <td>
                                        <div class="load-indicator" style="background: <?php echo $route['load_color']; ?>15; color: <?php echo $route['load_color']; ?>;">
                                            <span class="load-dot" style="background-color: <?php echo $route['load_color']; ?>;"></span>
                                            <?php echo $route['passenger_load']; ?>
                                        </div>
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

<!-- Scripts Delivery -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    // Sidebar Workspace Collapsing Logic
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

    // --- CHART IMPLEMENTATIONS (CHART.JS) ---
    const ctxLoad = document.getElementById('passengerLoadChart').getContext('2d');
    new Chart(ctxLoad, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($analyticsHours); ?>,
            datasets: [{
                label: 'High Capacity Routes Load',
                data: <?php echo json_encode($loadDataHigh); ?>,
                borderColor: '#00f2fe',
                backgroundColor: 'rgba(0, 242, 254, 0.05)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true
            }, {
                label: 'Small Routes Load',
                data: <?php echo json_encode($loadDataMedium); ?>,
                borderColor: '#fec163',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 10 } } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 10 } } }
            }
        }
    });

    const ctxPunctual = document.getElementById('routePunctualityChart').getContext('2d');
    new Chart(ctxPunctual, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($routeDelayMinutes)); ?>,
            datasets: [{
                label: 'Accumulated Latency (Mins)',
                data: <?php echo json_encode(array_values($routeDelayMinutes)); ?>,
                backgroundColor: function(context) {
                    const val = context.raw;
                    return val > 10 ? 'rgba(255, 75, 43, 0.8)' : 'rgba(0, 255, 135, 0.8)';
                },
                borderRadius: 5,
                barThickness: 15
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 10 } } },
                y: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 11, weight: '600' } } }
            }
        }
    });

    // --- LEAFLET MAP & SIMULATION LOGIC ---
    const DEFAULT_CENTER = [6.4310, 100.4290];
    const DEFAULT_ZOOM = 13;

    var map = L.map('transitMap', { zoomControl: false }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap', subdomains: 'abcd', maxZoom: 19
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    function createStationIcon() {
        return L.divIcon({
            className: 'station-marker',
            html: `<div style="background: rgba(11, 23, 42, 0.9); border: 2px solid var(--primary); width: 14px; height: 14px; border-radius: 4px; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 10px #00f2fe;">
                    <div style="background: #00f2fe; width:6px; height:6px; border-radius:1px;"></div>
                   </div>`,
            iconSize: [14, 14], iconAnchor: [7, 7]
        });
    }

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

    const PHP_ROUTES = <?php echo json_encode($transportRoutes); ?>;
    let liveFleetTelemetry = [];
    let mapRouteRegistry = {}; 
    let activeHighlightLine = null; 
    let currentSelectedRouteId = null;

    PHP_ROUTES.forEach((routeData) => {
        if (routeData.start_coord) {
            L.marker(routeData.start_coord, { icon: createStationIcon() })
             .addTo(map)
             .bindPopup(`<div style="color:#fff; font-family:'Outfit'; font-size:12px;"><i class="fa-solid fa-building-flag text-info me-2"></i><b>Station Hub:</b> ${routeData.start_name || 'Origin Terminal'}</div>`);
        }
        if (routeData.end_coord) {
            L.marker(routeData.end_coord, { icon: createStationIcon() })
             .addTo(map)
             .bindPopup(`<div style="color:#fff; font-family:'Outfit'; font-size:12px;"><i class="fa-solid fa-flag-checkered text-warning me-2"></i><b>Station Hub:</b> ${routeData.end_name || 'Destination Hub'}</div>`);
        }

        if (routeData.status.toLowerCase() === 'scheduled') return;

        let routingControl = L.Routing.control({
            waypoints: [
                L.latLng(routeData.start_coord[0], routeData.start_coord[1]),
                L.latLng(routeData.end_coord[0], routeData.end_coord[1])
            ],
            router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1' }),
            lineOptions: {
                styles: [{ color: routeData.color, opacity: 0.35, weight: 3.5, dashArray: '5, 8' }]
            },
            addWaypoints: false, draggableWaypoints: false, fitSelectedRoutes: false
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            let routes = e.routes;
            let realRoadCoordinates = routes[0].coordinates;
            
            let vehicleMarker = L.marker(realRoadCoordinates[0], {
                icon: createBusMarker(routeData.color, routeData.route)
            }).addTo(map);

            vehicleMarker.bindPopup(`
                <div style="color: #fff; font-family:'Outfit'; padding:2px; min-width:140px;">
                    <strong style="color:${routeData.color}; font-size:14px;">Bus ${routeData.route}</strong><br>
                    <small style="color:#94a3b8;">${routeData.name}</small><br>
                    <hr style="border-top:1px solid rgba(255,255,255,0.1); margin:6px 0;">
                    <b>Passenger Load:</b> ${routeData.passenger_load}<br>
                    <b>Status:</b> ${routeData.status}<br>
                    <b>ETA:</b> ${routeData.eta}
                </div>
            `);

            let telemetryObject = {
                id: routeData.route,
                marker: vehicleMarker,
                color: routeData.color,
                coords: realRoadCoordinates,
                currentIndex: Math.floor(Math.random() * (realRoadCoordinates.length / 2)),
                speedFactor: routeData.status.toLowerCase() === 'delayed' ? 0.2 : 0.5,
                direction: 1
            };

            liveFleetTelemetry.push(telemetryObject);
            mapRouteRegistry[routeData.route] = telemetryObject;
        });
    });

    // TABLE INTERACTION & FILTERING ENGINE
    function filterTransitTable(statusCriterion) {
        const targetButtons = document.querySelectorAll('.filter-btn');
        targetButtons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        const rows = document.querySelectorAll('#schedulesTable tbody tr');
        rows.forEach(row => {
            let rowStatus = row.getAttribute('data-status');
            if (statusCriterion === 'all' || rowStatus === statusCriterion) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function focusRouteLine(routeId, elementRow) {
        if (currentSelectedRouteId === routeId) {
            elementRow.classList.remove('selected-row');
            if (activeHighlightLine) {
                map.removeLayer(activeHighlightLine);
                activeHighlightLine = null;
            }
            let currentBus = mapRouteRegistry[routeId];
            if (currentBus && currentBus.marker) {
                currentBus.marker.closePopup();
            }
            currentSelectedRouteId = null;
            map.setView(DEFAULT_CENTER, DEFAULT_ZOOM, { animate: true, duration: 0.6 });
            return;
        }

        document.querySelectorAll('#schedulesTable tbody tr').forEach(tr => tr.classList.remove('selected-row'));
        elementRow.classList.add('selected-row');

        if (activeHighlightLine) {
            map.removeLayer(activeHighlightLine);
            activeHighlightLine = null;
        }

        let targetBus = mapRouteRegistry[routeId];
        if (targetBus && targetBus.coords.length > 0) {
            currentSelectedRouteId = routeId;

            activeHighlightLine = L.polyline(targetBus.coords, {
                color: targetBus.color,
                weight: 6,
                opacity: 0.95
            }).addTo(map);
            
            activeHighlightLine.bringToFront();
            targetBus.marker.bringToFront();

            let currentLoc = targetBus.marker.getLatLng();
            map.setView(currentLoc, 14, { animate: true, duration: 0.5 });
            
            setTimeout(() => {
                if (currentSelectedRouteId === routeId) {
                    targetBus.marker.openPopup();
                }
            }, 350);
        }
    }

    // Dynamic Tracking Thread Core Loop
    setInterval(() => {
        liveFleetTelemetry.forEach((vehicle) => {
            vehicle.currentIndex += (vehicle.speedFactor * vehicle.direction);
            
            if (vehicle.currentIndex >= vehicle.coords.length - 1) {
                vehicle.currentIndex = vehicle.coords.length - 1;
                vehicle.direction = -1; 
            } else if (vehicle.currentIndex <= 0) {
                vehicle.currentIndex = 0;
                vehicle.direction = 1; 
            }

            let roundedIndex = Math.floor(vehicle.currentIndex);
            let nextNodePosition = vehicle.coords[roundedIndex];
            
            if (nextNodePosition) {
                vehicle.marker.setLatLng([nextNodePosition.lat, nextNodePosition.lng]);
            }
        });
    }, 100);
</script>

</body>
</html>