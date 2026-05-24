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

$cctvFeeds = [
    [
        'camera' => 'CAM-CH01',
        'location' => 'C-Mart Junction',
        'status' => 'Live',
        'activity' => 'Heavy Traffic',
        'video_url' => 'assets/cctv1.mp4',
    ],
    [
        'camera' => 'CAM-CH02',
        'location' => 'Maybank Area',
        'status' => 'Live',
        'activity' => 'High parking occupancy',
        'video_url' => 'assets/cctv2.mp4',
    ],
    [
        'camera' => 'CAM-CH03',
        'location' => 'Bus Station',
        'status' => 'Live',
        'activity' => 'Intercity buses arriving',
        'video_url' => 'assets/cctv3.mp4',
    ],
    [
        'camera' => 'CAM-CH04',
        'location' => 'Sintok Highway (To UUM)',
        'status' => 'Live',
        'activity' => 'Smooth flow',
        'video_url' => 'assets/cctv4.mp4',
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
    [
        'route' => 'Shuttle UUM',
        'name' => 'Changlun Town - UUM Sintok',
        'arrival' => '12 min',
        'occupancy' => 'High',
        'progress' => 45,
    ],
    [
        'route' => 'Express',
        'name' => 'Changlun - Alor Setar',
        'arrival' => '25 min',
        'occupancy' => 'Low',
        'progress' => 15,
    ],
];

$safetyAlerts = [
    [
        'title' => 'Pasar Malam Congestion',
        'location' => 'Taman Teja area',
        'level' => 'Caution',
        'time' => '4:30 PM',
    ],
    [
        'title' => 'Road Resurfacing',
        'location' => 'Jalan Kodiang intersection',
        'level' => 'Notice',
        'time' => '10:00 AM',
    ],
    [
        'title' => 'Police Patrol Active',
        'location' => 'Pekan Changlun',
        'level' => 'Normal',
        'time' => '09:15 AM',
    ],
];

$quickActions = [
    'Report Pothole',
    'Check Bus Schedule',
    'Pay Parking',
    'Emergency Services',
];
?>