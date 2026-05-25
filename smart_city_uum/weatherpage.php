<?php
require __DIR__ . '/data/dashboard-data.php';

$weeklyForecast = [
    ['day' => 'Tue', 'temp' => '29&deg;', 'status' => 'Cloudy', 'icon' => 'fa-cloud', 'color' => '#94a3b8'],
    ['day' => 'Wed', 'temp' => '27&deg;', 'status' => 'Rain', 'icon' => 'fa-cloud-showers-heavy', 'color' => '#4facfe'],
    ['day' => 'Thu', 'temp' => '28&deg;', 'status' => 'Storm', 'icon' => 'fa-bolt', 'color' => '#ff4b2b'],
    ['day' => 'Fri', 'temp' => '30&deg;', 'status' => 'Sunny', 'icon' => 'fa-sun', 'color' => '#fec163'],
    ['day' => 'Sat', 'temp' => '26&deg;', 'status' => 'Rain', 'icon' => 'fa-cloud-rain', 'color' => '#00f2fe'],
    ['day' => 'Sun', 'temp' => '29&deg;', 'status' => 'Partly', 'icon' => 'fa-cloud-sun', 'color' => '#00ff87'],
];

$nearbyDistricts = [
    [
        'city' => 'Jitra',
        'status' => 'Mostly Sunny',
        'temp' => '30&deg;',
        'icon' => 'fa-cloud-sun',
        'color' => '#fec163',
        'lat' => 6.2681,
        'lng' => 100.4212,
        'intensity' => 0.48,
        'zone' => 'Warm pocket',
        'summary' => 'Sunny spells are keeping the corridor warmer with only a low shower chance.',
    ],
    [
        'city' => 'Alor Setar',
        'status' => 'Cloudy',
        'temp' => '32&deg;',
        'icon' => 'fa-cloud',
        'color' => '#94a3b8',
        'lat' => 6.1248,
        'lng' => 100.3678,
        'intensity' => 0.40,
        'zone' => 'Stable cloud band',
        'summary' => 'Dense cloud cover is flattening temperatures while keeping rainfall scattered.',
    ],
    [
        'city' => 'Bukit Kayu Hitam',
        'status' => 'Heavy Rain',
        'temp' => '26&deg;',
        'icon' => 'fa-cloud-showers-heavy',
        'color' => '#4facfe',
        'lat' => 6.6553,
        'lng' => 100.4216,
        'intensity' => 0.92,
        'zone' => 'Flood watch core',
        'summary' => 'This hotspot is carrying the strongest rain cell and the highest flood risk nearby.',
    ],
    [
        'city' => 'Sintok (UUM)',
        'status' => 'Sunny',
        'temp' => '29&deg;',
        'icon' => 'fa-sun',
        'color' => '#fec163',
        'lat' => 6.4653,
        'lng' => 100.5054,
        'intensity' => 0.58,
        'zone' => 'Dry campus belt',
        'summary' => 'Brighter conditions are holding around the campus edge with brief cloud build-up.',
    ],
];

