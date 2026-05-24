<?php
require __DIR__ . '/data/dashboard-data.php';

// Demo Weather Data
$weeklyForecast = [
    ['day' => 'Monday', 'temp' => '31°C', 'status' => 'Sunny', 'icon' => 'fa-sun', 'color' => '#fec163'],
    ['day' => 'Tuesday', 'temp' => '29°C', 'status' => 'Cloudy', 'icon' => 'fa-cloud', 'color' => '#94a3b8'],
    ['day' => 'Wednesday', 'temp' => '27°C', 'status' => 'Heavy Rain', 'icon' => 'fa-cloud-showers-heavy', 'color' => '#4facfe'],
    ['day' => 'Thursday', 'temp' => '28°C', 'status' => 'Thunderstorm', 'icon' => 'fa-bolt', 'color' => '#ff4b2b'],
    ['day' => 'Friday', 'temp' => '30°C', 'status' => 'Sunny', 'icon' => 'fa-sun', 'color' => '#fec163'],
    ['day' => 'Saturday', 'temp' => '26°C', 'status' => 'Flood Risk', 'icon' => 'fa-house-flood-water', 'color' => '#00f2fe'],
    ['day' => 'Sunday', 'temp' => '29°C', 'status' => 'Partly Cloudy', 'icon' => 'fa-cloud-sun', 'color' => '#00ff87'],
];

$floodRisk = "Moderate";
$humidity = "82%";
$windSpeed = "14 km/h";
$rainChance = "75%";
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
    --shadow: 0 8px 32px rgba(0,0,0,0.35);
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Outfit',sans-serif;
}

body{
    background-color: var(--bg-main);
    background-image:
        radial-gradient(circle at 20% 20%, rgba(79,172,254,0.15), transparent 25%),
        radial-gradient(circle at 80% 30%, rgba(0,242,254,0.12), transparent 25%);
    color: var(--text);
    min-height:100vh;
    overflow-x:hidden;
}

.dashboard{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background: rgba(11, 15, 25, 0.85);
    backdrop-filter: blur(14px);
    border-right:1px solid var(--card-border);
    padding:25px 20px;
    display:flex;
    flex-direction:column;
}

.brand{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:35px;
}

.brand-logo{
    width:45px;
    height:45px;
    border-radius:14px;
    background:linear-gradient(135deg,var(--secondary),var(--primary));
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    box-shadow:0 0 20px rgba(0,242,254,0.4);
}

.brand h4{
    margin:0;
    font-size:18px;
    font-weight:700;
}

.brand span{
    color:var(--primary);
    font-size:11px;
    text-transform:uppercase;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.sidebar-menu a{
    color:var(--muted);
    padding:12px 16px;
    border-radius:12px;
    transition:0.3s ease;
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:500;
    text-decoration:none;
}

.sidebar-menu a:hover,
.sidebar-menu a.active{
    background:rgba(0,242,254,0.1);
    color:var(--primary);
    border-left:3px solid var(--primary);
}

/* MAIN */
.main{
    flex:1;
    padding:20px;
}

.glass-panel{
    background: var(--card-glass);
    backdrop-filter: blur(12px);
    border:1px solid var(--card-border);
    border-radius:20px;
    box-shadow: var(--shadow);
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.topbar h1{
    font-size:28px;
    font-weight:700;
}

.topbar p{
    color:var(--muted);
    margin:0;
}

.date-box{
    padding:10px 18px;
    border-radius:12px;
    color:var(--primary);
    font-weight:600;
}

/* WEATHER HERO */
.weather-hero{
    padding:30px;
    margin-bottom:20px;
    position:relative;
    overflow:hidden;
}

.weather-hero::before{
    content:"";
    position:absolute;
    width:250px;
    height:250px;
    background:rgba(0,242,254,0.08);
    border-radius:50%;
    top:-100px;
    right:-80px;
}

.hero-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:relative;
    z-index:2;
}

.current-temp{
    font-size:72px;
    font-weight:700;
    line-height:1;
}

.weather-status{
    color:var(--muted);
    margin-top:8px;
    font-size:18px;
}

.weather-icon{
    font-size:90px;
    color:var(--warning);
    animation: float 3s ease-in-out infinite;
}

@keyframes float{
    0%,100%{ transform:translateY(0px); }
    50%{ transform:translateY(-10px); }
}

/* STATS */
.weather-stats{
    display:grid;
    grid-template-columns: repeat(4,1fr);
    gap:15px;
    margin-bottom:20px;
}

.weather-card{
    padding:20px;
    transition:0.3s ease;
}

.weather-card:hover{
    transform:translateY(-5px);
    border-color:var(--primary);
}

.weather-card i{
    font-size:28px;
    margin-bottom:12px;
}

.weather-card h3{
    font-size:24px;
    margin-bottom:5px;
}

.weather-card p{
    color:var(--muted);
    margin:0;
}

/* FORECAST */
.forecast-section{
    padding:20px;
}

.forecast-grid{
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(160px,1fr));
    gap:15px;
    margin-top:20px;
}

