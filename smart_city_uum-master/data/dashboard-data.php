<?php
$trafficSensors = [
    [
        'area' => 'Changlun Main Street (Pekan)',
        'status' => 'Busy',
        'flow' => 85,
        'speed' => '15 km/h',
        'delay' => '10 min',
        'note' => 'Heavy traffic near the C-Mart intersection.',
    ],
    [
        'area' => 'North-South Expressway Toll Exit',
        'status' => 'Smooth',
        'flow' => 40,
        'speed' => '80 km/h',
        'delay' => '0 min',
        'note' => 'Clear route heading towards Bukit Kayu Hitam.',
    ],
    [
        'area' => 'Jalan Sintok (Towards UUM)',
        'status' => 'Moderate',
        'flow' => 65,
        'speed' => '45 km/h',
        'delay' => '4 min',
        'note' => 'Steady stream of student and resident vehicles.',
    ],
];

$weather = [
    'condition' => 'Partly Cloudy',
    'temperature' => '31 °C',
    'humidity' => '72%',
    'rainChance' => '20%',
    'wind' => '12 km/h NW',
    'advice' => 'Good visibility for driving. Expect warmer temperatures in the afternoon.',
];

// UPDATED: Now contains exactly 4 CCTV feeds for a perfect 2x2 tile grid
$cctvFeeds = [
    [
        'camera' => 'CAM-CH01',
        'location' => 'C-Mart Junction',
        'status' => 'Live',
        'activity' => 'Heavy Traffic',
    ],
    [
        'camera' => 'CAM-CH02',
        'location' => 'Maybank Area',
        'status' => 'Live',
        'activity' => 'High parking occupancy',
    ],
    [
        'camera' => 'CAM-CH03',
        'location' => 'Bus Station',
        'status' => 'Live',
        'activity' => 'Intercity buses arriving',
    ],
    [
        'camera' => 'CAM-CH04',
        'location' => 'Sintok Highway (To UUM)',
        'status' => 'Live',
        'activity' => 'Smooth flow',
    ],
];

$transportRoutes = [
    [
        'route' => 'MyBas T14',
        'name' => 'Kangar - Changlun - Jitra',
        'arrival' => '5 min',
        'occupancy' => 'Medium',
        'progress' => 80,
    ],
];

$safetyAlerts = [
    ['title' => 'Pasar Malam Congestion', 'location' => 'Taman Teja area', 'level' => 'Caution', 'time' => '4:30 PM'],
    ['title' => 'Road Resurfacing', 'location' => 'Jalan Kodiang', 'level' => 'Notice', 'time' => '10:00 AM'],
];
?>

<?php
// ... keep your existing $trafficSensors and $weather data above ...

// UPDATED: Added 'video_url' to each feed with sample public traffic MP4s
$cctvFeeds = [
    [
        'camera' => 'CAM-CH01',
        'location' => 'C-Mart Junction',
        'status' => 'Live',
        'activity' => 'Heavy Traffic',
        'video_url' => 'https://static.videezy.com/system/resources/previews/000/004/235/original/freeway-traffic-at-night.mp4',
    ],
    [
        'camera' => 'CAM-CH02',
        'location' => 'Maybank Area',
        'status' => 'Live',
        'activity' => 'High parking occupancy',
        'video_url' => 'https://static.videezy.com/system/resources/previews/000/007/801/original/City_Traffic_4.mp4',
    ],
    [
        'camera' => 'CAM-CH03',
        'location' => 'Bus Station',
        'status' => 'Live',
        'activity' => 'Intercity buses arriving',
        'video_url' => 'https://static.videezy.com/system/resources/previews/000/008/174/original/Traffic_In_The_City_1.mp4',
    ],
    [
        'camera' => 'CAM-CH04',
        'location' => 'Sintok Highway (To UUM)',
        'status' => 'Live',
        'activity' => 'Smooth flow',
        'video_url' => 'https://static.videezy.com/system/resources/previews/000/004/244/original/night-traffic-time-lapse.mp4',
    ],
];

// ... keep your existing $transportRoutes and $safetyAlerts below ...
?>