$floodRisk = 'Moderate';
$humidity = '82%';
$windSpeed = '14 km/h';
$rainChance = '75%';
$pressure = '1012 hPa';
$currentTemperature = explode(' ', (string) $weather['temperature'])[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Weather Center - Changlun Smart City</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

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
        --panel-dark: rgba(15, 23, 42, 0.85);
        --today-bg: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%);
        --today-text: #0f172a;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Outfit', sans-serif;
    }

    body {
        background-color: var(--bg-main);
        background-image: radial-gradient(circle at 15% 50%, rgba(79, 172, 254, 0.15), transparent 25%), radial-gradient(circle at 85% 30%, rgba(0, 242, 254, 0.15), transparent 25%);
        background-attachment: fixed;
        color: var(--text);
        height: 100vh;
        width: 100vw;
        overflow: hidden;
    }

    a {
        text-decoration: none;
    }

    .glass-panel {
        background: var(--card-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow);
        border-radius: 24px;
    }

    .dashboard {
        display: flex;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
    }

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

    .sidebar.collapsed {
        margin-left: -260px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 35px;
        flex-shrink: 0;
    }

    .brand-logo {
        width: 45px;
        height: 45px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 0 20px rgba(0, 242, 254, 0.4);
    }

    .brand h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        background: -webkit-linear-gradient(#fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .brand span {
        color: var(--primary);
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 6px;
        overflow-y: auto;
    }

    .sidebar-menu a {
        color: var(--muted);
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        font-size: 15px;
    }

    .sidebar-menu a.active {
        background: rgba(0, 242, 254, 0.1);
        color: var(--primary);
        border-left: 3px solid var(--primary);
    }

    .main {
        flex: 1;
        padding: 12px 18px 14px;
        display: flex;
        flex-direction: column;
        height: 100vh;
        min-width: 0;
        overflow: hidden;
        transition: padding 0.4s;
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-shrink: 0;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

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

    .menu-toggle-btn:hover {
        background: rgba(0, 242, 254, 0.2);
        color: var(--primary);
        border-color: var(--primary);
        box-shadow: 0 0 15px rgba(0, 242, 254, 0.3);
    }

    .date-box {
        padding: 10px 16px;
        font-size: 13px;
    }

    .weather-toggle {
        background: rgba(0, 0, 0, 0.4);
        border-radius: 20px;
        padding: 4px;
        display: flex;
        border: 1px solid var(--card-border);
    }

    .w-toggle-btn {
        padding: 6px 16px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        border: none;
        background: transparent;
        cursor: pointer;
        transition: 0.3s;
    }

    .w-toggle-btn.active {
        background: #e2e8f0;
        color: #0f172a;
    }

    .weather-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
        min-height: 0;
    }

    .weather-top-row {
        display: flex;
        gap: 12px;
        min-height: 224px;
        flex-shrink: 0;
    }

    .today-card {
        width: 222px;
        background: var(--today-bg);
        border-radius: 22px;
        padding: 18px;
        color: var(--today-text);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 30px rgba(161, 196, 253, 0.2);
        flex-shrink: 0;
    }

    .today-header {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        font-size: 13px;
    }

    .today-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
    }

    .today-temp {
        font-size: 50px;
        font-weight: 700;
        letter-spacing: -2px;
        line-height: 1;
    }

    .today-icon {
        font-size: 38px;
        color: #f59e0b;
        filter: drop-shadow(0 4px 6px rgba(245, 158, 11, 0.4));
    }

    .today-condition {
        font-size: 14px;
        font-weight: 600;
        margin-top: 5px;
    }

    .today-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        font-size: 10.5px;
        font-weight: 500;
        opacity: 0.8;
        margin-top: 12px;
    }

    .forecast-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        background: var(--panel-dark);
        border: 1px solid var(--card-border);
        border-radius: 22px;
        padding: 16px 18px 18px;
    }

    .section-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .forecast-scroll {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        overflow: hidden;
        padding-bottom: 4px;
        flex: 1;
    }

    .forecast-scroll::-webkit-scrollbar {
        height: 4px;
    }

    .forecast-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .forecast-pill {
        background: var(--panel-dark);
        border: 1px solid var(--card-border);
        border-radius: 32px;
        min-width: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-evenly;
        padding: 12px 6px;
        transition: 0.3s;
    }

    .forecast-pill:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-3px);
        border-color: var(--primary);
    }

    .f-day {
        font-size: 12px;
        font-weight: 500;
        color: var(--text);
    }

    .f-icon {
        font-size: 20px;
        margin: 8px 0;
    }

    .f-temp {
        font-size: 17px;
        font-weight: 600;
    }

    /* === NEW CHANCE OF RAIN CHART DESIGN === */
    .rain-chart-card {
        width: 250px;
        background: var(--panel-dark);
        border: 1px solid var(--card-border);
        border-radius: 22px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .rain-chart-card .section-title {
        margin: 0;
        font-size: 14px;
        color: var(--text);
    }

    .rain-chart-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        margin-top: 15px;
    }

    .y-axis-labels {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-bottom: 22px; /* Leave room for X-axis labels */
        color: var(--muted);
        font-size: 11px;
        font-weight: 500;
        padding-right: 12px;
        z-index: 3;
    }

    .chart-content {
        flex: 1;
        position: relative;
    }

    .guide-lines-container {
        position: absolute;
        top: 7px; /* Align with middle of first Y-label */
        bottom: 29px; /* Align with middle of last Y-label */
        left: 0;
        right: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 1;
    }

    .guide-line {
        border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
        width: 100%;
    }

    .bars-container {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        z-index: 2;
    }

    .bar-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        width: 100%;
    }

    .bar-track-bg {
        width: 6px;
        flex: 1;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 6px;
        display: flex;
        align-items: flex-end;
        margin-bottom: 8px; /* Space above X-axis label */
    }

    .bar-fill-value {
        width: 100%;
        background: #c2e9fb;
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(194, 233, 251, 0.6);
        transition: height 0.5s ease;
    }

    .x-label {
        font-size: 9px;
        color: var(--muted);
        font-weight: 600;
        height: 14px;
        line-height: 14px;
        white-space: nowrap;
    }

    /* === MAP & NEARBY SECTION === */
    .weather-bottom-row {
        display: flex;
        gap: 12px;
        flex: 1;
        min-height: 0;
    }

    .map-panel {
        flex: 1;
        background: var(--panel-dark);
        border-radius: 22px;
        border: 1px solid var(--card-border);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .map-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 16px 16px 10px;
        flex-wrap: wrap;
    }

    .map-status-pill {
        background: rgba(0, 0, 0, 0.36);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text);
    }

    .map-canvas {
        position: relative;
        flex: 1;
        min-height: 0;
        margin: 0 16px 16px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    #weatherMap {
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .map-detail-card {
        position: absolute;
        left: 16px;
        bottom: 16px;
        z-index: 1000;
        width: 252px;
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 14px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(10px);
    }

    .map-detail-kicker {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--primary);
    }

    .map-detail-card h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 6px 0 8px;
    }

    .map-detail-card p {
        margin: 0 0 12px;
        font-size: 12px;
        line-height: 1.5;
        color: var(--muted);
    }

    .map-detail-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .map-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 999px;
        background: rgba(79, 172, 254, 0.16);
        color: #dbeafe;
        font-size: 11px;
        font-weight: 600;
    }

    .map-temp-badge {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    .weather-popup {
        min-width: 190px;
    }

    .weather-popup h6 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 600;
    }

    .weather-popup p {
        margin: 0 0 10px;
        font-size: 11px;
        color: var(--muted);
        line-height: 1.45;
    }

    .weather-popup-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 11px;
    }

    .weather-popup-meta span {
        color: #cbd5e1;
    }

    .weather-popup-meta strong {
        font-size: 16px;
        color: var(--text);
    }

    .weather-hotspot-core {
        display: block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.92);
        background: #00f2fe;
        box-shadow: 0 0 0 7px rgba(0, 242, 254, 0.12), 0 0 18px rgba(0, 242, 254, 0.55);
    }

    .weather-hotspot.hotspot-sun .weather-hotspot-core {
        background: #fec163;
        box-shadow: 0 0 0 7px rgba(254, 193, 99, 0.14), 0 0 18px rgba(254, 193, 99, 0.6);
    }

    .weather-hotspot.hotspot-cloud .weather-hotspot-core {
        background: #94a3b8;
        box-shadow: 0 0 0 7px rgba(148, 163, 184, 0.14), 0 0 16px rgba(148, 163, 184, 0.45);
    }

    .weather-hotspot.hotspot-rain .weather-hotspot-core {
        background: #4facfe;
        box-shadow: 0 0 0 7px rgba(79, 172, 254, 0.15), 0 0 18px rgba(79, 172, 254, 0.65);
    }

    .weather-hotspot.hotspot-alert .weather-hotspot-core {
        background: #ff4b2b;
        box-shadow: 0 0 0 7px rgba(255, 75, 43, 0.16), 0 0 18px rgba(255, 75, 43, 0.7);
    }

    .leaflet-popup-content-wrapper,
    .leaflet-popup-tip {
        background: rgba(15, 23, 42, 0.96);
        color: var(--text);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .leaflet-popup-close-button span {
        color: var(--text);
    }

    .nearby-panel {
        width: 278px;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .nearby-hint {
        color: var(--muted);
        font-size: 11px;
        margin-bottom: 10px;
    }

    .nearby-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        overflow-y: auto;
        padding-right: 4px;
        flex: 1;
    }

    .nearby-list::-webkit-scrollbar {
        width: 4px;
    }

    .nearby-list::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .city-card {
        background: var(--panel-dark);
        border: 1px solid var(--card-border);
        border-radius: 18px;
        padding: 13px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: 0.3s;
        cursor: pointer;
    }

    .city-card:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(-5px);
        border-color: var(--primary);
    }

    .city-card.active {
        background: rgba(79, 172, 254, 0.12);
        border-color: rgba(0, 242, 254, 0.65);
        box-shadow: 0 0 0 1px rgba(0, 242, 254, 0.18);
        transform: translateX(-3px);
    }

    .city-info h5 {
        font-size: 13px;
        font-weight: 600;
        margin: 0 0 3px;
    }

    .city-info p {
        font-size: 10px;
        color: var(--muted);
        margin: 0;
    }

    .city-temp-block {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .city-icon {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .city-temp {
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
    }

    .popup-alert {
        position: fixed;
        top: 25px;
        right: 25px;
        width: 320px;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(255, 75, 43, 0.3);
        border-left: 5px solid var(--danger);
        border-radius: 16px;
        padding: 18px;
        box-shadow: var(--shadow);
        z-index: 9999;
        display: none;
        animation: slideIn 0.4s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .popup-alert h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .popup-alert p {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.4;
    }

    .close-popup {
        position: absolute;
        top: 12px;
        right: 15px;
        cursor: pointer;
        color: var(--muted);
    }

    @media (max-width: 1200px) {
        .weather-top-row { flex-wrap: wrap; height: auto; }
        .forecast-container { width: 100%; }
        .forecast-scroll { grid-template-columns: repeat(auto-fit, minmax(76px, 1fr)); }
        .rain-chart-card { width: 100%; height: 200px; }
        .weather-bottom-row { flex-direction: column; }
        .nearby-panel { width: 100%; height: auto; }
        .map-panel { min-height: 400px; }
        .map-detail-card { width: calc(100% - 32px); max-width: 320px; }
        body, .dashboard, .main { height: auto; overflow: auto; min-height: 100vh; }
    }

    @media (max-width: 900px) {
        .sidebar { position: fixed; margin-left: -260px; height: 100vh; }
        .sidebar.active-mobile { margin-left: 0; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5); }
        .weather-top-row { flex-direction: column; }
        .today-card { width: 100%; }
        .forecast-scroll { grid-template-columns: repeat(auto-fit, minmax(84px, 1fr)); }
        .map-panel-header { align-items: flex-start; }
        .map-status-pill { width: 100%; }
    }
</style>
</head>

<body>

<div class="popup-alert" id="weatherPopup">
    <span class="close-popup" onclick="document.getElementById('weatherPopup').style.display='none'"><i class="fa-solid fa-xmark"></i></span>
    <h6><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Weather Warning</h6>
    <p>Heavy rainfall expected tonight in Changlun area. Flash flood risk is currently marked as <strong><?php echo htmlspecialchars($floodRisk); ?></strong>.</p>
</div>

<div class="dashboard">

    <aside class="sidebar" id="appSidebar">
        <div class="brand">
            <div class="brand-logo"><i class="fa-solid fa-cloud-bolt"></i></div>
            <div>
                <h4>Changlun City</h4>
                <span>Command Center</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="index.php"><i class="fa-solid fa-border-all"></i> Main Dashboard</a>
            <a href="trafficpage.php"><i class="fa-solid fa-car-burst"></i> Traffic & Map</a>
            <a href="transportpage.php"><i class="fa-solid fa-bus-simple"></i> Transit</a>
            <a href="weatherpage.php" class="active"><i class="fa-solid fa-cloud-sun-rain"></i> Weather</a>
            <a href="alertspage.php"><i class="fa-solid fa-triangle-exclamation"></i> Alerts</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button id="menuToggleBtn" class="menu-toggle-btn glass-panel"><i class="fa-solid fa-bars"></i></button>
                <div class="weather-toggle">
                    <button class="w-toggle-btn active">Forecast</button>
                    <button class="w-toggle-btn">Air quality</button>
                </div>
            </div>
            
            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <div class="weather-grid">
            <div class="weather-top-row">
                
                <div class="today-card">
                    <div>
                        <div class="today-header">
                            <span>Today</span>
                            <span><?php echo date('h:i A'); ?></span>
                        </div>
                        <div class="today-main">
                            <div class="today-temp"><?php echo htmlspecialchars($currentTemperature); ?></div>
                            <i class="fa-solid fa-cloud-sun today-icon"></i>
                        </div>
                        <div class="today-condition"><?php echo htmlspecialchars($weather['condition']); ?></div>
                    </div>

                    <div class="today-details">
                        <div><i class="fa-solid fa-temperature-half me-1"></i> Real Feel: 33&deg;C</div>
                        <div><i class="fa-solid fa-wind me-1"></i> Wind: <?php echo htmlspecialchars($windSpeed); ?></div>
                        <div><i class="fa-solid fa-gauge me-1"></i> Press: <?php echo htmlspecialchars($pressure); ?></div>
                        <div><i class="fa-solid fa-droplet me-1"></i> Hum: <?php echo htmlspecialchars($humidity); ?></div>
                    </div>
                </div>

                <div class="forecast-container">
                    <div class="section-title">
                        <span>Next 7 days</span>
                    </div>
                    <div class="forecast-scroll">
                        <?php foreach ($weeklyForecast as $day): ?>
                            <div class="forecast-pill">
                                <span class="f-day"><?php echo htmlspecialchars($day['day']); ?></span>
                                <i class="fa-solid <?php echo htmlspecialchars($day['icon']); ?> f-icon" style="color: <?php echo htmlspecialchars($day['color']); ?>"></i>
                                <span class="f-temp"><?php echo $day['temp']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="rain-chart-card">
                    <h3 class="section-title">Chance of rain</h3>
                    
                    <div class="rain-chart-wrapper">
                        <div class="y-axis-labels">
                            <span>Rainy</span>
                            <span>Cloudy</span>
                            <span>Sunny</span>
                        </div>
                        
                        <div class="chart-content">
                            <div class="guide-lines-container">
                                <div class="guide-line"></div>
                                <div class="guide-line"></div>
                                <div class="guide-line"></div>
                            </div>
                            
                            <div class="bars-container">
                                <?php
                                $hours = ['10AM', '11AM', '12AM', '01PM', '02PM', '03PM'];
                                $heights = ['55%', '45%', '85%', '35%', '75%', '30%'];
                                for ($i = 0; $i < 6; $i++):
                                ?>
                                    <div class="bar-column">
                                        <div class="bar-track-bg">
                                            <div class="bar-fill-value" style="height: <?php echo htmlspecialchars($heights[$i]); ?>;"></div>
                                        </div>
                                        <span class="x-label"><?php echo htmlspecialchars($hours[$i]); ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="weather-bottom-row">
                <div class="map-panel">
                    <div class="map-panel-header">
                        <div class="section-title mb-0">
                            <span>Regional Radar</span>
                        </div>
                        <div class="map-status-pill"><i class="fa-solid fa-hand-pointer me-2"></i>Click any glowing zone for details</div>
                    </div>

                    <div class="map-canvas">
                        <div id="weatherMap"></div>

                        <div class="map-detail-card" id="mapDetailCard">
                            <span class="map-detail-kicker" id="mapDetailZone">Flood watch core</span>
                            <h4 id="mapDetailTitle">Bukit Kayu Hitam</h4>
                            <p id="mapDetailText">This hotspot is carrying the strongest rain cell and the highest flood risk nearby.</p>
                            <div class="map-detail-meta">
                                <span class="map-chip">
                                    <i id="mapDetailStatusIcon" class="fa-solid fa-cloud-showers-heavy"></i>
                                    <span id="mapDetailStatus">Heavy Rain</span>
                                </span>
                                <span class="map-temp-badge" id="mapDetailTemp">26&deg;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nearby-panel">
                    <div class="section-title mb-3">
                        <span>Nearby Districts</span>
                    </div>
                    
                    <div class="nearby-list">
                        <?php foreach ($nearbyDistricts as $city): ?>
                            <div
                                class="city-card"
                                tabindex="0"
                                role="button"
                                aria-pressed="false"
                                data-city="<?php echo htmlspecialchars($city['city']); ?>"
                            >
                                <div class="city-info">
                                    <h5><?php echo htmlspecialchars($city['city']); ?></h5>
                                    <p><?php echo htmlspecialchars($city['status']); ?></p>
                                </div>
                                <div class="city-temp-block">
                                    <i class="fa-solid <?php echo htmlspecialchars($city['icon']); ?> city-icon" style="color: <?php echo htmlspecialchars($city['color']); ?>"></i>
                                    <span class="city-temp"><?php echo $city['temp']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
    const menuToggleBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.getElementById('appSidebar');
    const mapDetailZone = document.getElementById('mapDetailZone');
    const mapDetailTitle = document.getElementById('mapDetailTitle');
    const mapDetailText = document.getElementById('mapDetailText');
    const mapDetailStatus = document.getElementById('mapDetailStatus');
    const mapDetailTemp = document.getElementById('mapDetailTemp');
    const mapDetailStatusIcon = document.getElementById('mapDetailStatusIcon');
    const cityCards = Array.from(document.querySelectorAll('.city-card'));

    const coreWeatherZone = {
        city: 'Changlun',
        status: 'Partly Cloudy',
        temp: '31&deg;',
        icon: 'fa-cloud-sun',
        color: '#fec163',
        lat: 6.4320,
        lng: 100.4285,
        intensity: 0.56,
        zone: 'Urban transition belt',
        summary: 'Changlun is sitting between clearer skies and the heavier rain band pushing in from the north.',
    };

    const districtWeather = <?php echo json_encode($nearbyDistricts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const weatherZones = [coreWeatherZone, ...districtWeather];
    const zoneLookup = new Map(weatherZones.map((zone) => [zone.city, zone]));
    const hotspotMarkers = new Map();

    const map = L.map('weatherMap', {
        zoomControl: false,
        attributionControl: true,
    }).setView([6.4320, 100.4285], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    menuToggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            sidebar.classList.toggle('collapsed');
        } else {
            sidebar.classList.toggle('active-mobile');
        }

        setTimeout(() => {
            map.invalidateSize();
        }, 400);
    });

    function hotspotClass(status) {
        const normalized = status.toLowerCase();
        if (normalized.includes('heavy') || normalized.includes('storm')) return 'hotspot-alert';
        if (normalized.includes('rain')) return 'hotspot-rain';
        if (normalized.includes('sun')) return 'hotspot-sun';
        if (normalized.includes('cloud')) return 'hotspot-cloud';
        return 'hotspot-rain';
    }

    function zoneIcon(zone) {
        return L.divIcon({
            className: `weather-hotspot ${hotspotClass(zone.status)}`,
            html: '<span class="weather-hotspot-core"></span>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });
    }

    function zonePopup(zone) {
        return `
            <div class="weather-popup">
                <h6>${zone.city}</h6>
                <p>${zone.summary}</p>
                <div class="weather-popup-meta">
                    <span>${zone.status}</span>
                    <strong>${zone.temp}</strong>
                </div>
            </div>
        `;
    }

    function updateDetailCard(zone) {
        mapDetailZone.textContent = zone.zone;
        mapDetailTitle.textContent = zone.city;
        mapDetailText.textContent = zone.summary;
        mapDetailStatus.textContent = zone.status;
        mapDetailTemp.innerHTML = zone.temp;
        mapDetailStatusIcon.className = `fa-solid ${zone.icon}`;
    }

    function setActiveCard(cityName) {
        cityCards.forEach((card) => {
            const isActive = card.dataset.city === cityName;
            card.classList.toggle('active', isActive);
            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function focusZone(cityName, shouldFly = true) {
        const zone = zoneLookup.get(cityName);
        if (!zone) return;

        updateDetailCard(zone);
        setActiveCard(zone.city);

        if (shouldFly) {
            map.flyTo([zone.lat, zone.lng], cityName === 'Alor Setar' ? 10.6 : 11.8, {
                duration: 0.7
            });
        }

        const marker = hotspotMarkers.get(cityName);
        if (marker) {
            marker.openPopup();
        }
    }

    const clusterOffsets = [
        [0, 0, 1],
        [0.016, 0.010, 0.74],
        [-0.012, 0.012, 0.62],
        [0.011, -0.015, 0.56],
        [-0.015, -0.011, 0.48]
    ];

    const heatPoints = weatherZones.flatMap((zone) =>
        clusterOffsets.map(([latOffset, lngOffset, weight]) => [
            zone.lat + latOffset,
            zone.lng + lngOffset,
            Math.min(1, zone.intensity * weight)
        ])
    );

    if (typeof L.heatLayer === 'function') {
        L.heatLayer(heatPoints, {
            radius: 36,
            blur: 30,
            maxZoom: 12,
            minOpacity: 0.4,
            gradient: {
                0.20: '#4facfe',
                0.42: '#00f2fe',
                0.65: '#fec163',
                0.82: '#ff8a00',
                1.00: '#ff4b2b'
            }
        }).addTo(map);
    } else {
        weatherZones.forEach((zone) => {
            L.circle([zone.lat, zone.lng], {
                radius: 1800 + (zone.intensity * 1600),
                color: zone.color,
                fillColor: zone.color,
                fillOpacity: 0.18,
                opacity: 0.28,
                weight: 1
            }).addTo(map);
        });
    }

    weatherZones.forEach((zone) => {
        const marker = L.marker([zone.lat, zone.lng], {
            icon: zoneIcon(zone)
        }).addTo(map).bindPopup(zonePopup(zone));

        marker.on('click', () => {
            updateDetailCard(zone);
            setActiveCard(zone.city);
        });

        hotspotMarkers.set(zone.city, marker);
    });

    const bounds = L.latLngBounds(weatherZones.map((zone) => [zone.lat, zone.lng]));
    map.fitBounds(bounds.pad(0.20), { animate: false });

    cityCards.forEach((card) => {
        const focusCardZone = () => focusZone(card.dataset.city);

        card.addEventListener('click', focusCardZone);
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                focusCardZone();
            }
        });
    });

    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('weatherPopup').style.display = 'block';
        }, 1000);

        setTimeout(() => {
            map.invalidateSize();
        }, 180);

        focusZone('Bukit Kayu Hitam', false);
    });

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });
</script>

</body>
</html>