.forecast-card{
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:16px;
    padding:18px;
    text-align:center;
    transition:0.3s ease;
    cursor:pointer;
}

.forecast-card:hover{
    transform:translateY(-5px);
    border-color:var(--primary);
    background:rgba(0,242,254,0.08);
}

.forecast-card i{
    font-size:38px;
    margin:15px 0;
}

.forecast-card h5{
    margin-bottom:5px;
}

.forecast-card p{
    margin:0;
    color:var(--muted);
    font-size:14px;
}

/* ALERT */
.alert-box{
    margin-top:20px;
    padding:18px 20px;
    border-radius:16px;
    background:rgba(255,75,43,0.12);
    border:1px solid rgba(255,75,43,0.3);
    display:flex;
    align-items:center;
    gap:15px;
}

.alert-box i{
    font-size:30px;
    color:var(--danger);
}

.alert-box h5{
    margin:0;
}

.alert-box p{
    margin:0;
    color:#cbd5e1;
    font-size:14px;
}

/* POPUP */
.popup-alert{
    position:fixed;
    top:25px;
    right:25px;
    width:320px;
    background:rgba(15,23,42,0.95);
    border:1px solid rgba(255,75,43,0.3);
    border-left:5px solid var(--danger);
    border-radius:16px;
    padding:18px;
    box-shadow:var(--shadow);
    z-index:9999;
    display:none;
    animation:slideIn 0.4s ease;
}

@keyframes slideIn{
    from{
        opacity:0;
        transform:translateX(50px);
    }
    to{
        opacity:1;
        transform:translateX(0);
    }
}

.popup-alert h6{
    margin-bottom:5px;
}

.popup-alert p{
    margin:0;
    color:var(--muted);
    font-size:14px;
}

.close-popup{
    position:absolute;
    top:10px;
    right:12px;
    cursor:pointer;
    color:var(--muted);
}

/* MOBILE */
@media(max-width: 900px){
    .dashboard{
        flex-direction:column;
    }

    .sidebar{
        width:100%;
    }

    .weather-stats{
        grid-template-columns:1fr 1fr;
    }

    .hero-content{
        flex-direction:column;
        gap:20px;
        text-align:center;
    }

    .current-temp{
        font-size:56px;
    }
}
</style>
</head>

<body>

<div class="popup-alert" id="weatherPopup">
    <span class="close-popup" onclick="closePopup()">
        <i class="fa-solid fa-xmark"></i>
    </span>

    <h6>
        <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
        Weather Warning
    </h6>

    <p>
        Heavy rainfall expected tonight in Changlun area. Possible flash flood risk near low-level roads.
    </p>
