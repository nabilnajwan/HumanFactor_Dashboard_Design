<?php
require __DIR__ . '/data/dashboard-data.php';

// Crime surveillance and alarm data for Changlun, Malaysia
$crimeAlerts = [
    [
        'id' => 'ALT-001',
        'type' => 'Robbery Alarm',
        'location' => 'Maybank ATM, Pekan Changlun',
        'status' => 'Critical',
        'time' => '00:47 AM',
        'description' => 'ATM tampering detected - motion sensor triggered',
        'lat' => 6.4320,
        'lng' => 100.4285,
    ],
    [
        'id' => 'ALT-002',
        'type' => 'Break-in Alert',
        'location' => '99 Speedmart, Jalan Sintok',
        'status' => 'Warning',
        'time' => '01:15 AM',
        'description' => 'Back door forced entry attempt detected',
        'lat' => 6.4350,
        'lng' => 100.4260,
    ],
    [
        'id' => 'ALT-003',
        'type' => 'Panic Button',
        'location' => 'Public Bank, C-Mart Complex',
        'status' => 'Critical',
        'time' => '11:32 PM',
        'description' => 'Teller panic button activated during transaction',
        'lat' => 6.4285,
        'lng' => 100.4310,
    ],
    [
        'id' => 'ALT-004',
        'type' => 'Suspicious Activity',
        'location' => 'Petronas Station, Changlun',
        'status' => 'Notice',
        'time' => '10:45 PM',
        'description' => 'Unattended vehicle near fuel pumps for 2+ hours',
        'lat' => 6.4400,
        'lng' => 100.4220,
    ],
    [
        'id' => 'ALT-005',
        'type' => 'Perimeter Breach',
        'location' => 'Hong Leong Bank, Taman Intan',
        'status' => 'Warning',
        'time' => '02:30 AM',
        'description' => 'Motion detected in restricted vault area',
        'lat' => 6.4250,
        'lng' => 100.4180,
    ],
];

$cctvSurveillance = [
    [
        'camera' => 'SEC-CAM01',
        'location' => 'Maybank ATM Lobby',
        'area' => 'Pekan Changlun',
        'status' => 'Recording',
        'event' => 'Motion Alert',
        'video_url' => 'assets/cctv1.mp4',
    ],
    [
        'camera' => 'SEC-CAM02',
        'location' => '99 Speedmart Entrance',
        'area' => 'Jalan Sintok',
        'status' => 'Recording',
        'event' => 'Door Sensor',
        'video_url' => 'assets/cctv2.mp4',
    ],
    [
        'camera' => 'SEC-CAM03',
        'location' => 'Public Bank Vault',
        'area' => 'C-Mart Complex',
        'status' => 'Recording',
        'event' => 'Panic Alert',
        'video_url' => 'assets/cctv3.mp4',
    ],
    [
        'camera' => 'SEC-CAM04',
        'location' => 'Petronas Forecourt',
        'area' => 'North Changlun',
        'status' => 'Recording',
        'event' => 'Loitering Detected',
        'video_url' => 'assets/cctv4.mp4',
    ],
];

$patrolUnits = [
    [
        'unit' => 'PV-01',
        'officers' => 'Sgt. Ahmad',
        'status' => 'Responding',
        'location' => 'En route to Maybank ATM',
        'eta' => '3 min',
    ],
    [
        'unit' => 'PV-02',
        'officers' => 'Kpl. Razak',
        'status' => 'Patrolling',
        'location' => 'Jalan Sintok area',
        'eta' => '-',
    ],
    [
        'unit' => 'PV-03',
        'officers' => 'Insp. Sarah',
        'status' => 'On Scene',
        'location' => 'Public Bank, C-Mart',
        'eta' => '0 min',
    ],
];

function alertBadgeClass(string $status): string {
    $normalized = strtolower($status);
    if (in_array($normalized, ['critical', 'emergency'])) return 'alert-badge critical';
    if (in_array($normalized, ['warning', 'active'])) return 'alert-badge warning';
    if (in_array($normalized, ['notice', 'monitoring'])) return 'alert-badge notice';
    return 'alert-badge normal';
}