</div>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-logo">
                <i class="fa-solid fa-cloud-bolt"></i>
            </div>

            <div>
                <h4>Changlun City</h4>
                <span>Weather Center</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="index.php">
                <i class="fa-solid fa-border-all"></i>
                Main Dashboard
            </a>

            <a href="trafficpage.php">
                <i class="fa-solid fa-car-burst"></i>
                Traffic & Map
            </a>

            <a href="transportpage.php">
                <i class="fa-solid fa-bus-simple"></i>
                Transit
            </a>

            <a href="weatherpage.php" class="active">
                <i class="fa-solid fa-cloud-sun-rain"></i>
                Weather
            </a>

            <a href="alertspage.php">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Alerts
            </a>
        </div>

    </aside>

    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">

            <div>
                <h1>Weather Intelligence</h1>
                <p>Live climate monitoring and prediction</p>
            </div>

            <div class="date-box glass-panel">
                <i class="fa-regular fa-calendar me-2"></i>
                <?php echo date('D, d M Y'); ?>
            </div>

        </div>

        <!-- HERO -->
        <div class="weather-hero glass-panel">

            <div class="hero-content">

                <div>
                    <div class="current-temp">
                        <?php echo htmlspecialchars($weather['temperature']); ?>
                    </div>

                    <div class="weather-status">
                        <?php echo htmlspecialchars($weather['condition']); ?>
                    </div>
                </div>

                <div class="weather-icon">
                    <i class="fa-solid fa-cloud-sun-rain"></i>
                </div>

            </div>

        </div>

        <!-- STATS -->
        <div class="weather-stats">

            <div class="weather-card glass-panel">
                <i class="fa-solid fa-droplet text-info"></i>
                <h3><?php echo $humidity; ?></h3>
                <p>Humidity</p>
            </div>

            <div class="weather-card glass-panel">
                <i class="fa-solid fa-wind text-primary"></i>
                <h3><?php echo $windSpeed; ?></h3>
                <p>Wind Speed</p>
            </div>

            <div class="weather-card glass-panel">
                <i class="fa-solid fa-cloud-rain text-warning"></i>
                <h3><?php echo $rainChance; ?></h3>
                <p>Rain Probability</p>
            </div>

            <div class="weather-card glass-panel">
                <i class="fa-solid fa-house-flood-water text-danger"></i>
                <h3><?php echo $floodRisk; ?></h3>
                <p>Flood Risk</p>
            </div>

        </div>

        <!-- FORECAST -->
        <div class="forecast-section glass-panel">

            <h4>
                <i class="fa-solid fa-calendar-week text-info me-2"></i>
                Weekly Forecast
            </h4>

            <div class="forecast-grid">

                <?php foreach($weeklyForecast as $forecast): ?>

                    <div class="forecast-card"
                         onclick="showForecastAlert('<?php echo $forecast['day']; ?>', '<?php echo $forecast['status']; ?>')">

                        <h5><?php echo $forecast['day']; ?></h5>

                        <i class="fa-solid <?php echo $forecast['icon']; ?>"
                           style="color: <?php echo $forecast['color']; ?>"></i>

                        <h4><?php echo $forecast['temp']; ?></h4>

                        <p><?php echo $forecast['status']; ?></p>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- ALERT -->
            <div class="alert-box">
                <i class="fa-solid fa-triangle-exclamation"></i>

                <div>
                    <h5>Heavy Rainfall Advisory</h5>
                    <p>
                        Water level at Changlun river area increasing.
                        Citizens are advised to avoid flood-prone roads after 9PM.
                    </p>
                </div>
            </div>

        </div>

    </main>

</div>

<script>
// SHOW POPUP AFTER PAGE LOAD
window.onload = function() {
    setTimeout(() => {
        document.getElementById('weatherPopup').style.display = 'block';
    }, 1200);
};

// CLOSE POPUP
function closePopup() {
    document.getElementById('weatherPopup').style.display = 'none';
}

// FORECAST CLICK FUNCTION
function showForecastAlert(day, status) {

    let message = day + " forecast: " + status;

    if(status === "Flood Risk") {
        message += "\\n\\nWarning: High probability of flash flood.";
    }

    if(status === "Thunderstorm") {
        message += "\\n\\nAdvisory: Avoid outdoor activities.";
    }

    alert(message);
}
</script>

</body>
</html>