function patrolStatusClass(string $status): string {
    $normalized = strtolower($status);
    if (in_array($normalized, ['responding', 'emergency'])) return 'patrol-chip responding';
    if (in_array($normalized, ['on scene', 'engaged'])) return 'patrol-chip onscene';
    if (in_array($normalized, ['patrolling', 'available'])) return 'patrol-chip patrolling';
    return 'patrol-chip offline';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Security Alerts - Changlun Smart City</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap/5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        --critical: #ff0040;
        --text: #f8fafc;
        --muted: #94a3b8;
        --shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

    body {
        background-color: var(--bg-main);
        background-image: radial-gradient(circle at 15% 50%, rgba(255, 75, 43, 0.08), transparent 25%), radial-gradient(circle at 85% 30%, rgba(0, 242, 254, 0.1), transparent 25%);
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
    .brand-logo { width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, var(--danger), var(--critical)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; box-shadow: 0 0 20px rgba(255, 75, 43, 0.4); }
    .brand h4 { margin: 0; font-size: 18px; font-weight: 700; background: -webkit-linear-gradient(#fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
    .brand span { color: var(--danger); font-size: 11px; letter-spacing: 1px; text-transform: uppercase;}
    .sidebar-menu { display: flex; flex-direction: column; gap: 6px; overflow-y: auto; }
    .sidebar-menu a { color: var(--muted); padding: 12px 16px; border-radius: 12px; transition: all 0.3s ease; display: flex; align-items: center; gap: 12px; font-weight: 500; font-size: 15px;}
    .sidebar-menu a.active { background: rgba(255, 75, 43, 0.1); color: var(--danger); border-left: 3px solid var(--danger); }
    .sidebar-menu a:hover { background: rgba(255, 255, 255, 0.05); color: var(--text); }

    /* MAIN CONTAINER */
    .main { flex: 1; padding: 15px 20px; display: flex; flex-direction: column; height: 100vh; min-width: 0; overflow: hidden; transition: padding 0.4s; }

    /* TOPBAR */
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-shrink: 0; }
    .topbar-left { display: flex; align-items: center; gap: 15px; }
    .menu-toggle-btn { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s ease; font-size: 18px; }
    .menu-toggle-btn:hover { background: rgba(255, 75, 43, 0.2); color: var(--danger); border-color: var(--danger); box-shadow: 0 0 15px rgba(255, 75, 43, 0.3); }
    .topbar h1 { font-size: 26px; font-weight: 700; margin-bottom: 2px; }
    .topbar p { color: var(--muted); margin:0; font-size: 13px;}

    /* ALERTS PAGE SPECIFIC LAYOUT */
    .alerts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        flex: 1;
        min-height: 0;
        margin-bottom: 15px;
    }

    /* ALERTS LIST PANEL */
    .alerts-panel {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .alerts-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--card-border);
        flex-shrink: 0;
    }
    .alerts-header h3 {
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alerts-list {
        overflow-y: auto;
        flex: 1;
        padding: 10px;
    }
    .alerts-list::-webkit-scrollbar { width: 6px; }
    .alerts-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .alert-card {
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .alert-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 14px 0 0 14px;
    }
    .alert-card.critical::before { background: var(--critical); box-shadow: 0 0 15px rgba(255, 0, 64, 0.5); }
    .alert-card.warning::before { background: var(--warning); box-shadow: 0 0 15px rgba(254, 193, 99, 0.5); }
    .alert-card.notice::before { background: var(--primary); box-shadow: 0 0 15px rgba(0, 242, 254, 0.5); }

    .alert-card:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(5px);
    }
    .alert-card.critical:hover { background: rgba(255, 0, 64, 0.08); }
    .alert-card.warning:hover { background: rgba(254, 193, 99, 0.08); }

    .alert-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .alert-type {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-time {
        font-size: 11px;
        color: var(--muted);
    }
    .alert-location {
        font-size: 13px;
        color: var(--primary);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .alert-desc {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.4;
    }
    .alert-id {
        font-size: 10px;
        color: rgba(255,255,255,0.3);
        margin-top: 8px;
    }

    /* Alert Badge */
    .alert-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .alert-badge.critical {
        background: rgba(255, 0, 64, 0.2);
        color: #ff4d79;
        border: 1px solid rgba(255, 0, 64, 0.4);
        animation: pulse-critical 2s infinite;
    }
    .alert-badge.warning {
        background: rgba(254, 193, 99, 0.2);
        color: var(--warning);
        border: 1px solid rgba(254, 193, 99, 0.4);
    }
    .alert-badge.notice {
        background: rgba(0, 242, 254, 0.15);
        color: var(--primary);
        border: 1px solid rgba(0, 242, 254, 0.3);
    }
    .alert-badge.normal {
        background: rgba(0, 255, 135, 0.15);
        color: var(--success);
        border: 1px solid rgba(0, 255, 135, 0.3);
    }

    @keyframes pulse-critical {
        0%, 100% { box-shadow: 0 0 5px rgba(255, 0, 64, 0.3); }
        50% { box-shadow: 0 0 20px rgba(255, 0, 64, 0.6); }
    }

    /* CCTV SURVEILLANCE PANEL */
    .cctv-surveillance {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .cctv-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--card-border);
        flex-shrink: 0;
    }
    .cctv-header h3 {
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cctv-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 10px;
        overflow-y: auto;
        flex: 1;
    }
    .cctv-grid::-webkit-scrollbar { width: 6px; }
    .cctv-grid::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .cctv-monitor {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    .cctv-monitor-screen {
        height: 120px;
        position: relative;
        background: #000;
    }
    .cctv-monitor video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(60%) contrast(130%) brightness(60%) sepia(10%) hue-rotate(180deg);
    }
    .cctv-monitor-screen::after {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 1px, transparent 3px);
        pointer-events: none;
    }
    .recording-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(255, 0, 0, 0.8);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .rec-dot {
        width: 6px;
        height: 6px;
        background: white;
        border-radius: 50%;
        animation: blink 1s infinite;
    }
    .area-label {
        position: absolute;
        bottom: 6px;
        left: 6px;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        color: #00ff87;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(0, 255, 135, 0.3);
        z-index: 2;
    }
    .cctv-info {
        padding: 10px;
    }
    .cctv-label {
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 2px;
    }
    .cctv-location {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }
    .cctv-event {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .cctv-event.active {
        background: rgba(255, 0, 64, 0.2);
        color: #ff4d79;
        border: 1px solid rgba(255, 0, 64, 0.3);
    }
    .cctv-event.normal {
        background: rgba(0, 255, 135, 0.1);
        color: var(--success);
        border: 1px solid rgba(0, 255, 135, 0.2);
    }

    /* BOTTOM SECTION - MAP & PATROL */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        flex-shrink: 0;
        height: 45%;
        min-height: 0;
    }

    /* MAP PANEL */
    .map-section {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .map-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--card-border);
        flex-shrink: 0;
    }
    .map-header h3 {
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .map-container {
        flex: 1;
        position: relative;
        overflow: hidden;
    }
    #alertMap {
        width: 100%;
        height: 100%;
    }
    .map-stats {
        position: absolute;
        bottom: 15px;
        left: 15px;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 12px;
        pointer-events: none;
    }
    .map-stats * {
        pointer-events: auto;
    }
    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .stat-item:last-child { margin-bottom: 0; }
    .stat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .stat-dot.critical { background: var(--critical); box-shadow: 0 0 8px rgba(255, 0, 64, 0.5); }
    .stat-dot.warning { background: var(--warning); box-shadow: 0 0 8px rgba(254, 193, 99, 0.5); }
    .stat-dot.patrol { background: var(--success); box-shadow: 0 0 8px rgba(0, 255, 135, 0.5); }

    /* Map Legend */
    .map-legend {
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 11px;
        pointer-events: none;
    }
    .map-legend * {
        pointer-events: auto;
    }
    .legend-title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        margin-bottom: 8px;
        font-weight: 600;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
    }
    .legend-item:last-child { margin-bottom: 0; }
    .legend-icon {
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .legend-diamond {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        transform: rotate(45deg);
    }
    .legend-diamond.responding {
        background: var(--success);
        box-shadow: 0 0 8px rgba(0, 255, 135, 0.5);
        animation: legendPulse 2s infinite;
    }
    .legend-diamond.patrolling {
        background: var(--primary);
        box-shadow: 0 0 6px rgba(0, 242, 254, 0.4);
    }
    .legend-diamond.onscene {
        background: var(--warning);
        box-shadow: 0 0 6px rgba(254, 193, 99, 0.4);
    }
    @keyframes legendPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* PATROL UNITS PANEL */
    .patrol-section {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .patrol-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--card-border);
        flex-shrink: 0;
    }
    .patrol-header h3 {
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .patrol-list {
        overflow-y: auto;
        flex: 1;
        padding: 10px;
    }
    .patrol-list::-webkit-scrollbar { width: 6px; }
    .patrol-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .patrol-card {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .patrol-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(0, 242, 254, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--primary);
        flex-shrink: 0;
    }
    .patrol-details {
        flex: 1;
    }
    .patrol-unit {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }
    .patrol-officers {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 4px;
    }
    .patrol-location {
        font-size: 12px;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .patrol-chip {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .patrol-chip.responding {
        background: rgba(255, 75, 43, 0.2);
        color: var(--danger);
        border: 1px solid rgba(255, 75, 43, 0.4);
    }
    .patrol-chip.onscene {
        background: rgba(255, 0, 64, 0.2);
        color: #ff4d79;
        border: 1px solid rgba(255, 0, 64, 0.4);
    }
    .patrol-chip.patrolling {
        background: rgba(0, 255, 135, 0.15);
        color: var(--success);
        border: 1px solid rgba(0, 255, 135, 0.3);
    }
    .patrol-chip.offline {
        background: rgba(255, 255, 255, 0.05);
        color: var(--muted);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive */
    @media(max-width: 1200px) {
        .alerts-grid { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; height: auto; }
        body, .dashboard, .main { height: auto; overflow: auto; min-height: 100vh; }
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
            <div class="brand-logo"><i class="fa-solid fa-shield-halved"></i></div>
            <div>
                <h4>Changlun City</h4>
                <span>Security Command</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="index.php"><i class="fa-solid fa-border-all"></i> Main Dashboard</a>
            <a href="trafficpage.php"><i class="fa-solid fa-car-burst"></i> Traffic & Map</a>
            <a href="transportpage.php"><i class="fa-solid fa-bus-simple"></i> Transit</a>
            <a href="weatherpage.php"><i class="fa-solid fa-cloud-sun"></i> Weather</a>
            <a href="alertspage.php" class="active"><i class="fa-solid fa-triangle-exclamation"></i> Alerts</a>
        </div>
    </aside>

    <main class="main">

        <div class="topbar">
            <div class="topbar-left">
                <button id="menuToggleBtn" class="menu-toggle-btn glass-panel">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1>Security Alerts</h1>
                    <p>Crime Surveillance & Alarm Monitoring</p>
                </div>
            </div>
            <div class="date-box glass-panel" style="padding: 10px 20px;">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
                <span class="mx-2 text-muted">|</span>
                <i class="fa-regular fa-clock me-2"></i> <?php echo date('H:i'); ?>
            </div>
        </div>

        <div class="alerts-grid">
            <!-- MAP SECTION -->
            <div class="map-section glass-panel">
                <div class="map-header">
                    <h3><i class="fa-solid fa-map-location-dot text-primary me-2"></i>Incident Map - Changlun</h3>
                </div>
                <div class="map-container">
                    <div id="alertMap"></div>
                    <div class="map-stats">
                        <div class="stat-item">
                            <span class="stat-dot critical"></span>
                            <span>Critical: <strong id="criticalCount">0</strong></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot warning"></span>
                            <span>Warnings: <strong id="warningCount">0</strong></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot patrol"></span>
                            <span>Patrols Active: <strong id="patrolCount">0</strong></span>
                        </div>
                    </div>
                    <div class="map-legend">
                        <div class="legend-title"><i class="fa-solid fa-car-side me-1"></i> Patrol Status</div>
                        <div class="legend-item">
                            <div class="legend-icon"><div class="legend-diamond responding"></div></div>
                            <span>Responding</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon"><div class="legend-diamond patrolling"></div></div>
                            <span>Patrolling</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon"><div class="legend-diamond onscene"></div></div>
                            <span>On Scene</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CCTV SURVEILLANCE -->
            <div class="cctv-surveillance glass-panel">
                <div class="cctv-header">
                    <h3><i class="fa-solid fa-video text-success me-2"></i>Live Surveillance</h3>
                </div>
                <div class="cctv-grid">
                    <?php foreach ($cctvSurveillance as $cam): ?>
                    <div class="cctv-monitor">
                        <div class="cctv-monitor-screen">
                            <video autoplay loop muted playsinline>
                                <source src="<?php echo htmlspecialchars($cam['video_url']); ?>" type="video/mp4">
                            </video>
                            <span class="recording-badge"><span class="rec-dot"></span>REC</span>
                            <span class="area-label"><?php echo htmlspecialchars($cam['area']); ?></span>
                        </div>
                        <div class="cctv-info">
                            <div class="cctv-label"><?php echo htmlspecialchars($cam['camera']); ?></div>
                            <div class="cctv-location"><?php echo htmlspecialchars($cam['location']); ?></div>
                            <span class="cctv-event <?php echo stripos($cam['event'], 'Alert') !== false || stripos($cam['event'], 'Motion') !== false ? 'active' : 'normal'; ?>">
                                <?php echo htmlspecialchars($cam['event']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="bottom-grid">
            <!-- ALERTS LIST -->
            <div class="alerts-panel glass-panel">
                <div class="alerts-header">
                    <h3><i class="fa-solid fa-list-ul text-danger me-2"></i>Active Alerts</h3>
                </div>
                <div class="alerts-list">
                    <?php foreach ($crimeAlerts as $alert): ?>
                    <div class="alert-card <?php echo strtolower($alert['status']); ?>" data-lat="<?php echo $alert['lat']; ?>" data-lng="<?php echo $alert['lng']; ?>">
                        <div class="alert-card-header">
                            <div class="alert-type">
                                <?php
                                $iconClass = 'fa-circle-exclamation';
                                if (stripos($alert['type'], 'robbery') !== false || stripos($alert['type'], 'break-in') !== false) $iconClass = 'fa-user-secret';
                                elseif (stripos($alert['type'], 'panic') !== false) $iconClass = 'fa-hand';
                                elseif (stripos($alert['type'], 'suspicious') !== false) $iconClass = 'fa-eye';
                                elseif (stripos($alert['type'], 'perimeter') !== false) $iconClass = 'fa-dungeon';
                                ?>
                                <i class="fa-solid <?php echo $iconClass; ?> text-<?php echo $alert['status'] === 'Critical' ? 'danger' : 'warning'; ?>"></i>
                                <?php echo htmlspecialchars($alert['type']); ?>
                            </div>
                            <span class="<?php echo alertBadgeClass($alert['status']); ?>">
                                <?php echo htmlspecialchars($alert['status']); ?>
                            </span>
                        </div>
                        <div class="alert-location">
                            <i class="fa-solid fa-location-dot"></i>
                            <?php echo htmlspecialchars($alert['location']); ?>
                        </div>
                        <div class="alert-desc">
                            <?php echo htmlspecialchars($alert['description']); ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="alert-time"><i class="fa-regular fa-clock me-1"></i> <?php echo htmlspecialchars($alert['time']); ?></span>
                            <span class="alert-id">#<?php echo htmlspecialchars($alert['id']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PATROL UNITS -->
            <div class="patrol-section glass-panel">
                <div class="patrol-header">
                    <h3><i class="fa-solid fa-car-side text-success me-2"></i>Patrol Units</h3>
                </div>
                <div class="patrol-list">
                    <?php foreach ($patrolUnits as $unit): ?>
                    <div class="patrol-card">
                        <div class="patrol-icon">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                        <div class="patrol-details">
                            <div class="patrol-unit"><?php echo htmlspecialchars($unit['unit']); ?></div>
                            <div class="patrol-officers"><i class="fa-solid fa-users me-1"></i> <?php echo htmlspecialchars($unit['officers']); ?></div>
                            <div class="patrol-location"><i class="fa-solid fa-location-arrow me-1"></i> <?php echo htmlspecialchars($unit['location']); ?></div>
                        </div>
                        <span class="patrol-chip <?php echo strtolower(str_replace(' ', '', $unit['status'])); ?>">
                            <?php echo htmlspecialchars($unit['status']); ?>
                            <?php if ($unit['eta'] !== '-'): ?>
                                <br><small style="font-size: 9px;">ETA: <?php echo htmlspecialchars($unit['eta']); ?></small>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
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

    // MAP INITIALIZATION - Focused on Changlun, Malaysia
    var map = L.map('alertMap', { zoomControl: false }).setView([6.4320, 100.4285], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Custom Icons
    var criticalIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #ff0040; width: 18px; height: 18px; border-radius: 50%; box-shadow: 0 0 20px 5px rgba(255,0,64, 0.7); border: 3px solid white; animation: pulse 1.5s infinite;"></div>',
        iconSize: [18, 18], iconAnchor: [9, 9]
    });

    var warningIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #fec163; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 15px 3px rgba(254,193,99, 0.5); border: 2px solid white;"></div>',
        iconSize: [14, 14], iconAnchor: [7, 7]
    });

    var noticeIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color: #00f2fe; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 10px 2px rgba(0,242,254, 0.5); border: 2px solid white;"></div>',
        iconSize: [12, 12], iconAnchor: [6, 6]
    });

    // Animated patrol icon with direction indicator
    function createPatrolIcon(unitId, isMoving) {
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="
                background-color: #00ff87; 
                width: 20px; 
                height: 20px; 
                border-radius: 4px; 
                transform: rotate(45deg); 
                box-shadow: 0 0 15px 3px rgba(0,255,135, 0.5); 
                border: 2px solid white;
                ${isMoving ? 'animation: patrolPulse 2s infinite;' : ''}
            "></div>
            <div style="
                position: absolute;
                top: -2px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 8px;
                font-weight: bold;
                color: #00ff87;
                text-shadow: 0 0 4px #000;
                background: rgba(0,0,0,0.7);
                padding: 1px 3px;
                border-radius: 3px;
            ">${unitId}</div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
    }

    // Add CSS animations
    var style = document.createElement('style');
    style.innerHTML = `
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 20px 5px rgba(255,0,64, 0.7); }
            50% { transform: scale(1.3); box-shadow: 0 0 30px 10px rgba(255,0,64, 0.4); }
            100% { transform: scale(1); box-shadow: 0 0 20px 5px rgba(255,0,64, 0.7); }
        }
        @keyframes patrolPulse {
            0%, 100% { box-shadow: 0 0 15px 3px rgba(0,255,135, 0.5); }
            50% { box-shadow: 0 0 25px 8px rgba(0,255,135, 0.8); }
        }
    `;
    document.head.appendChild(style);

    // Alert markers from PHP data
    var alertData = <?php echo json_encode($crimeAlerts); ?>;
    var criticalCount = 0, warningCount = 0;

    alertData.forEach(function(alert) {
        var icon;
        var status = alert.status.toLowerCase();
        if (status === 'critical') { icon = criticalIcon; criticalCount++; }
        else if (status === 'warning') { icon = warningIcon; warningCount++; }
        else { icon = noticeIcon; }

        L.marker([alert.lat, alert.lng], {icon: icon}).addTo(map)
            .bindPopup(`
                <div style="color: #333; min-width: 200px;">
                    <strong style="color: ${status === 'critical' ? '#ff0040' : '#fec163'};">${alert.type}</strong><br>
                    <span style="color: #666;">${alert.location}</span><br>
                    <small style="color: #999;">${alert.description}</small><br>
                    <span style="color: #999; font-size: 11px;">${alert.time}</span>
                </div>
            `);
    });

    // Update stats
    document.getElementById('criticalCount').textContent = criticalCount;
    document.getElementById('warningCount').textContent = warningCount;

    // ==========================================
    // PATROL UNIT ANIMATION SYSTEM
    // ==========================================
    
    // Define patrol routes (waypoints around Changlun)
    var patrolRoutes = {
        'PV-01': {
            waypoints: [
                [6.4330, 100.4290],
                [6.4350, 100.4310],
                [6.4380, 100.4280],
                [6.4360, 100.4250],
                [6.4330, 100.4270],
                [6.4330, 100.4290]
            ],
            speed: 0.00005,
            currentWaypoint: 0,
            status: 'responding'
        },
        'PV-02': {
            waypoints: [
                [6.4360, 100.4250],
                [6.4400, 100.4220],
                [6.4380, 100.4180],
                [6.4340, 100.4200],
                [6.4360, 100.4250]
            ],
            speed: 0.000035,
            currentWaypoint: 0,
            status: 'patrolling'
        },
        'PV-03': {
            waypoints: [
                [6.4290, 100.4315],
                [6.4285, 100.4310],
                [6.4300, 100.4330],
                [6.4280, 100.4350],
                [6.4290, 100.4315]
            ],
            speed: 0.00002,
            currentWaypoint: 0,
            status: 'onscene'
        }
    };

    // Create patrol markers with trails
    var patrolMarkers = {};
    var patrolTrails = {};

    Object.keys(patrolRoutes).forEach(function(unitId) {
        var route = patrolRoutes[unitId];
        var startPos = route.waypoints[0];
        
        var marker = L.marker(startPos, {
            icon: createPatrolIcon(unitId, route.status !== 'onscene')
        }).addTo(map);
        
        marker.bindPopup(`
            <div style="color: #333; min-width: 180px;">
                <strong style="color: #00ff87;">${unitId}</strong><br>
                <span style="color: #666; font-size: 12px;">Patrol Unit</span><br>
                <span style="color: #999; font-size: 11px;">Status: ${route.status.toUpperCase()}</span>
            </div>
        `);
        
        patrolMarkers[unitId] = marker;
        
        var trail = L.polyline([startPos], {
            color: '#00ff87',
            weight: 2,
            opacity: 0.3,
            dashArray: '5, 10'
        }).addTo(map);
        
        patrolTrails[unitId] = trail;
    });

    // Animation state
    var animationFrame;
    var lastUpdate = Date.now();

    function interpolate(start, end, t) {
        return [
            start[0] + (end[0] - start[0]) * t,
            start[1] + (end[1] - start[1]) * t
        ];
    }

    function distance(p1, p2) {
        return Math.sqrt(Math.pow(p2[0] - p1[0], 2) + Math.pow(p2[1] - p1[1], 2));
    }

    function animatePatrols() {
        var now = Date.now();
        var deltaTime = (now - lastUpdate) / 1000;
        lastUpdate = now;

        Object.keys(patrolRoutes).forEach(function(unitId) {
            var route = patrolRoutes[unitId];
            var marker = patrolMarkers[unitId];
            var trail = patrolTrails[unitId];
            
            if (route.status === 'onscene') {
                if (Math.random() < 0.01) {
                    var currentPos = marker.getLatLng();
                    var offset = [(Math.random() - 0.5) * 0.0001, (Math.random() - 0.5) * 0.0001];
                    marker.setLatLng([currentPos.lat + offset[0], currentPos.lng + offset[1]]);
                }
                return;
            }

            var currentPos = route.waypoints[route.currentWaypoint];
            var nextWaypoint = route.waypoints[(route.currentWaypoint + 1) % route.waypoints.length];
            var dist = distance(currentPos, nextWaypoint);

            if (dist < 0.00001) {
                route.currentWaypoint = (route.currentWaypoint + 1) % route.waypoints.length;
                return;
            }

            var moveAmount = route.speed * deltaTime * 60;
            var t = Math.min(moveAmount / dist, 1);

            var newPos = interpolate(currentPos, nextWaypoint, t);
            
            marker.setLatLng(newPos);
            
            var trailLatLngs = trail.getLatLngs();
            trailLatLngs.push(L.latLng(newPos[0], newPos[1]));
            
            if (trailLatLngs.length > 50) {
                trailLatLngs = trailLatLngs.slice(-50);
            }
            trail.setLatLngs(trailLatLngs);

            marker.setPopupOpacity(0);
        });

        animationFrame = requestAnimationFrame(animatePatrols);
    }

    animatePatrols();

    document.querySelectorAll('.alert-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var lat = parseFloat(this.dataset.lat);
            var lng = parseFloat(this.dataset.lng);
            map.setView([lat, lng], 16);
        });
    });

    document.getElementById('patrolCount').textContent = Object.keys(patrolMarkers).length;
</script>

</body>
</html>